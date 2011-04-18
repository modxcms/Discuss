<?php
/**
 * A representation of a forum Board. Uses closure tables to maintain depth and
 * proper ordering while getting O(1) query performance.
 *
 * @package discuss
 */
class disBoard extends xPDOSimpleObject {
    /**
     *  @var boolean $parentChanged Monitors whether parent has been changed.
     *  @access protected
     */
    protected $parentChanged = false;

    /**
     * Overrides xPDOObject::set to provide custom functionality and automation
     * for the closure tables that persist the board map.
     *
     * {@inheritDoc}
     */
    public function set($k, $v= null, $vType= '') {
        $oldParentId = $this->get('parent');
        $set = parent::set($k,$v,$vType);
        if ($set && $k == 'parent' && $v != $oldParentId && !$this->isNew()) {
            $this->parentChanged = true;
        }
        return $set;
    }

    public function remove(array $ancestors = array()) {
        $removed = parent::remove($ancestors);
        $this->clearCache();
        return $removed;
    }

    /**
     * Overrides the xPDOObject::save method to provide custom functionality and
     * automation for the closure tables that persist the board map.
     *
     * {@inheritDoc}
     */
    public function save($cacheFlag = null) {
        $new = $this->isNew();
        $saved = parent::save($cacheFlag);

        /* if a new board */
        if ($saved && $new) {
            $id = $this->get('id');
            $parent = $this->get('parent');

            /* create self closure */
            $cl = $this->xpdo->newObject('disBoardClosure');
            $cl->set('ancestor',$id);
            $cl->set('descendant',$id);
            if ($cl->save() === false) {
                $this->remove();
                return false;
            }

            /* create closures and calculate rank */
            $tableName = $this->xpdo->getTableName('disBoardClosure');
            $c = $this->xpdo->newQuery('disBoardClosure');
            $c->where(array(
                'descendant' => $parent,
            ));
            $c->sortby('depth','DESC');
            $gparents = $this->xpdo->getCollection('disBoardClosure',$c);
            $cgps = count($gparents);
            $i = $cgps - 1;
            $gps = array();
            foreach ($gparents as $gparent) {
                $depth = 0;
                $ancestor = $gparent->get('ancestor');
                if ($ancestor != 0) $depth = $i;
                $obj = $this->xpdo->newObject('disBoardClosure');
                $obj->set('ancestor',$ancestor);
                $obj->set('descendant',$id);
                $obj->set('depth',$depth);
                $obj->save();
                $i--;
                $gps[] = $ancestor;
            }

            /* handle 0 ancestor closure */
            $rootcl = $this->xpdo->getObject('disBoardClosure',array(
                'ancestor' => 0,
                'descendant' => $id,
            ));
            if (!$rootcl) {
                $rootcl = $this->xpdo->newObject('disBoardClosure');
                $rootcl->set('ancestor',0);
                $rootcl->set('descendant',$id);
                $rootcl->set('depth',0);
                $rootcl->save();
            }
            /* set map (allows for 1-query grabbing of all boards while keeping
             * proper sort ordering) */
            $gps[] = $id;
            $map = implode('-',$gps);
            $this->set('map',$map);

            /* set rank to number of boards already with this parent */
            if (!defined('DISCUSS_IMPORT_MODE')) {
                $rank = $this->xpdo->getCount('disBoard',array('parent'=>$this->get('parent')));
                $this->set('rank',$rank);
            }
            $saved = parent::save();
        }
        /* if parent changed on existing object, rebuild closure table */
        if (!$new && $this->parentChanged) {
            /* first remove old tree path */
            $this->xpdo->removeCollection('disBoardClosure',array(
                'descendant' => $this->get('id'),
                'ancestor:!=' => $this->get('id'),
            ));

            /* now create new tree path from new parent */
            $newParentId = $this->get('parent');
            $c = $this->xpdo->newQuery('disBoardClosure');
            $c->where(array(
                'descendant' => $newParentId,
            ));
            $c->sortby('depth','DESC');
            $ancestors= $this->xpdo->getCollection('disBoardClosure',$c);
            $grandParents = array();
            foreach ($ancestors as $ancestor) {
                $depth = $ancestor->get('depth');
                $grandParentId = $ancestor->get('ancestor');
                /* if already has a depth, inc by 1 */
                if ($depth > 0) $depth++;
                /* if is the new parent node, set depth to 1 */
                if ($grandParentId == $newParentId && $newParentId != 0) { $depth = 1; }
                if ($grandParentId != 0) {
                    $grandParents[] = $grandParentId;
                }

                $cl = $this->xpdo->newObject('disBoardClosure');
                $cl->set('ancestor',$ancestor->get('ancestor'));
                $cl->set('descendant',$this->get('id'));
                $cl->set('depth',$depth);
                $cl->save();
            }
            /* if parent is root, make sure to set the root closure */
            if ($newParentId == 0) {
                $cl = $this->xpdo->newObject('disBoardClosure');
                $cl->set('ancestor',0);
                $cl->set('descendant',$this->get('id'));
                $cl->save();
            }

            /* set map */
            $grandParents[] = $this->get('id');
            $map = implode('-',$grandParents);
            $this->set('map',$map);
            $saved = parent::save();

            $this->clearCache();
        }

        return $saved;
    }

    /**
     * Grab the viewing text for the board.
     *
     * @access public
     * @return string The text returned for the viewing users.
     */
    public function getViewing() {
        if (!$this->xpdo->getOption('discuss.show_whos_online',null,true)) return '';

        $c = $this->xpdo->newQuery('disSession');
        $c->innerJoin('disUser','User');
        $c->select($this->xpdo->getSelectColumns('disSession','disSession','',array('id')));
        $c->select(array(
            'GROUP_CONCAT(CONCAT_WS(":",User.id,User.username) SEPARATOR ",") AS readers',
        ));
        $c->where(array(
            'disSession.place' => 'board:'.$this->get('id'),
        ));
        $c->groupby('disSession.user');
        $members = $this->xpdo->getObject('disSession',$c);
        if ($members) {
            $readers = explode(',',$members->get('readers'));
            $members = array();
            foreach ($readers as $reader) {
                $r = explode(':',$reader);
                $members[] = '<a href="[[~[[*id]]]]user?user='.$r[0].'">'.$r[1].'</a>';
            }
            $members = array_unique($members);
            $members = implode(',',$members);
        } else { $members = '0 members'; }

        $c = $this->xpdo->newQuery('disSession');
        $c->where(array(
            'place' => 'board:'.$this->get('id'),
            'user' => 0,
        ));
        $guests = $this->xpdo->getCount('disSession',$c);

        return $this->xpdo->lexicon('discuss.board_viewing',array('members' => $members,'guests' => $guests));
    }


    /**
     * Grab the moderators text for the board.
     *
     * @access public
     * @return string The text returned for the viewing users.
     */
    public function getModerators() {
        $c = $this->xpdo->newQuery('disModerator');
        $c->innerJoin('disUser','User');
        $c->select($this->xpdo->getSelectColumns('disModerator','disModerator','',array('id','user')));
        $c->select(array(
            'User.username',
        ));
        $c->where(array(
            'disModerator.board' => $this->get('id'),
        ));
        $moderators = $this->xpdo->getCollection('disModerator',$c);
        $mods = array();
        if ($moderators) {
            foreach ($moderators as $moderator) {
                $mods[] = '<a href="'.$this->xpdo->discuss->url.'user?user='.$moderator->get('user').'">'.$moderator->get('username').'</a>';
            }
            $mods = array_unique($mods);
            $mods = implode(',',$mods);
        }

        return !empty($mods) ? $this->xpdo->lexicon('discuss.board_moderators',array('moderators' => $mods)) : '';
    }

    /**
     * Grabs a comma-separated list of direct subboards for this board.
     *
     * @access public
     * @return string A CSV list of subboards
     */
    public function getSubBoardList() {
        $subboards = $this->get('subboards');
        $sbl = '';
        if (!empty($subboards)) {
            $subboards = explode(',',$subboards);
            $sbs = array();
            foreach ($subboards as $subboard) {
                $sb = explode(':',$subboard);
                $sbs[] = '<a href="[[~[[*id]]]]board/?board='.$sb[0].'">'.$sb[1].'</a>';
            }
            $sbl = $this->xpdo->lexicon('discuss.subforums').': '.implode(',',$sbs);
        }
        $this->set('subforums',$sbl);
        return $sbl;
    }

    public function buildBreadcrumbs($additional = array(),$linkToSelf = false) {
        $cacheKey = 'discuss/board/'.$this->get('id').'/breadcrumbs';
        $trail = $this->xpdo->cacheManager->get($cacheKey);
        if (empty($trail) || true) {
            /* get board breadcrumb trail */
            $c = $this->xpdo->newQuery('disBoard');
            $c->innerJoin('disBoardClosure','Ancestors');
            $c->where(array(
                'Ancestors.descendant' => $this->get('id'),
                'Ancestors.ancestor:!=' => $this->get('id'),
            ));
            $c->sortby($this->xpdo->getSelectColumns('disBoardClosure','Ancestors','',array('depth')),'DESC');
            $ancestors = $this->xpdo->getCollection('disBoard',$c);

            /* breadcrumbs */
            $trail = array();
            $trail[] = array(
                'url' => $this->xpdo->discuss->url,
                'text' => $this->xpdo->getOption('discuss.forum_title'),
            );
            $category = $this->getOne('Category');
            if ($category) {
                $trail[] = array(
                    'url' => $this->xpdo->discuss->url.'?category='.$category->get('id'),
                    'text' => $category->get('name'),
                );
            }
            if (!empty($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $trail[] = array(
                        'url' => $this->xpdo->discuss->url.'board?board='.$ancestor->get('id'),
                        'text' => $ancestor->get('name'),
                    );
                }
            }
            $self = array(
                'text' => $this->get('name'),
            );
            if ($linkToSelf) {
                $self['url'] = $this->xpdo->discuss->url.'board/?board='.$this->get('id');
            }
            if (empty($additional)) { $self['active'] = true; }
            $trail[] = $self;
            if (!empty($additional)) {
                foreach ($additional as $ad) {
                    $trail[] = $ad;
                }
            }
            $trail = $this->xpdo->discuss->hooks->load('breadcrumbs',array(
                'items' => &$trail,
            ));
        }
        $this->set('trail',$trail);
        return $trail;
    }

    /**
     * Mark all threads in this board as read for a user.
     * 
     * @param  $userId
     * @return bool
     */
    public function read($userId) {
        $stmt = $this->xpdo->query('SELECT id FROM '.$this->xpdo->getTableName('disThread').' WHERE board = '.$this->get('id').' ORDER BY id DESC');
        if (!$stmt) return false;

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $read = $this->xpdo->getCount('disThreadRead',array(
                'thread' => $row['id'],
                'user' => $userId,
            ));
            if ($read == 0) {
                $read = $this->xpdo->newObject('disThreadRead');
                $read->fromArray(array(
                    'thread' => $row['id'],
                    'board' => $this->get('id'),
                    'user' => $userId,
                ));
                $read->save();
            }
        }
        $this->clearCache();
        return true;
    }

    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/board/'.$this->get('id'));
        }
    }
}
<?php
/**
 * A representation of a forum Board. Uses closure tables to maintain depth and
 * proper ordering while getting O(1) query performance.
 *
 * @package discuss
 */
class disBoard extends xPDOSimpleObject {
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_ARCHIVED = 2;
    
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

    public static function fetch(xPDO &$modx,$id) {
        $c = $modx->newQuery('disBoard');
        $c->leftJoin('disBoardUserGroup','UserGroups');
        $c->where(array(
            'id' => $id,
        ));
        $groups = $modx->discuss->user->getUserGroups();
        if (!empty($groups) && !$modx->discuss->user->isAdmin) {
            /* restrict boards by user group if applicable */
            $g = array(
                'UserGroups.usergroup:IN' => $groups,
            );
            $g['OR:UserGroups.usergroup:='] = null;
            $where[] = $g;
            $c->andCondition($where,null,2);
        }
        return $modx->getObject('disBoard',$c);
    }

    /**
     * Grab the viewing text for the board.
     *
     * @access public
     * @return string The text returned for the viewing users.
     */
    public function getViewing() {
        if (!$this->xpdo->getOption('discuss.show_whos_online',null,true)) return '';
        if (!$this->xpdo->hasPermission('discuss.view_online')) return '';
        $canViewProfiles = $this->xpdo->hasPermission('discuss.view_profiles');

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
                $members[] = $canViewProfiles ? '<a href="'.$this->xpdo->discuss->url.'user?user='.$r[0].'">'.$r[1].'</a>' : $r[1];
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
     * Determine if a user is a moderator of this board
     * 
     * @param int $userId
     * @return bool
     */
    public function isModerator($userId) {
        if ($this->xpdo->discuss->user->isGlobalModerator()) return true;
         
        $moderator = $this->xpdo->getCount('disModerator',array(
            'user' => $userId,
            'board' => $this->get('id'),
        ));
        return $moderator > 0;
    }

    /**
     * Grab the moderators text for the board.
     *
     * @access public
     * @return string The text returned for the viewing users.
     */
    public function getModeratorsList() {
        $canViewProfiles = $this->xpdo->hasPermission('discuss.view_profiles');

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
                $mods[] = $canViewProfiles ? '<a href="'.$this->xpdo->discuss->url.'user?user='.$moderator->get('user').'">'.$moderator->get('username').'</a>' : $moderator->get('username');
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
                $sbs[] = '<a href="'.$this->xpdo->discuss->url.'board/?board='.$sb[0].'">'.$sb[1].'</a>';
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
                'text' => $this->get('name').($this->get('locked') ? $this->xpdo->lexicon('discuss.board_is_locked') : ''),
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
        $stmt->closeCursor();
        $this->clearCache();
        return true;
    }

    /**
     * Clear the cache for this board
     * 
     * @return void
     */
    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/board/'.$this->get('id'));
        }
    }

    /**
     * See if the active user can post on this board
     * @return boolean
     */
    public function canPost() {
        $canPost = false;
        if ($this->xpdo->discuss->user->isLoggedIn) {
            $level = 9998;
            if ($this->xpdo->discuss->user->isAdmin()) {
                $level = 0;
            } elseif ($this->isModerator($this->xpdo->discuss->user->get('id'))) {
                $level = 1;
            }
            /* only allow posts if meeting minimum level
             * AND if board is not archived (and user is not an admin, who can post to archived boards)
             * AND if board not locked
            */
            if ($level <= ((int)$this->get('minimum_post_level')) && ($this->get('status') != disBoard::STATUS_ARCHIVED || $level == 0) && !$this->get('locked')) {
                $canPost = true;
            }
        }
        return $canPost;
    }

    public static function getList(xPDO &$modx,$board = 0,$category = false) {
        $response = array();
        
        $unreadSubCriteria = $modx->newQuery('disThreadRead');
        $unreadSubCriteria->select($modx->getSelectColumns('disThreadRead','disThreadRead','',array('thread')));
        $unreadSubCriteria->where(array(
            'disThreadRead.user' => $modx->discuss->user->get('id'),
            $modx->getSelectColumns('disThreadRead','disThreadRead','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
        ));
        $unreadSubCriteria->prepare();
        $unreadSubCriteriaSql = $unreadSubCriteria->toSql();
        $unreadCriteria = $modx->newQuery('disThread');
        $unreadCriteria->setClassAlias('dp');
        $unreadCriteria->select('COUNT('.$modx->getSelectColumns('disThread','','',array('id')).')');
        $unreadCriteria->where(array(
            $modx->getSelectColumns('disThread','','',array('id')).' NOT IN ('.$unreadSubCriteriaSql.')',
            $modx->getSelectColumns('disThread','','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
        ));
        $unreadCriteria->prepare();
        $unreadSql = $unreadCriteria->toSql();


        /* subboards sql */
        $sbCriteria = $modx->newQuery('disBoard');
        $sbCriteria->setClassAlias('subBoard');
        $sbCriteria->select('GROUP_CONCAT(CONCAT_WS(":",subBoardClosureBoard.id,subBoardClosureBoard.name) SEPARATOR "||") AS name');
        $sbCriteria->innerJoin('disBoardClosure','subBoardClosure','subBoardClosure.ancestor = subBoard.id');
        $sbCriteria->innerJoin('disBoard','subBoardClosureBoard','subBoardClosureBoard.id = subBoardClosure.descendant');
        $sbCriteria->where(array(
            'subBoard.id = disBoard.id',
            'subBoard.status:!=' => disBoard::STATUS_INACTIVE,
            'subBoardClosureBoard.status:!=' => disBoard::STATUS_INACTIVE,
            'subBoardClosure.descendant != disBoard.id',
            'subBoardClosure.depth' => 1,
        ));
        $sbCriteria->groupby($modx->getSelectColumns('disBoard','subBoard','',array('id')));
        $sbCriteria->prepare();
        $sbSql = $sbCriteria->toSql();

        /* get main query */
        $c = $modx->newQuery('disBoard');
        $c->innerJoin('disCategory','Category');
        $c->innerJoin('disBoardClosure','Descendants');
        $c->leftJoin('disPost','LastPost');
        $c->leftJoin('disUser','LastPostAuthor','LastPost.author = LastPostAuthor.id');
        $c->leftJoin('disBoardUserGroup','UserGroups');
        $c->where(array(
            'disBoard.status:!=' => disBoard::STATUS_INACTIVE,
        ));
        if (isset($board) && $board !== null) {
            $c->where(array(
                'disBoard.parent' => $board,
            ));
        }
        if (!empty($category)) {
            $c->where(array(
                'disBoard.category' => $category,
            ));
        }
        $groups = $modx->discuss->user->getUserGroups();
        if (!$modx->discuss->user->isAdmin()) {
            if (!empty($groups)) {
                /* restrict boards by user group if applicable */
                $g = array(
                    'UserGroups.usergroup:IN' => $groups,
                );
                $g['OR:UserGroups.usergroup:IS'] = null;
                $where[] = $g;
                $c->andCondition($where,null,2);
            } else {
                $c->where(array(
                    'UserGroups.usergroup:IS' => null,
                ));
            }
        }
        if ($modx->discuss->isLoggedIn) {
            $ignoreBoards = $modx->discuss->user->get('ignore_boards');
            if (!empty($ignoreBoards)) {
                $c->where(array(
                    'id:NOT IN' => explode(',',$ignoreBoards),
                ));
            }
        }

        $response['total'] = $modx->getCount('disBoard',$c);
        $c->query['distinct'] = 'DISTINCT';
        $c->select($modx->getSelectColumns('disBoard','disBoard'));
        $c->select(array(
            'Category.name AS category_name',
            '('.$sbSql.') AS '.$modx->escape('subboards'),
            '('.$unreadSql.') AS '.$modx->escape('unread'),
            'LastPost.id AS last_post_id',
            'LastPost.thread AS last_post_thread',
            'LastPost.title AS last_post_title',
            'LastPost.author AS last_post_author',
            'LastPost.createdon AS last_post_createdon',
            'LastPostAuthor.username AS last_post_username',
        ));
        $c->sortby('Category.rank','ASC');
        $c->sortby('disBoard.rank','ASC');
        $response['results'] = $modx->getCollection('disBoard',$c);

        return $response;
    }

    public function get2ndLatestPost() {
        $c = $this->xpdo->newQuery('disPost');
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disPost','LastPost','Board.last_post = LastPost.id');
        $c->where(array(
            'board' => $this->get('id'),
            'LastPost.id != disPost.id',
            'LastPost.thread != disPost.thread',
        ));
        $c->sortby('disPost.createdon','DESC');
        $c->limit(1);
        return $this->xpdo->getObject('disPost',$c);
    }
}
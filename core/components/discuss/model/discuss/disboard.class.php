<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
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
            $c = $this->xpdo->newQuery('disBoardClosure');
            $c->where(array(
                'descendant:=' => $this->get('id'),
                'AND:ancestor:!=' => $this->get('id'),
            ));
            $rs = $this->xpdo->getCollection('disBoardClosure',$c);
            foreach ($rs as $r) {
                $r->remove();
            }

            $parents = $this->getRecursiveParentIds();
            array_unshift($parents,$this->get('id'));

            $idx = 0;
            $map = array();
            foreach ($parents as $parent) {
                $cl = $this->xpdo->newObject('disBoardClosure');
                $cl->set('ancestor',$parent);
                $cl->set('descendant',$this->get('id'));
                $cl->set('depth',$idx);
                $cl->save();
                if ($parent != 0) {
                    array_unshift($map,$parent);
                }
                $idx++;
            }
            $this->set('depth',($idx-1));
            $this->set('map',implode('.',$map));

            /* save */
            $saved = parent::save();
        }

        if ($saved) {
            $this->clearCache();
        }

        return $saved;
    }

    public function getRecursiveParentIds($id = false,array $ids = array()) {
        if ($id === false) {
            $board =& $this;
        } else {
            $board = $this->xpdo->getObject('disBoard',$id);
            $ids[] = $board->get('id');
        }
        if ($board->get('parent') == 0) {
            $ids[] = 0;
        } else {
            $ids = $this->getRecursiveParentIds($board->get('parent'),$ids);
        }
        return $ids;
    }

    public static function fetch(xPDO &$modx,$id,$integrated = false) {
        $c = $modx->newQuery('disBoard');
        $c->leftJoin('disBoardUserGroup','UserGroups');
        $c->where(array(
            ($integrated ? 'integrated_id' : 'id') => trim($id,'.'),
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
     * @return bool
     */
    public function isModerator() {
        if ($this->xpdo->discuss->user->isGlobalModerator()) return true;
         
        $moderator = $this->xpdo->getCount('disModerator',array(
            'user' => $this->xpdo->discuss->user->get('id'),
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
     * Get an array of disUser objects who are Moderators of this board
     * @return array
     */
    public function getModerators() {
        $gms = $this->xpdo->getOption('discuss.global_moderators',null,'');
        $gms = explode(',',$gms);

        $c = $this->xpdo->newQuery('disUser');
        $c->leftJoin('disModerator','Moderator','Moderator.user = disUser.id AND Moderator.board = '.$this->get('id'));
        $c->where(array(
            'disUser.username:IN' => $gms,
            'OR:Moderator.id:!=' => null,
        ));
        return $this->xpdo->getCollection('disUser',$c);
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
            $this->xpdo->cacheManager->delete('discuss/board/user/');
            $this->xpdo->cacheManager->delete('discuss/board/'.$this->get('id'));
            $this->xpdo->cacheManager->delete('discuss/board/index/');
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

        /* get a comma-sep-list of thread IDs for comparing to read ids for user */
        $threadsCriteria = $modx->newQuery('disThread');
        $threadsCriteria->setClassAlias('Threadr');
        $threadsCriteria->select(array(
            'GROUP_CONCAT(Threadr.id)',
        ));
        $threadsCriteria->where(array(
            'Threadr.board = disBoard.id',
        ));
        $threadsCriteria->prepare();
        $threadsSql = $threadsCriteria->toSql();

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
        $c->leftJoin('disThread','LastPostThread','LastPostThread.id = LastPost.thread');
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

        $ugc = $modx->newQuery('disBoardUserGroup');
        $ugc->select(array(
            'GROUP_CONCAT(usergroup)',
        ));
        $ugc->where(array(
            'board = disBoard.id',
        ));
        $ugc->groupby('board');
        $ugc->prepare();
        $userGroupsSql = $ugc->toSql();
        
        $response['total'] = $modx->getCount('disBoard',$c);
        $c->query['distinct'] = 'DISTINCT';
        $c->select($modx->getSelectColumns('disBoard','disBoard'));
        $c->select(array(
            'Category.name AS category_name',
            '('.$sbSql.') AS '.$modx->escape('subboards'),
            '('.$threadsSql.') AS '.$modx->escape('threads'),
            '('.$userGroupsSql.') AS '.$modx->escape('usergroups'),
            'LastPost.id AS last_post_id',
            'LastPost.thread AS last_post_thread',
            'LastPost.title AS last_post_title',
            'LastPost.author AS last_post_author',
            'LastPost.createdon AS last_post_createdon',
            'LastPostThread.replies AS last_post_replies',
            'LastPostAuthor.username AS last_post_username',
        ));
        $c->sortby('Category.rank','ASC');
        $c->sortby('disBoard.rank','ASC');
        $response['results'] = $modx->getCollection('disBoard',$c);

        return $response;
    }

    public static function fetchList(xPDO &$modx,$ignoreBoards = true) {
        $c = array(
            'ignore_boards' => $ignoreBoards,
        );
        $cacheKey = 'discuss/board/user/'.$modx->discuss->user->get('id').'/select-options-'.md5(serialize($c));
        $boards = $modx->cacheManager->get($cacheKey);
        if (empty($boards)) {
            $c = $modx->newQuery('disBoard');
            $c->innerJoin('disBoardClosure','Descendants');
            $c->leftJoin('disBoardUserGroup','UserGroups');
            $c->innerJoin('disCategory','Category');
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
            if ($modx->discuss->isLoggedIn && $ignoreBoards) {
                $ignoreBoards = $modx->discuss->user->get('ignore_boards');
                if (!empty($ignoreBoards)) {
                    $c->where(array(
                        'id:NOT IN' => explode(',',$ignoreBoards),
                    ));
                }
            }
            $c->select($modx->getSelectColumns('disBoard','disBoard'));
            $c->select(array(
                'MAX(Descendants.depth) AS depth',
                'Category.name AS category_name',
            ));
            $c->sortby('disBoard.category','ASC');
            $c->sortby('disBoard.map','ASC');
            $c->groupby('disBoard.id');
            $boardObjects = $modx->getCollection('disBoard',$c);
            foreach ($boardObjects as $board) {
                $boards[] = $board->toArray();
            }
            if (!empty($boards)) {
                $modx->cacheManager->set($cacheKey,$boards,$modx->getOption('discuss.cache_time',null,3600));
            }
        }
        return $boards;

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

    public function calcLastPostPage() {
        $page = 1;
        $replies = $this->get('last_post_replies');
        $perPage = $this->xpdo->getOption('discuss.post_per_page',null, 10);
        if ($replies > $perPage) {
            $page = ceil($replies / $perPage);
        }
        $this->set('last_post_page',$page);
        return $page;
    }

    public function canPostStickyThread() {
        return $this->xpdo->hasPermission('discuss.thread_stick') &&
            ($this->isModerator() || $this->xpdo->discuss->user->isAdmin());
    }

    public function canPostLockedThread() {
        return $this->xpdo->hasPermission('discuss.thread_lock') &&
            ($this->isModerator() || $this->xpdo->discuss->user->isAdmin());
    }

    public function canPostAttachments() {
        return $this->xpdo->discuss->user->isLoggedIn && $this->xpdo->hasPermission('discuss.thread_attach');
    }
}
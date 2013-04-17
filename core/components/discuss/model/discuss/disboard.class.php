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
 * @property int category The ID of the Category this board is in
 * @property int parent The ID of the parent Board. If none, will be 0.
 * @property string name The name of the board.
 * @property int last_post The ID of the most recent Post on this Board.
 * @property int num_topics The number of Threads on this Board.
 * @property int num_replies The number of replies to Threads on this Board.
 * @property int total_posts The total number of Posts (in all threads) on this Board.
 * @property boolean ignoreable Whether or not this Board can be ignored.
 * @property int rank The rank, or order, of this board in its category.
 * @property string map An internally-used map that stores the order of the board in the board tree.
 * @property int minimum_post_level The minimum post authority of the user that is required to post on this board (either Admin = 0,Moderator = 1,anything else = 9998)
 * @property int status The status of this board. See the STATUS_* constants.
 * @property boolean locked Whether or not this board is locked from more posts.
 * @property int integrated_id If this board was imported, the PK of the old system's board.
 *
 * @property disCategory $Category
 *
 * @package discuss
 */
class disBoard extends xPDOSimpleObject {
    /**
     * The status constant for inactive Boards.
     * @const STATUS_INACTIVE
     */
    const STATUS_INACTIVE = 0;
    /**
     * The status constant for active Boards.
     * @const STATUS_ACTIVE
     */
    const STATUS_ACTIVE = 1;
    /**
     * The status constant for archived Boards.
     * @const STATUS_ARCHIVED
     */
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
     * @param string $k
     * @param mixed $v
     * @param string $vType
     * @return boolean
     */
    public function set($k, $v= null, $vType= '') {
        $oldParentId = $this->get('parent');
        $set = parent::set($k,$v,$vType);
        if ($set && $k == 'parent' && $v != $oldParentId && !$this->isNew()) {
            $this->parentChanged = true;
        }
        return $set;
    }

    /**
     * Override xPDOObject::remove to clear cache on remove
     * @param array $ancestors
     * @return boolean
     */
    public function remove(array $ancestors = array()) {
        $removed = parent::remove($ancestors);
        $this->clearCache();
        return $removed;
    }

    /**
     * Overrides the xPDOObject::save method to provide custom functionality and
     * automation for the closure tables that persist the board map.
     *
     * @param boolean $cacheFlag
     * @return boolean
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
            /** @var disBoardClosure $gparent */
            foreach ($gparents as $gparent) {
                $depth = 0;
                $ancestor = $gparent->get('ancestor');
                if ($ancestor != 0) $depth = $i;
                /** @var disBoardClosure $obj */
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
                /** @var disBoardClosure $rootcl */
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
            /** @var disBoardClosure $r */
            foreach ($rs as $r) {
                $r->remove();
            }

            $parents = $this->getRecursiveParentIds();
            array_unshift($parents,$this->get('id'));

            $idx = 0;
            $map = array();
            foreach ($parents as $parent) {
                /** @var disBoardClosure $cl */
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

    /**
     * Get all the IDs of the parents of this board up the tree
     *
     * @param bool $id
     * @param array $ids
     * @return array
     */
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

    /**
     * Fetch a board by ID
     * 
     * @static
     * @param xPDO $modx
     * @param int $id
     * @param bool $integrated
     * @return disBoard
     */
    public static function fetch(xPDO &$modx,$id,$integrated = false) {
        $c = $modx->newQuery('disBoard');
        $c->leftJoin('disBoardUserGroup','UserGroups');
        $c->where(array(
            ($integrated ? 'integrated_id' : 'id') => trim($id,'.'),
        ));
        $groups = $modx->discuss->user->getUserGroups();
        $where = array();
        /* restrict boards by user group if applicable */
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
        $c->andCondition($where,null,2);
        return $modx->getObject('disBoard',$c);
    }

    /**
     * Grab the viewing text for the board.
     *
     * @access public
     * @return string The text returned for the viewing users.
     * @todo Fix hardcoded markup and separator and add check for display name.
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
            'disSession.place:LIKE' => 'board:'.$this->get('id').':%',
        ));
        $c->groupby('disSession.user');
        $members = $this->xpdo->getObject('disSession',$c);
        if ($members) {
            $readers = explode(',',$members->get('readers'));
            $members = array();
            foreach ($readers as $reader) {
                $r = explode(':',$reader);
                $members[] = $canViewProfiles ? '<a href="'.$this->xpdo->discuss->request->makeUrl('user',array('user' => $r[0])).'">'.$r[1].'</a>' : $r[1];
            }
            $members = array_unique($members);
            $members = implode(',',$members);
        } else { $members = '0 members'; }

        $c = $this->xpdo->newQuery('disSession');
        $c->where(array(
            'place:LIKE' => 'board:'.$this->get('id').':%',
            'AND:user:=' => 0,
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
     * @todo Get rid of hardcoded separator + markup
     */
    public function getModeratorsList() {
        $canViewProfiles = $this->xpdo->hasPermission('discuss.view_profiles');

        $c = $this->xpdo->newQuery('disModerator');
        $c->innerJoin('disUser','User');
        $c->select($this->xpdo->getSelectColumns('disModerator','disModerator','',array('id','user')));
        $c->select(array(
            'User.username',
            'User.use_display_name',
            'User.display_name',
        ));
        $c->where(array(
            'disModerator.board' => $this->get('id'),
        ));
        $moderators = $this->xpdo->getCollection('disModerator',$c);
        $mods = array();
        if ($moderators) {
            foreach ($moderators as $moderator) {
                $username = $moderator->get('username');
                if ($moderator->get('use_display_name')) {
                    $username = $moderator->get('display_name');
                }
                if (empty($username)) {
                    $username = $moderator->get('username');
                }
                $mods[] = $canViewProfiles ? '<a href="'.$this->xpdo->discuss->request->makeUrl('u/'.$moderator->get('username')).'">'.$username.'</a>' : $username;
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
                $sbs[] = '<a href="'.$this->xpdo->discuss->request->makeUrl('board/',array('board' => $sb[0])).'">'.$sb[1].'</a>';
            }
            $sbl = $this->xpdo->lexicon('discuss.subforums').': '.implode(',',$sbs);
        }
        $this->set('subforums',$sbl);
        return $sbl;
    }

    /**
     * Build the breadcrumb navigation for this board
     * 
     * @param array $additional
     * @param bool $linkToSelf
     * @return array
     */
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
                'url' => $this->xpdo->discuss->request->makeUrl(),
                'text' => $this->xpdo->getOption('discuss.forum_title'),
            );
            $category = $this->getOne('Category');
            if ($category) {
                $trail[] = array(
                    'url' => $this->xpdo->discuss->request->makeUrl('',array('category' => $category->get('id'))),
                    'text' => $category->get('name'),
                );
            }
            if (!empty($ancestors)) {
                foreach ($ancestors as $ancestor) {
                    $trail[] = array(
                        'url' => $this->xpdo->discuss->request->makeUrl('board',array('board' => $ancestor->get('id'))),
                        'text' => $ancestor->get('name'),
                    );
                }
            }
            $self = array(
                'text' => $this->get('name').($this->get('locked') ? $this->xpdo->lexicon('discuss.board_is_locked') : ''),
            );
            if ($linkToSelf) {
                $self['url'] = $this->xpdo->discuss->request->makeUrl('board/',array('board' => $this->get('id')));
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
            $this->xpdo->cacheManager->delete('discuss/recent/');
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
             * AND if board not locked (and user is not an admin, who can always post)
            */
            if ($level <= ((int)$this->get('minimum_post_level'))
            && ($this->get('status') != disBoard::STATUS_ARCHIVED || $level == 0)
            && (!$this->get('locked') || $level == 0)) {
                $canPost = true;
            }
        }
        return $canPost;
    }

    /**
     * Get a full list of all boards on the forum, for any user
     * @static
     * @param xPDO $modx
     * @param int $board
     * @param bool $category
     * @return array
     */
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
        $sbCriteria->select(array(
            'GROUP_CONCAT(CONCAT_WS(":",subBoardClosureBoard.id,subBoardClosureBoard.name) SEPARATOR "||") AS name'
        ));
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
            'LastPost.author AS last_post_author',
            'LastPost.createdon AS last_post_createdon',
            'LastPostThread.replies AS last_post_replies',
            'LastPostThread.title AS last_post_title',
            'LastPostAuthor.username AS last_post_username',
            'LastPostAuthor.use_display_name AS last_post_udn',
            'LastPostAuthor.display_name AS last_post_display_name',
        ));
        $c->sortby('Category.rank','ASC');
        $c->sortby('disBoard.rank','ASC');
        $response['results'] = $modx->getCollection('disBoard',$c);

        return $response;
    }

    /**
     * Fetch a full list of boards for the forum with restrictions based on current user
     * 
     * @static
     * @param xPDO $modx
     * @param bool $ignoreBoards
     * @return array
     */
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
            if ($modx->discuss->user->isLoggedIn && $ignoreBoards) {
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
            $c->sortby('Category.rank','ASC');
            $c->sortby('disBoard.map','ASC');
            $c->groupby('disBoard.id');
            $boardObjects = $modx->getCollection('disBoard',$c);
            /** @var disBoard $board */
            foreach ($boardObjects as $board) {
                $boards[] = $board->toArray();
            }
            if (!empty($boards)) {
                $modx->cacheManager->set($cacheKey,$boards,$modx->getOption('discuss.cache_time',null,3600));
            }
        }
        return $boards;

    }

    /**
     * Get the 2nd to last Post
     * @return disPost
     */
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

    /**
     * Calc the page of the last post on this board (requires prior data filled)
     * @return float|int
     */
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

    /**
     * Can active user post a sticky thread on this board
     * @return bool
     */
    public function canPostStickyThread() {
        return $this->xpdo->hasPermission('discuss.thread_stick') &&
            ($this->isModerator() || $this->xpdo->discuss->user->isAdmin());
    }

    /**
     * Can active user post a locked thread on this board
     * 
     * @return bool
     */
    public function canPostLockedThread() {
        return $this->xpdo->hasPermission('discuss.thread_lock') &&
            ($this->isModerator() || $this->xpdo->discuss->user->isAdmin());
    }

    /**
     * Can active user post attachments on this board
     * 
     * @return bool
     */
    public function canPostAttachments() {
        return $this->xpdo->discuss->user->isLoggedIn && $this->xpdo->hasPermission('discuss.thread_attach');
    }

    /**
     * Deprecated, use disBoard.getLastPostTitleSlug instead.
     * @deprecated 2012-09-07
     */
    public function getLastPostTitle($key = 'last_post_title') {
        return $this->getLastPostTitleSlug($key);
    }

    /**
     * Get the title of the last post, properly escaped for building urls. (requires prior data filled)
     * @param string $key
     * @return string
     */
    public function getLastPostTitleSlug($key = 'last_post_title') {
        $title = $this->get($key);
        if (!empty($title)) {
            $title = trim(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($title)),'-').'/';
        }
        return $title;
    }

    /**
     * Get the URL of the last post on this board (requires prior data filled)
     * @return string
     */
    public function getLastPostUrl() {
        $view = 'thread/'.$this->get('last_post_thread').'/'.$this->getLastPostTitleSlug();

        $params = array();
        $sortDir = $this->xpdo->getOption('discuss.post_sort_dir',null,'ASC');
        if ($this->get('last_post_page') > 1 && $sortDir == 'ASC') {
            $params['page'] = $this->get('last_post_page');
        }
        $url = $this->xpdo->discuss->request->makeUrl($view,$params);

        $url = $url.'#dis-post-'.$this->get('last_post_id');
        $this->set('last_post_url',$url);
        return $url;
    }

    /**
     * Override toArray to provide more values
     *
     * @param string $keyPrefix
     * @param bool $rawValues
     * @param bool $excludeLazy
     * @param bool $includeRelated
     * @return array
     */
    public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated = false) {
        $values = parent :: toArray($keyPrefix,$rawValues,$excludeLazy);
        if ($this->xpdo->context->key != 'mgr' && $this->xpdo->discuss) {
            $values['url'] = $this->getUrl();
        }
        return $values;
    }

    /**
     * Get the friendly URL for this board
     *
     * @return string
     */
    public function getUrl() {
        return $this->xpdo->discuss->request->makeUrl('board/'.$this->get('id').'/'.$this->getSlug());
    }

    /**
     * Get the URL-friendly name of this board
     * @return string
     */
    public function getSlug() {
        $title = $this->get('name');
        if (!empty($title)) {
            $title = trim(preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($title)),'-');
        } else {
            $title = $this->get('id');
        }
        return $title;

    }
}

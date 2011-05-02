<?php
/**
 * @package discuss
 */
class disThread extends xPDOSimpleObject {
    const TYPE_POST = 'post';
    const TYPE_MESSAGE = 'message';
    
    /**
     * Fetch a thread, but check permissions first
     * 
     * @static
     * @param xPDO $modx A reference to the modX instance
     * @param $id The ID of the thread
     * @param string $type The type of thread: post/message
     * @return disThread/null The returned, grabbed thread; or false/null if not found/accessible
     */
    public static function fetch(xPDO &$modx, $id, $type = disThread::TYPE_POST) {
        $c = $modx->newQuery('disThread');
        $c->innerJoin('disPost','FirstPost');
        $c->select($modx->getSelectColumns('disThread','disThread'));
        $c->select(array(
            'FirstPost.title',
            '(SELECT GROUP_CONCAT(pAuthor.id)
                FROM '.$modx->getTableName('disPost').' AS pPost
                INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
                WHERE pPost.thread = disThread.id
             ) AS participants',
        ));
        $c->where(array(
            'disThread.id' => $id,
        ));
        if ($type == disThread::TYPE_POST) {
            $c->innerJoin('disBoard','Board');
            $c->where(array(
                'Board.status:!=' => disBoard::STATUS_INACTIVE,
            ));
            $c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
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
        }
        if ($type == disThread::TYPE_MESSAGE) {
            $c->innerJoin('disThreadUser','Users');
            $c->leftJoin('disUser','ThreadUser','Users.user = ThreadUser.id');
            $c->where(array(
                'Users.user' => $modx->discuss->user->get('id'),
            ));
            $c->select(array(
                '(SELECT GROUP_CONCAT(sqThreadUser.username)
                    FROM '.$modx->getTableName('disThreadUser').' AS sqThreadUsers
                        INNER JOIN '.$modx->getTableName('disUser').' AS sqThreadUser
                        ON sqThreadUser.id = sqThreadUsers.user
                    WHERE sqThreadUsers.thread = disThread.id
                 ) AS participants_usernames',
            ));
        }
        $thread = $modx->getObject('disThread',$c);
        if ($thread && $type == disThread::TYPE_MESSAGE) {
            $pu = array_unique(explode(',',$thread->get('participants_usernames')));
            asort($pu);
            $thread->set('participants_usernames',implode(',',$pu));
        }
        return $thread;
    }

    /**
     * Fetch all unread, accessible threads
     * 
     * @static
     * @param xPDO $modx A reference to the modX instance
     * @param string $sortBy The column to sort by
     * @param string $sortDir The direction to sort
     * @param int $limit The # of threads to limit
     * @param int $start The index to start by
     * @param boolean $sinceLastLogin
     * @return array An array in results/total format
     */
    public static function fetchUnread(xPDO &$modx,$sortBy = 'LastPost.createdon',$sortDir = 'DESC',$limit = 20,$start = 0,$sinceLastLogin = false) {
        $response = array();
        $c = $modx->newQuery('disThread');
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disPost','FirstPost');
        $c->innerJoin('disPost','LastPost');
        $c->innerJoin('disUser','FirstAuthor');
        $c->leftJoin('disThreadRead','Reads','Reads.thread = disThread.id AND Reads.user = '.$modx->discuss->user->get('id'));
        $c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
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
        $c->where(array(
            'Reads.thread:IS' => null,
            'Board.status:!=' => disBoard::STATUS_INACTIVE,
        ));
                
        /* ignore spam/recycle bin boards */
        $spamBoard = $modx->getOption('discuss.spam_bucket_board',null,false);
        if (!empty($spamBoard)) {
            $c->where(array(
                'Board.id:!=' => $spamBoard,
            ));
        }
        $trashBoard = $modx->getOption('discuss.recycle_bin_board',null,false);
        if (!empty($trashBoard)) {
            $c->where(array(
                'Board.id:!=' => $trashBoard,
            ));
        }

        /* usergroup protection */
        if ($modx->discuss->isLoggedIn) {
            if ($sinceLastLogin) {
                $lastLogin = $modx->discuss->user->get('last_login');
                if (!empty($lastLogin)) {
                    $c->where(array(
                        'LastPost.createdon:>=' => $lastLogin,
                    ));
                }
            }
            $ignoreBoards = $modx->discuss->user->get('ignore_boards');
            if (!empty($ignoreBoards)) {
                $c->where(array(
                    'Board.id:NOT IN' => explode(',',$ignoreBoards),
                ));
            }
        }
        $response['total'] = $modx->getCount('disThread',$c);
        $c->select($modx->getSelectColumns('disThread','disThread'));
        $c->select(array(
            'Board.name AS board_name',
            'FirstPost.title AS title',
            'FirstPost.thread AS thread',
            'FirstAuthor.username AS author_username',

            'LastPost.id AS post_id',
            'LastPost.createdon AS createdon',
            'LastPost.author AS author',
        ));
        $c->sortby($sortBy,$sortDir);
        $c->limit($limit,$start);
        $response['results'] = $modx->getCollection('disThread',$c);

        return $response;
    }

    /**
     * Override remove() to clear thread cache
     *
     * @param array $ancestors
     * @param boolean $doBoardMoveChecks
     * @param boolean $moveToSpam
     * @return boolean
     */
    public function remove(array $ancestors = array(),$doBoardMoveChecks = false,$moveToSpam = false) {
        $remove = false;
        $removed = false;

        if (!empty($doBoardMoveChecks)) {
            $board = $this->getOne('Board');
            if (!empty($board)) {
                $isModerator = $board->isModerator($this->xpdo->discuss->user->get('id'));
                $isAdmin = $this->xpdo->discuss->user->isAdmin();
                if ($isAdmin) { /* admins can always remove threads */
                    $remove = true;
                } else if ($isModerator) { /* move to spambox/recyclebin */
                    $spamBoard = $this->xpdo->getOption('discuss.spam_bucket_board',null,false);
                    if ($moveToSpam && !empty($spamBoard)) {
                        if ($this->move($spamBoard)) {
                            $removed = true;
                        }
                    } else {
                        $trashBoard = $this->xpdo->getOption('discuss.recycle_bin_board',null,false);
                        if (!empty($trashBoard) && $this->move($trashBoard)) {
                            $removed = true;
                        } else {
                            $remove = true;
                        }
                    }
                }
            } else { /* is a PM */
                $remove = true;
            }
        } else { /* skipping, usually used for related objs */
            $remove = true;
        }

        if ($remove) {
            $removed = parent::remove($ancestors);
        }

        if ($removed) {
            $this->clearCache();
        }
        return $removed;
    }

    /**
     * Clear cache for this thread
     *
     * @return void
     */
    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/thread/'.$this->get('id'));
            $this->xpdo->cacheManager->delete('discuss/board/'.$this->get('board'));
        }
    }

    
    /**
     * Gets the viewing message for the bottom of the thread
     *
     * @access public
     * @param string $placePrefix
     * @return string The who is viewing message
     */
    public function getViewing($placePrefix = 'thread') {
        if (!$this->xpdo->getOption('discuss.show_whos_online',null,true)) return '';
        if (!$this->xpdo->hasPermission('discuss.view_online')) return '';
        $canViewProfiles = $this->xpdo->hasPermission('discuss.view_profiles');

        $c = $this->xpdo->newQuery('disSession');
        $c->innerJoin('disUser','User');
        $c->select($this->xpdo->getSelectColumns('disSession','disSession','',array('id')));
        $c->select(array(
            'CONCAT_WS(":",User.id,User.username) AS reader',
        ));
        $c->where(array(
            'disSession.place' => $placePrefix.':'.$this->get('id'),
        ));
        $c->groupby('disSession.user');
        $sessions = $this->xpdo->getCollection('disSession',$c);

        if (!empty($sessions)) {
            $members = array();
            foreach ($sessions as $member) {
                $r = explode(':',$member->get('reader'));
                $members[] = $canViewProfiles ? '<a href="'.$this->xpdo->discuss->url.'user/?user='.str_replace('%20','',$r[0]).'">'.$r[1].'</a>' : $r[1];
            }
            $members = array_unique($members);
            $members = implode(',',$members);
        } else { $members = $this->xpdo->lexicon('discuss.zero_members'); }

        $c = $this->xpdo->newQuery('disSession');
        $c->where(array(
            'place' => $placePrefix.':'.$this->get('id'),
            'user' => 0,
        ));
        $guests = $this->xpdo->getCount('disSession',$c);

        return $this->xpdo->lexicon('discuss.thread_viewing',array(
            'members' => $members,
            'guests' => $guests,
        ));
    }

    /**
     * Marks a post read by the currently logged in user.
     *
     * @access public
     * @param int $user
     * @return boolean True if successful.
     */
    public function read($user) {
        $read = $this->xpdo->getObject('disThreadRead',array(
            'thread' => $this->get('id'),
            'user' => $user,
        ));
        if ($read != null) return false;

        $read = $this->xpdo->newObject('disThreadRead');
        $read->set('thread',$this->get('id'));
        $read->set('board',$this->get('board'));
        $read->set('user',$user);

        $saved = $read->save();
        if (!$saved) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR,'[Discuss] An error occurred while trying to mark read the post: '.print_r($read->toArray(),true));
        }
        return $saved;
    }

    /**
     * Marks a post unread by the currently logged in user.
     *
     * @access public
     * @param int $user
     * @return boolean True if successful.
     */
    public function unread($user) {
        $read = $this->xpdo->getObject('disThreadRead',array(
            'user' => $user,
            'thread' => $this->get('id'),
        ));
        if ($read == null) return true;

        $removed = $read->remove();
        if (!$removed) {
            $this->xpdo->log(modX::LOG_LEVEL_ERROR,'[Discuss] An error occurred while trying to mark unread the post: '.print_r($read->toArray(),true));
        }
        return $removed;
    }

    /**
     * Make thread sticky
     * 
     * @return boolean
     */
    public function stick() {
        $this->set('sticky',true);
        return $this->save();
    }

    /**
     * Make thread unsticky
     *
     * @return boolean
     */
    public function unstick() {
        $this->set('sticky',false);
        return $this->save();
    }

    /**
     * Lock thread
     *
     * @return boolean
     */
    public function lock() {
        $this->set('locked',true);
        return $this->save();
    }

    /**
     * Unlock thread
     *
     * @return boolean
     */
    public function unlock() {
        $this->set('locked',false);
        return $this->save();
    }

    /**
     * Check if a user (by id) has a subscription to this thread
     *
     * @param int $userId ID of disUser
     * @return bool True if has a subscription
     */
    public function hasSubscription($userId) {
        return $this->xpdo->getCount('disUserNotification',array(
            'user' => $userId,
            'thread' => $this->get('id'),
        )) > 0;
    }

    /**
     * Add a subscription to this thread for the User
     * 
     * @param int $userId ID of disUser
     * @return bool True if successful
     */
    public function addSubscription($userId) {
        $success = false;
        $notify = $this->xpdo->getObject('disUserNotification',array(
            'user' => $userId,
            'thread' => $this->get('id'),
        ));
        if (!$notify) {
            $notify = $this->xpdo->newObject('disUserNotification');
            $notify->set('user',$userId);
            $notify->set('thread',$this->get('id'));
            $notify->set('board',$this->get('board'));
            if (!$notify->save()) {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] Could not create notification: '.print_r($notify->toArray(),true));
            } else {
                $success = true;
            }
        }
        return $success;
    }

    /**
     * Remove a subscription to this thread for the User
     *
     * @param int $userId ID of disUser
     * @return bool True if successful
     */
    public function removeSubscription($userId) {
        $success = false;
        $notify = $this->xpdo->getObject('disUserNotification',array(
            'user' => $userId,
            'thread' => $this->get('id'),
        ));
        if ($notify) {
            if (!$notify->remove()) {
                $this->xpdo->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not remove notification: '.print_r($notify->toArray(),true));
            } else {
                $success = true;
            }
        }
        return $success;
    }

    /**
     * Check to see if given user (by id) is a Moderator for this thread's board
     *
     * @param int $userId The ID of the user
     * @return bool True if they are a moderator
     */
    public function isModerator($userId) {
        $moderator = $this->xpdo->getCount('disModerator',array(
            'user' => $userId,
            'board' => $this->get('board'),
        ));
        return $moderator > 0;
    }

    /**
     * Build the breadcrumb trail for this thread
     *
     * @param array $defaultTrail
     * @return array
     */
    public function buildBreadcrumbs($defaultTrail = array()) {
        $c = $this->xpdo->newQuery('disBoard');
        $c->innerJoin('disBoardClosure','Ancestors');
        $c->where(array(
            'Ancestors.descendant' => $this->get('board'),
        ));
        $c->sortby('Ancestors.depth','DESC');
        $ancestors = $this->xpdo->getCollection('disBoard',$c);
        $trail = empty($defaultTrail) ? array(array(
            'url' => $this->xpdo->discuss->url,
            'text' => $this->xpdo->getOption('discuss.forum_title'),
        )) : $defaultTrail;
        $category = false;
        foreach ($ancestors as $ancestor) {
            if (empty($category)) {
                $category = $ancestor->getOne('Category');
                if ($category) {
                    $trail[] = array(
                        'url' => $this->xpdo->discuss->url.'?category='.$category->get('id'),
                        'text' => $category->get('name'),
                    );
                }
            }
            $trail[] = array(
                'url' => $this->xpdo->discuss->url.'board/?board='.$ancestor->get('id'),
                'text' => $ancestor->get('name'),
            );
        }
        $title = str_replace(array('[',']'),array('&#91;','&#93;'),$this->get('title'));
        $trail[] = array('text' => $title, 'active' => true);
        $trail = $this->xpdo->discuss->hooks->load('breadcrumbs',array(
            'items' => &$trail,
        ));
        $this->set('trail',$trail);
        return $trail;
    }

    /**
     * Build the CSS class for this thread
     *
     * @param string $defaultClass
     * @return array|string
     */
    public function buildCssClass($defaultClass = 'dis-normal-thread') {
        $class = array($defaultClass);
        $threshold = $this->xpdo->getOption('discuss.hot_thread_threshold',null,10);
        $participants = explode(',',$this->get('participants'));
        if (in_array($this->xpdo->discuss->user->get('id'),$participants) && $this->xpdo->discuss->isLoggedIn) {
            $class[] = $this->get('replies') < $threshold ? 'dis-my-normal-thread' : 'dis-my-veryhot-thread';
        } else {
            $class[] = $this->get('replies') < $threshold ? '' : 'dis-veryhot-thread';
        }
        $class = implode(' ',$class);
        $this->set('class',$class);
        return $class;
    }

    /**
     * Build the icon list for this thread
     *
     * @param array $icons
     * @return array|string
     */
    public function buildIcons($icons = array()) {
        if ($this->get('locked')) {
            $icons[] = '<div class="dis-thread-locked"></div>';
        }
        if ($this->xpdo->getOption('discuss.enable_sticky',null,true) && $this->get('sticky')) {
            $icons[] = '<div class="dis-thread-sticky"></div>';
        }
        $icons = implode("\n",$icons);
        $this->set('icons',$icons);
        return $icons;
    }

    /**
     * Up the view count and set last visited for the Thread.
     * @return void
     */
    public function view() {
        /* up the view count for this thread */
        $views = $this->get('views');
        $this->set('views',($views+1));
        $this->save();
        unset($views);

        /* set last visited */
        if ($this->xpdo->discuss->user->get('user') != 0) {
            $this->xpdo->discuss->user->set('thread_last_visited',$this->get('id'));
            $this->xpdo->discuss->user->save();
        }
        return true;
    }
    
    /**
     * Fetch all posts for this thread
     *
     * @param mixed $post A reference to a disPost or ID of disPost to start the posts from
     * @param int $limit The number of posts to limit to
     * @param int $start The starting index of posts to start at
     * @param bool $flat If true, will render posts flat
     * @return array
     */
    public function fetchPosts($post = false,$limit = 0,$start = 0,$flat = true) {
        $response = array();
        $c = $this->xpdo->newQuery('disPost');
        $c->innerJoin('disThread','Thread');
        $c->where(array(
            'thread' => $this->get('id'),
        ));
        $cc = clone $c;
        $response['total'] = $this->xpdo->getCount('disPost',$cc);
        if ($flat) {
            $c->sortby($this->xpdo->getSelectColumns('disPost','disPost','',array('createdon')),'ASC');
            $c->limit($limit, $start);
        } else {
            $c->sortby($this->xpdo->getSelectColumns('disPost','disPost','',array('rank')),'ASC');
        }

        if (!empty($post)) {
            if (!is_object($post)) {
                $post = $this->xpdo->getObject('disPost',$post);
            }
            if ($post) {
                $c->where(array(
                    'disPost.createdon:>=' => $post->get('createdon'),
                ));
            }
        }

        $c->bindGraph('{"Author":{},"EditedBy":{}}');
        //$c->prepare();
        //$cacheKey = 'discuss/thread/'.$thread->get('id').'/'.md5($c->toSql());
        $response['results'] = $this->xpdo->getCollectionGraph('disPost','{"Author":{},"EditedBy":{}}',$c);

        return $response;
    }

    /**
     * Move a thread to a new board
     *
     * @param int $boardId
     * @return boolean True if successful
     */
    public function move($boardId) {
        $oldBoard = $this->getOne('Board');
        $newBoard = is_object($boardId) && $boardId instanceof disBoard ? $boardId : $this->xpdo->getObject('disBoard',$boardId);
        if (!$oldBoard || !$newBoard) {
            return false;
        }
        $this->addOne($newBoard);
        if ($this->save()) {
            /* readjust all posts */
            $posts = $this->getMany('Posts');
            foreach ($posts as $post) {
                $post->set('board',$newBoard->get('id'));
                $post->save();
            }

            /* adjust old board topics/reply counts */
            $oldBoard->set('num_topics',$oldBoard->get('num_topics')-1);

            $replies = $oldBoard->get('num_replies') - $this->get('replies');
            $oldBoard->set('num_replies',$replies);

            $total_posts = $oldBoard->get('total_posts') - $this->get('replies') - 1;
            $oldBoard->set('total_posts',$total_posts);

            /* recalculate latest post */
            $oldBoardLastPost = $this->xpdo->getObject('disPost',array('id' => $oldBoard->get('last_post')));
            if ($oldBoardLastPost && $oldBoardLastPost->get('id') == $this->get('post_last')) {
                $newLastPost = $oldBoard->get2ndLatestPost();
                if ($newLastPost) {
                    $oldBoard->set('last_post',$newLastPost->get('id'));
                    $oldBoard->addOne($newLastPost,'LastPost');
                }
            }
            $oldBoard->save();

            /* adjust new board topics/reply counts */
            $newBoard->set('num_topics',$oldBoard->get('num_topics')-1);

            $replies = $newBoard->get('num_replies') + $this->get('replies');
            $newBoard->set('num_replies',$replies);

            $total_posts = $newBoard->get('total_posts') + $this->get('replies') + 1;
            $newBoard->set('total_posts',$total_posts);

            /* recalculate latest post */
            $newBoardLastPost = $this->xpdo->getObject('disPost',array('id' => $newBoard->get('last_post')));
            $thisThreadPost = $this->getOne('LastPost');
            if ($newBoardLastPost && $thisThreadPost && $newBoardLastPost->get('createdon') < $thisThreadPost->get('createdon')) {
                $newBoard->set('last_post',$thisThreadPost->get('id'));
                $newBoard->addOne($thisThreadPost,'LastPost');
            }
            $newBoard->save();

            /* clear caches */
            if (!defined('DISCUSS_IMPORT_MODE')) {
                $this->xpdo->getCacheManager();
                $this->xpdo->cacheManager->delete('discuss/thread/'.$this->get('id'));
                $this->xpdo->cacheManager->delete('discuss/board/'.$newBoard->get('id'));
                $this->xpdo->cacheManager->delete('discuss/board/'.$oldBoard->get('id'));
            }
        }
        return true;
    }
}
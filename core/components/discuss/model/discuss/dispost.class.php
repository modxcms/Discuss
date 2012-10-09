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
 * Represents any post made on the forum, including Threads, which are Posts
 * with a parent of 0.
 *
 * @property int $board
 * @property int $thread
 * @property int $parent
 * @property string $title
 * @property string $message
 * @property int $author
 * @property datetime $createdon
 * @property int $editedby
 * @property datetime $editedon
 * @property string $icon
 * @property boolean $allow_replies
 * @property string $rank
 * @property string $ip
 * @property int $integrated_id
 * @property int $depth
 * @property boolean $answer
 *
 * @property disBoard $Board
 * @property disThread $Thread
 * @property disUser $Author
 * @property disUser $CreatedBy
 * @property disUser $EditedBy
 * @property disPost $Parent
 * @property array $Children
 * @property array $Ancestors
 * @property array $Descendants
 * @property array $Attachments
 * @package discuss
 */
class disPost extends xPDOSimpleObject {
    /**
     * The parsing engine for this post
     * @var disParser $parser
     */
    public $parser;
    
    /**
     * Overrides xPDOObject::save to handle closure table edits.
     *
     * @todo add code for moving posts to different parents.
     *
     * @param boolean $cacheFlag
     * @return boolean
     */
    public function save($cacheFlag = null) {
        $new = $this->isNew();

        if ($new) {
            if (!$this->get('createdon')) {
                $this->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
            }
            $ip = $this->get('ip');
            if (empty($ip)) {
                $this->set('ip',$_SERVER['REMOTE_ADDR']);
            }
        }

        $saved = parent::save($cacheFlag);

        if ($saved && $new) {
            $id = $this->get('id');
            $parent = $this->get('parent');

            /* create self closure */
            /** @var disPostClosure $cl */
            $cl = $this->xpdo->newObject('disPostClosure');
            $cl->set('ancestor',$id);
            $cl->set('descendant',$id);
            $cl->save();

            /* create closures and calculate rank */
            $c = $this->xpdo->newQuery('disPostClosure');
            $c->where(array(
                'descendant' => $parent,
                'ancestor:!=' => 0,
            ));
            $c->sortby('depth','DESC');
            /** @var array $gparents */
            $gparents = $this->xpdo->getCollection('disPostClosure',$c);
            $cgps = count($gparents);
            $gps = array();
            $i = $cgps;
            /** @var disPostClosure $gparent */
            foreach ($gparents as $gparent) {
                $gps[] = str_pad($gparent->get('ancestor'),10,'0',STR_PAD_LEFT);
                /** @var disPostClosure $obj */
                $obj = $this->xpdo->newObject('disPostClosure');
                $obj->set('ancestor',$gparent->get('ancestor'));
                $obj->set('descendant',$id);
                $obj->set('depth',$i);
                $obj->save();
                $i--;
            }
            $gps[] = str_pad($id,10,'0',STR_PAD_LEFT); /* add self closure too */

            /* add root closure */
            /** @var disPostClosure $cl */
            $cl = $this->xpdo->newObject('disPostClosure');
            $cl->set('ancestor',0);
            $cl->set('descendant',$id);
            $cl->set('depth',$cgps);
            $cl->save();

            /* set rank + depth */
            $rank = implode('-',$gps);
            $this->set('rank',$rank);
            if (!defined('DISCUSS_IMPORT_MODE')) {
                $this->set('depth',$cgps);
            }
            $this->save();

            /* adjust board total replies/posts, and last post */
            $board = $this->getOne('Board');
            if ($board  && !defined('DISCUSS_IMPORT_MODE')) {
                $board->set('last_post',$this->get('id'));
                $board->set('total_posts',$board->get('total_posts')+1);
                if ($this->get('parent') != 0) {
                    $board->set('num_replies',$board->get('num_replies')+1);
                } else {
                    $board->set('num_topics',$board->get('num_topics')+1);
                }
                $board->save();
            }

            /** @var disThread $thread */
            $thread = $this->getOne('Thread');
            $privatePost = $this->get('private');

            /* set thread, update thread  */
            if (!defined('DISCUSS_IMPORT_MODE')) {
                if (!$thread) {
                    $thread = $this->xpdo->newObject('disThread');
                    $thread->fromArray(array(
                        'board' => $this->get('board'),
                        'title' => $this->get('title'),
                        'post_first' => $this->get('id'),
                        'author_first' => $this->get('author'),
                        'replies' => 0,
                        'views' => 0,
                        'class_key' => $this->get('class_key'),
                    ));
                }

                $thread->set('post_last',$this->get('id'));
                $thread->set('post_last_on',time());
                $thread->set('author_last',$this->get('author'));
                $thread->set('replies',$thread->get('replies')+1);
                if ($thread->get('post_last') == $thread->get('post_first')) {
                    $thread->set('replies',0);
                }
                $thread->save();

                $this->set('thread',$thread->get('id'));
                $this->save();
            }

            /* adjust forum activity */
            if (!defined('DISCUSS_IMPORT_MODE') && $thread && !$thread->get('private') && empty($privatePost)) {
                $now = date('Y-m-d');
                /** @var disForumActivity $activity */
                $activity = $this->xpdo->getObject('disForumActivity',array(
                    'day' => $now,
                ));
                if (!$activity) {
                    $activity = $this->xpdo->newObject('disForumActivity');
                    $activity->set('day',$now);
                }
                if ($this->get('parent') != 0) {
                    $activity->set('replies',$activity->get('replies')+1);
                } else {
                    $activity->set('topics',$activity->get('topics')+1);
                }
                $activity->save();
            }


            /* up author post count */
            if ($thread && !$thread->get('private') && empty($privatePost)) {
                $author = $this->getOne('Author');
                if ($author && !defined('DISCUSS_IMPORT_MODE')) {
                    $author->set('posts',($author->get('posts')+1));
                    $author->save();
                }
            }
        } /* end if $saved && $new */

        if ($saved) {
            /* fix participants */
            $thread = $this->getOne('Thread');
            if ($thread) {
                $thread->addParticipant($this->get('author'));
            }

            $this->index();

            /* clear cache */
            $this->clearCache();
        }
        return $saved;
    }

    /**
     * Index the post into the search system
     * 
     * @return bool
     */
    public function index() {
        if (defined('DISCUSS_IMPORT_MODE') && DISCUSS_IMPORT_MODE) return true;
        $indexed = false;
        if ($this->xpdo->discuss->loadSearch()) {
            $postArray = $this->toArray();
            $postArray['url'] = $this->getUrl();
            if (empty($postArray['username'])) {
                $this->getOne('Author');
                if ($this->Author) {
                    $postArray['username'] = $this->Author->get('username');
                }
            }
            if (empty($postArray['board_name'])) {
                $this->getOne('Board');
                if ($this->Board) {
                    $postArray['board_name'] = $this->Board->get('name');
                    $postArray['category'] = $this->Board->get('category');
                    $this->Board->getOne('Category');
                    if ($this->Board->Category) {
                        $postArray['category_name'] = $this->Board->Category->get('name');
                    }
                }
            }
            $this->getOne('Thread');
            if ($this->Thread) {
                $postArray['private'] = $this->Thread->get('private');
                $postArray['users'] = $this->Thread->get('users');
                $postArray['replies'] = $this->Thread->get('replies');
            }
            $postArray['message'] = $this->getContent();
            $indexed = $this->xpdo->discuss->search->index($postArray);
        }
        return $indexed;
    }

    /**
     * Remove a Post from the index
     * 
     * @return bool True if removed
     */
    public function removeFromIndex() {
        $removed = false;
        if ($this->xpdo->discuss->loadSearch()) {
            $removed = $this->xpdo->discuss->search->removeIndex($this->get('id'));
        }
        return $removed;
    }


    /**
     * Always ensure that the title strips any HTML/MODX tags
     * @param string $k
     * @param string|null $format
     * @param string|null $formatTemplate
     * @return mixed
     */
    public function get($k, $format = null, $formatTemplate= null) {
        $v = parent::get($k,$format,$formatTemplate);
        switch ($k) {
            case 'title':
            	$v = htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
                break;
            default: break;
        }
        return $v;
    }

    /**
     * Always ensure that the title strips any HTML/MODX tags
     * @param string $keyPrefix
     * @param bool $rawValues
     * @param bool $excludeLazy
     * @param bool $includeRelated
     * @return array
     */
    public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated = false) {
        $array = parent::toArray($keyPrefix,$rawValues,$excludeLazy);
        foreach ($array as $k => &$v) {
            if ($k == 'title') {
                $v = $this->xpdo->discuss->stripAllTags($v);
            }
        }
        reset($array);
        return $array;
    }

    /**
     * Move a post to a different board
     *
     * @param int|disBoard $boardId The ID of the board or disBoard obj to move to
     * @return bool
     */
    public function move($boardId) {
        /* check to see if only post in thread, if so, just move thread */
        /** @var disThread $oldThread */
        $oldThread = $this->xpdo->getObject('disThread',array('id' => $this->get('thread')));
        /** @var disBoard $newBoard */
        $newBoard = is_object($boardId) && $boardId instanceof disBoard ? $boardId : $this->xpdo->getObject('disBoard',$boardId);
        /** @var disBoard $oldBoard */
        $oldBoard = $this->xpdo->getObject('disBoard',array('id' => $this->get('board')));
        if (!$oldThread || !$newBoard || !$oldBoard) return false;

        $postCount = $this->xpdo->getCount('disPost',array('thread' => $oldThread->get('id')));
        if ($postCount == 1) {
            return $oldThread->move($boardId);
        }

        /* is multiple posts in thread, so split post out and move new thread */
        /** @var disThread $newThread */
        $newThread = $this->xpdo->newObject('disThread');
        $newThread->fromArray($oldThread->toArray());
        $newThread->set('board',$newBoard->get('id'));
        $newThread->set('post_first',$this->get('id'));
        $newThread->set('post_last',$this->get('id'));
        $newThread->set('author_first',$this->get('author'));
        $newThread->set('author_last',$this->get('author'));
        $newThread->set('replies',0);
        if ($newThread->save()) {
            $this->set('thread',$newThread->get('id'));
            $this->set('board',$newBoard->get('id'));
            $this->addOne($newThread,'Thread');
            $this->addOne($newBoard,'Board');
            $this->save();

            $oldThread->removeParticipant($this->get('author'),$this->get('id'));
            $oldThread->set('replies',$oldThread->get('replies')-1);
            $oldThread->save();


            $newBoard = $this->xpdo->getObject('disBoard',$newBoard->get('id'));
            if ($newBoard) {
                $newBoard->set('num_replies',$newBoard->get('num_replies')+1);
                $newBoard->set('total_posts',$newBoard->get('total_posts')+1);
                $newBoard->save();
            }
            $oldBoard = $this->xpdo->getObject('disBoard',$oldBoard->get('id'));
            if ($oldBoard) {
                $oldBoard->set('num_replies',$oldBoard->get('num_replies')-1);
                $oldBoard->set('total_posts',$oldBoard->get('total_posts')-1);
                $oldBoard->save();
            }

        }
        return true;
    }

    /**
     * Overrides xPDOObject::remove to handle closure tables, post counts, fix
     * the forum activity and user profile counts.
     *
     * @param array $ancestors
     * @param boolean $doBoardMoveChecks
     * @param boolean $moveToSpam
     * @return boolean
     */
    public function remove(array $ancestors = array(),$doBoardMoveChecks = false,$moveToSpam = false) {
        $removed = false;
        $moved = false;
        $canRemove = $this->xpdo->invokeEvent('OnDiscussPostBeforeRemove',array(
            'post' => &$this,
            'doBoardMoveChecks' => $doBoardMoveChecks,
            'moveToSpam' => $moveToSpam,
        ));
        if (!empty($canRemove)) {
            return false;
        }

        /** @var disUser $author */
        $author = $this->getOne('Author');
        /** @var disThread $thread */
        $thread = $this->xpdo->getObject('disThread',array('id' => $this->get('thread')));
        /** @var disBoard $board */
        $board = $this->xpdo->getObject('disBoard',array('id' => $this->get('board')));
        $isPrivateMessage = !$thread || $thread->get('private');

        /* first check to see if moving to spam/trash */
        if (!empty($doBoardMoveChecks) && !$isPrivateMessage && !empty($board)) {
            $isModerator = $board->isModerator($this->xpdo->discuss->user->get('id'));
            $isAdmin = $this->xpdo->discuss->user->isAdmin();
            if ($isAdmin || $isModerator || $this->xpdo->discuss->user->get('id') == $this->get('author')) { /* move to spambox/recyclebin */
                $spamBoard = $this->xpdo->getOption('discuss.spam_bucket_board',null,false);
                if ($moveToSpam && !empty($spamBoard) && $this->get('board') != $spamBoard) {
                    $removed = $this->move($spamBoard);
                    $moved = true;
                } else {
                    $trashBoard = $this->xpdo->getOption('discuss.recycle_bin_board',null,false);
                    if (!empty($trashBoard) && $this->get('board') != $trashBoard) {
                        $removed = $this->move($trashBoard);
                        $moved = true;
                    }
                }
            }
        }


        if (!$removed) {
            $removed = parent::remove($ancestors);
        }
        if ($removed) {
            $parent = $this->get('parent');

            if (empty($parent)) {
                /* get oldest post and make it the new root post for the thread */
                $c = $this->xpdo->newQuery('disPost');
                $c->where(array(
                    'id:!=' => $this->get('id'),
                    'board' => $this->get('board'),
                    'thread' => $this->get('thread')
                ));
                $c->sortby($this->xpdo->escape('createdon'), 'ASC');
                $c->limit(1);
                /* @var disPost $oldestPost */
                $oldestPost = $this->xpdo->getObject('disPost', $c);
                if ($oldestPost) {
                    $oldestPost->set('parent', 0);
                    if ($oldestPost->save()) {
                        $parent = $oldestPost->get('id');
                        $thread->set('post_first', $oldestPost->get('id'));
                        $thread->save();
                    }
                }
            }
            /* fix child posts' parent */
            foreach ($this->getMany('Children') as $child) {
                /* @var disPost $child */
                $child->set('parent', $parent);
                $child->save();
            }

            /* decrease profile posts */
            if ($author && !$isPrivateMessage) {
                $author->set('posts',($author->get('posts')-1));
                $author->save();
            }

            /* fix board last post */
            if ($board && !$isPrivateMessage) {
                $c = $this->xpdo->newQuery('disPost');
                $c->where(array(
                    'id:!=' => $this->get('id'),
                    'board' => $board->get('id'),
                ));
                $c->sortby('createdon','DESC');
                $c->limit(1);
                /** @var disPost $latestPost */
                $latestPost = $this->xpdo->getObject('disPost',$c);
                if ($latestPost) {
                    $board->set('last_post',$latestPost->get('id'));
                } else {
                    $board->set('last_post',0);
                }

                /* fix board total post/replies/topics counts */
                $board->set('total_posts',($board->get('total_posts')-1));
                if ($parent == 0) {
                    $board->set('num_topics',($board->get('num_topics')-1));
                } else {
                    $board->set('num_replies',($board->get('num_replies')-1));
                }
                $board->save();
            }

            /* fix thread posts/data */
            if ($thread) {
                $thread->set('replies',$thread->get('replies') - 1);
                $c = $this->xpdo->newQuery('disPost');
                $c->where(array(
                    'id:!=' => $this->get('id'),
                    'thread:=' => $thread->get('id'),
                ));
                $c->sortby('createdon','DESC');
                $c->limit(1);
                /** @var disPost $priorPost */
                $priorPost = $this->xpdo->getObject('disPost',$c);
                if ($priorPost) { /* set last post anew */
                    $thread->set('post_last',$priorPost->get('id'));
                    $thread->set('post_last_on',strtotime($priorPost->get('createdon')));
                    $thread->set('author_last',$priorPost->get('author'));

                    /* fix thread participants */
                    $thread->removeParticipant($this->get('author'),$this->get('id'));

                    $saved = $thread->save();
                } else { /* if no more posts, remove thread */
                    $thread->remove();
                }
            }
            
            /* adjust forum activity */
            if (!defined('DISCUSS_IMPORT_MODE') && !$isPrivateMessage) {
                $now = date('Y-m-d');
                /** @var disForumActivity $activity */
                $activity = $this->xpdo->getObject('disForumActivity',array(
                    'day' => $now,
                ));
                if ($activity) {
                    if ($this->get('parent') != 0) {
                        $activity->set('replies',$activity->get('replies')-1);
                    } else {
                        $activity->set('topics',$activity->get('topics')-1);
                    }
                    $activity->save();
                }
            }

            $this->removeFromIndex();

            $this->xpdo->invokeEvent('OnDiscussPostRemove',array(
                'post' => &$this,
                'thread' => &$thread,
                'doBoardMoveChecks' => $doBoardMoveChecks,
                'moveToSpam' => $moveToSpam,
                'moved' => $moved,
                'isPrivateMessage' => $isPrivateMessage,
            ));

            $this->clearCache();
        }
        return $removed;
    }

    /**
     * Load the Parsing class for the post
     * @return disParser
     */
    public function loadParser() {
        if (empty($this->parser)) {
            $parserClass = $this->xpdo->getOption('discuss.parser_class',null,'disBBCodeParser');
            $parserClassPath = $this->xpdo->getOption('discuss.parser_class_path');
            if (empty($parserClassPath)) {
                $parserClassPath = $this->xpdo->discuss->config['modelPath'].'discuss/parser/';
            }
            $this->parser = $this->xpdo->getService('disParser',$parserClass,$parserClassPath);
        }
        return $this->parser;
    }

    /**
     * Get the parsed content of this post
     * @return string The parsed content of the post
     */
    public function getContent() {
        $message = $this->get('message');
        
        /* Check custom content parser setting */
        if ($this->xpdo->getOption('discuss.use_custom_post_parser',null,false)) {
            /* Load custom parser */
            $parsed = $this->xpdo->invokeEvent('OnDiscussPostCustomParser', array(
                    'content' => &$message,
            ));
            if (is_array($parsed)) {
                foreach ($parsed as $msg) {
                    if (!empty($msg)) {
                        $message = $msg;
                    }
                }
            } else if (!empty($parsed)) {
                $message = $parsed;
            }
        } else if (true) {
            $this->loadParser();
            $message = $this->parser->parse($message);
        }

        /* Allow for plugin to change content of posts after it has been parsed */
        $rs = $this->xpdo->invokeEvent('OnDiscussPostFetchContent',array(
            'content' => &$message,
        ));

        if (is_array($rs)) {
            foreach ($rs as $msg) {
                if (!empty($msg)) {
                    $message = $msg;
                }
            }
        } else if (!empty($rs)) {
            $message = $rs;
        }

        $message = $this->stripBBCode($message);
        return $message;
    }

    /**
     * Strip any BBCode from the post
     * @param string $str The string to strip
     * @return mixed The cleansed content
     */
    public function stripBBCode($str) {
         $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
         $replace = '';
         return preg_replace($pattern, $replace, $str);
    }

    /**
     * Gets the Thread root post of this current Post; if this Post is the
     * thread root, returns itself.
     *
     * @access public
     * @return disPost
     */
    public function getThreadRoot() {
        if ($this->get('parent') == 0) {
            return $this;
        }
        $c = $this->xpdo->newQuery('disPost');
        $c->select($this->xpdo->getSelectColumns('disPost','disPost'));
        $c->select(array(
            'Author.username AS author_username',
            'Board.name AS board_name',
        ));
        $c->innerJoin('modUser','Author');
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disPostClosure','Ancestors');
        $c->innerJoin('disPost','Thread','Thread.id = Ancestors.ancestor');
        $c->where(array(
            'Ancestors.descendant' => $this->get('id'),
            'Ancestors.ancestor:!=' => $this->get('id'),
            'Thread.parent' => 0,
        ));
        $thread = $this->xpdo->getObject('disPost',$c);

        return $thread;
    }

    /**
     * Grabs all descendants of this post.
     *
     * @access public
     * @param int $depth If set, will limit to specified depth
     * @return array A collection of disPost objects.
     */
    public function getDescendants($depth = 0) {
        $c = $this->xpdo->newQuery('disPost');
        $c->select($this->xpdo->getSelectColumns('disPost','disPost'));
        $c->select(array(
            'Descendants.depth AS depth',
        ));
        $c->innerJoin('disPostClosure','Descendants');
        $c->innerJoin('disPostClosure','Ancestors');
        $c->where(array(
            'Descendants.ancestor' => $this->get('id'),
        ));
        if ($depth) {
            $c->where(array(
                'Descendants.depth:<=' => $depth,
            ));
        }
        $c->sortby('disPost.rank','ASC');
        return $this->xpdo->getCollection('disPost',$c);
    }

    /**
     * Gets the viewing message for the bottom of the thread
     *
     * @access public
     * @return string The who is viewing message
     */
    public function getViewing() {
        if (!$this->xpdo->getOption('discuss.show_whos_online',null,true)) return '';

        $c = $this->xpdo->newQuery('disSession');
        $c->innerJoin('modUser','User');
        $c->select($this->xpdo->getSelectColumns('disSession','disSession','',array('id')));
        $c->select(array(
            'GROUP_CONCAT(DISTINCT CONCAT_WS(":",User.id,User.username)) AS readers',
        ));
        $c->where(array(
            'disSession.place' => 'thread:'.$this->get('id'),
        ));
        $c->groupby('disSession.user');
        $members = $this->xpdo->getObject('disSession',$c);
        if ($members) {
            $readers = explode(',',$members->get('readers'));
            $readers = array_unique($readers);
            $members = array();
            foreach ($readers as $reader) {
                $r = explode(':',$reader);
                $members[] = '<a href="[[DiscussUrlMaker? &action=`user` &params=`'.$modx->toJSON(array('user' => str_replace('%20','',$r[0]))).'`]]">'.$r[1].'</a>';
            }
            $members = array_unique($members);
            $members = implode(',',$members);
        } else { $members = $this->xpdo->lexicon('discuss.zero_members'); }

        $c = $this->xpdo->newQuery('disSession');
        $c->where(array(
            'place' => 'thread:'.$this->get('id'),
            'user' => 0,
        ));
        $guests = $this->xpdo->getCount('disSession',$c);

        return $this->xpdo->lexicon('discuss.thread_viewing',array(
            'members' => $members,
            'guests' => $guests,
        ));
    }

    /**
     * Clears the cache on post save/remove
     *
     * @return void
     */
    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/post/'.$this->get('id'));
            $this->xpdo->cacheManager->delete('discuss/board/'.$this->get('board'));
            $this->xpdo->cacheManager->delete('discuss/board/'.$this->get('board').'/');
            $this->xpdo->cacheManager->delete('discuss/board/user/');
            $this->xpdo->cacheManager->delete('discuss/board/index/');
            $this->xpdo->cacheManager->delete('discuss/board/recent/');
            $this->xpdo->cacheManager->delete('discuss/recent/');
            $thread = $this->getOne('Thread');
            if ($thread) {
                $this->xpdo->cacheManager->delete('discuss/thread/'.$thread->get('id'));
            }
        }
    }

    /**
     * Clear all post caches
     *
     * @static
     * @param xPDO $xpdo A reference to the xPDO|modX instance
     * @return bool True if cleared
     */
    public static function clearAllCache(xPDO $xpdo) {
        $xpdo->getCacheManager();
        return $xpdo->cacheManager->delete('discuss/post/');
    }

    /**
     * Convert all BR tags to newlines
     * @param string $str The string to parse
     * @return string The converted content
     */
    public function br2nl($str) {
        return str_replace(array('<br>','<br />','<br/>'),"\n",$str);
    }

    /**
     * Get an array of all the ancestors of this Post's board
     * @return array
     */
    public function getBoardAncestors() {
        $c = $this->xpdo->newQuery('disBoard');
        $c->innerJoin('disBoardClosure','Ancestors');
        $c->where(array(
            'Ancestors.descendant' => $this->get('board'),
        ));
        $c->sortby('Ancestors.depth','ASC');
        return $this->xpdo->getCollection('disBoard',$c);
    }

    /**
     * Return if the active user can reply to this post
     * @return bool
     */
    public function canReply() {
        if ($this->xpdo->discuss->user->isAdmin()) return true;
        /** @var disThread $thread */
        $thread = $this->getOne('Thread');
        if (!$thread) return false;
        
        return $thread->canReply();
    }

    /**
     * Whether or not the user can report this post as spam
     *
     * @return boolean True if the active user can report this post as spam
     */
    public function canReport() {
        return $this->xpdo->discuss->user->isLoggedIn && $this->xpdo->hasPermission('discuss.thread_report');
    }

    /**
     * See if the active user can modify this post
     * @return boolean
     */
    public function canModify() {
        $canModify = $this->xpdo->discuss->user->isLoggedIn && $this->xpdo->hasPermission('discuss.thread_modify');
        $canModify = $this->xpdo->discuss->user->get('id') == $this->get('author') || ($this->isModerator() && $canModify);

        /** @var disThread $thread */
        $thread = $this->getOne('Thread');
        return $canModify || $thread->canModifyPost($this->get('id'));
    }

    /**
     * See if active user can remove this post
     * @return boolean
     */
    public function canRemove() {
        $canRemove = $this->xpdo->discuss->user->isLoggedIn && $this->xpdo->hasPermission('discuss.thread_remove');
        $canRemove = $this->xpdo->discuss->user->get('id') == $this->get('author') || ($this->isModerator() && $canRemove);

        /** @var disThread $thread */
        $thread = $this->getOne('Thread');
        return $canRemove || $thread->canRemovePost($this->get('id'));
    }

    /**
     * Return true if the active user is a moderator of the Post's Thread
     * @return boolean
     */
    public function isModerator() {
        /** @var disThread $thread */
        $thread = $this->getOne('Thread');
        return $thread->isModerator();
    }

    /**
     * Get the thread page that this post would be on
     *
     * @param boolean $last
     * @return int
     */
    public function getThreadPage($last = false) {
        $thread = $this->getOne('Thread');
        if (!$thread) return 1;

        $page = 1;
        $sortDir = $this->xpdo->getOption('discuss.post_sort_dir',null,'ASC');
        $replies = $thread->get('replies');
        $perPage = (int)$this->xpdo->getOption('discuss.post_per_page',null, 10);
        if ($replies >= $perPage) {
            $idx = $this->get('idx');
            if (empty($idx)) { /* if we're not in a list thread page, so no idx is calculated */
                $idx = $this->calculateIdx();
            }

            if ($sortDir == 'ASC') {
                $page = ceil($idx / $perPage);
            } else {
                $page = ceil(($replies - $idx) / $perPage);
                $page = $page < 1 ? 1 : $page;
            }
        }
        $this->set('page',$page);
        return $page;
    }

    /**
     * Calculate the position of this post in the thread when it is not known
     *
     * @return int The index position of the post in its thread
     */
    public function calculateIdx() {
        $sortDir = $this->xpdo->getOption('discuss.post_sort_dir',null,'ASC');
        $c = $this->xpdo->newQuery('disPost');
        $c->select($this->xpdo->getSelectColumns('disPost','disPost','',array('id')));
        $c->where(array(
            'thread' => $this->get('thread'),
        ));
        $c->sortby($this->xpdo->getSelectColumns('disPost','disPost','',array('createdon')),$sortDir);
        $c->prepare();
        $sql = $c->toSql();
        $stmt = $this->xpdo->query($sql);
        $i = 0;
        if ($stmt && $stmt instanceof PDOStatement) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                if ($row['id'] == $this->get('id')) break;
            }
            $stmt->closeCursor();
        }
        return $i;
    }

    /**
     * Get the URL of this post
     *
     * @param string $view
     * @param boolean $last
     * @return string
     */
    public function getUrl($action = 'thread',$last = false) {
        $params = array();
        $params['thread'] = $this->get('thread');
        $page = $this->getThreadPage($last);
        if ($page != 1) {
            $params['page'] = $page;
        }

        $thread = $this->getOne('Thread');
        if ($thread && $action == 'thread') {
            $params['thread_name'] = $thread->getUrlTitle();
        }

        $url = $this->xpdo->discuss->request->makeUrl($view,$params);
        $url .= '#dis-post-'.$this->get('id');
        $this->set('url',$url);
        return $url;
    }

    /**
     * @param array $postArray
     * @return
     */
    public function renderAuthorMeta(array &$postArray) {
        if (empty($this->Author)) {
            $this->getOne('Author');
            if (empty($this->Author)) {
                return;
            }
        }

        /** @var array $authorArray */
        $authorArray = $this->Author->toArray('author.');
        $postArray = array_merge($postArray,$authorArray);
        $postArray['author.signature'] = $this->Author->parseSignature();
        $postArray['author.posts'] = number_format($postArray['author.posts']);
        unset($postArray['author.password'],$postArray['author.cachepwd']);
        
        if ($this->xpdo->discuss->user->canViewProfiles()) {
            $postArray['author.username_link'] = '<a href="'.$this->Author->getUrl().'">'.$this->Author->get('name').'</a>';
        } else {
            $postArray['author.username_link'] = '<span class="dis-username">'.$this->Author->get('name').'</span>';
        }
        if ($this->Author->get('status') == disUser::BANNED) {
            $postArray['author.username_link'] .= '<span class="dis-banned">'.$this->xpdo->lexicon('discuss.banned').'</span>';
        }

        /* set author avatar */
        $avatarUrl = $this->Author->getAvatarUrl();
        if (!empty($avatarUrl)) {
            $postArray['author.avatar'] = '<img class="dis-post-avatar" alt="'.$postArray['author'].'" src="'.$avatarUrl.'" />';
        }

        /* check if author wants to show email */
        if ($this->Author->get('show_email') && $this->xpdo->discuss->user->canViewEmails()) {
            $this->loadParser();
            $postArray['author.email'] = call_user_func(array($this->parser,'encodeEmail'),$this->Author->get('email'),$this->xpdo->lexicon('discuss.email_author'));
        } else {
            $postArray['author.email'] = '';
        }

        /* get primary group badge/name, if applicable */
        $postArray['author.group_badge'] = $this->Author->getGroupBadge();
        $postArray['author.group_name'] = '';
        if (!empty($this->Author->PrimaryGroup)) {
            $postArray['author.group_name'] = $this->Author->PrimaryGroup->get('name');
        }
    }
}

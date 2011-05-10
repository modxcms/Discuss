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
 * @package discuss
 */
class disPost extends xPDOSimpleObject {
    /**
     * Overrides xPDOObject::save to handle closure table edits.
     *
     * TODO: add code for moving posts to different parents.
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
            $cl = $this->xpdo->newObject('disPostClosure');
            $cl->set('ancestor',$id);
            $cl->set('descendant',$id);
            if ($cl->save() === false) {
                $this->remove();
                return false;
            }

            /* create closures and calculate rank */
            $tableName = $this->xpdo->getTableName('disPostClosure');
            $c = $this->xpdo->newQuery('disPostClosure');
            $c->where(array(
                'descendant' => $parent,
                'ancestor:!=' => 0,
            ));
            $c->sortby('depth','DESC');
            $gparents = $this->xpdo->getCollection('disPostClosure',$c);
            $cgps = count($gparents);
            $gps = array();
            $i = $cgps;
            foreach ($gparents as $gparent) {
                $gps[] = str_pad($gparent->get('ancestor'),10,'0',STR_PAD_LEFT);
                $obj = $this->xpdo->newObject('disPostClosure');
                $obj->set('ancestor',$gparent->get('ancestor'));
                $obj->set('descendant',$id);
                $obj->set('depth',$i);
                $obj->save();
                $i--;
            }
            $gps[] = str_pad($id,10,'0',STR_PAD_LEFT); /* add self closure too */

            /* add root closure */
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

            $thread = $this->getOne('Thread');
            $privatePost = $this->get('private');

            /* set thread, update thread  */
            if (!defined('DISCUSS_IMPORT_MODE')) {
                if (!$thread) {
                    $thread = $this->xpdo->newObject('disThread');
                    $thread->fromArray(array(
                        'board' => $this->get('board'),
                        'post_first' => $this->get('id'),
                        'author_first' => $this->get('author'),
                        'replies' => 0,
                        'views' => 0,
                    ));
                }

                $thread->set('post_last',$this->get('id'));
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
        }
        if ($saved) {
            $this->index();

            /* clear cache */
            $this->clearCache();
        }
        return $saved;
    }

    public function index() {
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
            $postArray['message'] = $this->getContent();
            $indexed = $this->xpdo->discuss->search->index($postArray);
        }
        return $indexed;
    }

    public function removeFromIndex() {
        $removed = false;
        if ($this->xpdo->discuss->loadSearch()) {
            $removed = $this->xpdo->discuss->search->removeIndex($this->get('id'));
        }
        return $removed;
    }

    public function move($boardId) {
        /* check to see if only post in thread, if so, just move thread */
        $thread = $this->getOne('Thread');
        $newBoard = is_object($boardId) && $boardId instanceof disBoard ? $boardId : $this->xpdo->getObject('disBoard',$boardId);
        $oldBoard = $this->getOne('Board');
        if (!$thread || !$newBoard || !$oldBoard) return false;

        $postCount = $this->xpdo->getCount('disPost',array('thread' => $this->get('thread')));
        if ($postCount == 1) {
            return $thread->move($boardId);
        }

        /* is multiple posts in thread, so split post out and move new thread */
        $newThread = $this->xpdo->newObject('disThread');
        $newThread->fromArray($thread->toArray());
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
        $canRemove = $this->xpdo->invokeEvent('OnDiscussPostBeforeRemove',array(
            'post' => &$this,
            'doBoardMoveChecks' => $doBoardMoveChecks,
            'moveToSpam' => $moveToSpam,
        ));
        if (!empty($canRemove)) {
            return false;
        }

        /* first check to see if moving to spam/trash */
        if (!empty($doBoardMoveChecks) && !$this->get('private')) {
            $board = $this->getOne('Board');
            if (!empty($board)) {
                $isModerator = $board->isModerator($this->xpdo->discuss->user->get('id'));
                $isAdmin = $this->xpdo->discuss->user->isAdmin();
                if ($isAdmin || $isModerator) { /* move to spambox/recyclebin */
                    $spamBoard = $this->xpdo->getOption('discuss.spam_bucket_board',null,false);
                    if ($moveToSpam && !empty($spamBoard) && $this->get('board') != $spamBoard) {
                        $this->removeFromIndex();
                        return $this->move($spamBoard);
                    } else {
                        $trashBoard = $this->xpdo->getOption('discuss.recycle_bin_board',null,false);
                        if (!empty($trashBoard) && $this->get('board') != $trashBoard) {
                            $this->removeFromIndex();
                            return $this->move($trashBoard);
                        }
                    }
                } else {
                    return false;
                }
            }
        }

        $removed = parent::remove();
        if ($removed) {
            $author = $this->getOne('Author');
            $board = $this->getOne('Board');
            $parent = $this->get('parent');
            /* decrease profile posts */
            if ($author) {
                $author->set('posts',($author->get('posts')-1));
                $author->save();
            }

            /* fix board last post */
            if ($board) {
                $c = $this->xpdo->newQuery('disPost');
                $c->where(array(
                    'board' => $board->get('id'),
                ));
                $c->sortby('createdon','DESC');
                $c->limit(1);
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
            $thread = $this->getOne('Thread');
            if ($thread) {
                $thread->set('replies',$thread->get('replies') - 1);
                $c = $this->xpdo->newQuery('disPost');
                $c->where(array('id:!=' => $this->get('id')));
                $c->sortby('createdon','DESC');
                $c->limit(1);
                $priorPost = $this->xpdo->getObject('disPost',$c);
                if ($priorPost) { /* set last post anew */
                    $thread->set('post_last',$priorPost->get('id'));
                    $thread->set('author_last',$priorPost->get('author'));
                    $thread->save();
                } else { /* if no more posts, remove thread */
                    $thread->remove();
                }
            }
            
            /* adjust forum activity */
            if (!defined('DISCUSS_IMPORT_MODE')) {
                $now = date('Y-m-d');
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
            ));

            $this->clearCache();
        }
        return $removed;
    }

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
                $members[] = '<a href="[[~[[*id]]]]user/?user='.str_replace('%20','',$r[0]).'">'.$r[1].'</a>';
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

    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/post/'.$this->get('id'));
            $this->xpdo->cacheManager->delete('discuss/board/'.$this->get('board'));
            $thread = $this->getOne('Thread');
            if ($thread) {
                $this->xpdo->cacheManager->delete('discuss/thread/'.$thread->get('id'));
            }
        }
    }

    public static function clearAllCache(xPDO $xpdo) {
        $xpdo->getCacheManager();
        return $xpdo->cacheManager->delete('discuss/post/');
    }

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
        $thread = $this->getOne('Thread');
        if (!$thread) return false;
        
        return $thread->canReply();
    }

    /**
     * Get the thread page that this post would be on
     * @return int
     */
    public function getThreadPage() {
        $thread = $this->getOne('Thread');
        if (!$thread) return 1;

        $page = 1;
        $replies = $thread->get('replies');
        $perPage = $this->xpdo->getOption('discuss.post_per_page',null, 10);
        if ($replies > $perPage) {
            $page = ceil($replies / $perPage);
        }
        $this->set('page',$page);
        return $page;
    }

    /**
     * Get the URL of this post
     *
     * @param string $view
     * @return string
     */
    public function getUrl($view = 'thread/') {
        $url = $this->xpdo->discuss->url.$view.'?thread='.$this->get('thread');
        $page = $this->getThreadPage();
        if ($page != 1) {
            $url .= '&page='.$page;
        }
        $url .= '#dis-post-'.$this->get('id');
        $this->set('url',$url);
        
        return $url;
    }
}
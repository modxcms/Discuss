<?php
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
     * @TODO: add code for moving posts to different parents.
     *
     * {@inheritDoc}
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

            /* up author post count */
            $author = $this->getOne('Author');
            if ($author && !defined('DISCUSS_IMPORT_MODE')) {
                $author->set('posts',($author->get('posts')+1));
                $author->save();
            }

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

            /* adjust forum activity */
            if (!defined('DISCUSS_IMPORT_MODE')) {
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

            /* set thread, up thread  */
            if (!defined('DISCUSS_IMPORT_MODE')) {
                $thread = $this->getOne('Thread');
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

                $this->set('thread',$thread->get('id'));
                $this->save();

                $thread->set('post_last',$this->get('id'));
                $thread->set('author_last',$this->get('author'));
                $thread->save();
            }

            /* clear cache */
            $this->clearCache();
        }
        return $saved;
    }

    /**
     * Overrides xPDOObject::remove to handle closure tables, post counts, fix
     * the forum activity and user profile counts.
     *
     * {@inheritDoc}
     */
    public function remove(array $ancestors = array()) {
        $author = $this->getOne('Author');
        $board = $this->getOne('Board');
        $parent = $this->get('parent');

        $removed = parent::remove();
        if ($removed) {
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
                if ($priorPost) {
                    $thread->set('post_last',$priorPost->get('id'));
                    $thread->set('author_last',$priorPost->get('author'));
                    $thread->save();
                } else {
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

            $this->clearCache();
        }
        return $removed;
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
            //$message = str_replace(array('<br/>','<br />','<br>'),'',$message);
            //$message = $this->_nl2br2($message);
            $message = $this->parseBBCode($message);
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

    private function _nl2br2($str) {
        $str = str_replace("\r", '', $str);
        return preg_replace('/(?<!>)\n/', "<br />\n", $str);
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
            'author_username' => 'Author.username',
            'board_name' => 'Board.name',
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
            'depth' => 'Descendants.depth',
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

    /**
     * Parse BBCode in post and return proper HTML. Supports SMF/Vanilla formats.
     *
     * @param $message The string to parse
     * @return string The parsed string with HTML instead of BBCode, and all code stripped
     */
    public function parseBBCode($message) {
        /* handle quotes better, to allow for citing */
        $message = $this->parseQuote($message);
        
        /* parse bbcode from vanilla/smf boards bbcode formats */
        $message = preg_replace("#\[b\](.*?)\[/b\]#si",'<b>\\1</b>',$message);
        $message = preg_replace("#\[i\](.*?)\[/i\]#si",'<i>\\1</i>',$message);
        $message = preg_replace("#\[u\](.*?)\[/u\]#si",'<u>\\1</u>',$message);
        $message = preg_replace("#\[s\](.*?)\[/s\]#si",'<s>\\1</s>',$message);

        $message = preg_replace("#\[quote\](.*?)\[/quote\]#si",'<blockquote>\\1</blockquote>',$message);
        $message = preg_replace("#\[cite\](.*?)\[/cite\]#si",'<blockquote>\\1</blockquote>',$message);
        $message = preg_replace("#\[code\](.*?)\[/code\]#si",'<div class="dis-code"><h5>Code</h5><pre>\\1</pre></div>',$message);
        $message = preg_replace("#\[hide\](.*?)\[/hide\]#si",'\\1',$message);
        $message = preg_replace("#\[url\]([^/]*?)\[/url\]#si",'<a href="http://\\1">\\1</a>',$message);
        $message = preg_replace("#\[url\](.*?)\[/url\]#si",'\\1',$message);
        $message = preg_replace("#\[url=[\"']?(.*?)[\"']?\](.*?)\[/url\]#si",'<a href="\\1">\\2</a>',$message);
        $message = preg_replace("#\[php\](.*?)\[/php\]#si",'<code>\\1</code>',$message);
        $message = preg_replace("#\[mysql\](.*?)\[/mysql\]#si",'<code>\\1</code>',$message);
        $message = preg_replace("#\[css\](.*?)\[/css\]#si",'<code>\\1</code>',$message);
        $message = preg_replace("#\[img=[\"']?(.*?)[\"']?\](.*?)\[/img\]#si",'<img src="\\1" alt="\\2" />',$message);
        $message = preg_replace("#\[img\](.*?)\[/img\]#si",'<img src="\\1" border="0" />',$message);
        $message = str_ireplace(array('[indent]', '[/indent]'), array('<div class="Indent">', '</div>'), $message);

        $message = preg_replace("#\[font=[\"']?(.*?)[\"']?\]#i",'<span style="font-family:\\1;">',$message);
        $message = preg_replace("#\[color=[\"']?(.*?)[\"']?\]#i",'<span style="color:\\1">',$message);
        $message = str_ireplace(array("[/size]", "[/font]", "[/color]"), "</span>", $message);

        $message = preg_replace("#\[size=[\"']?(.*?)[\"']?\]#si",'<font size="\\1">',$message);
        $message = str_ireplace('[/font]', '</font>', $message);

        $message = preg_replace('#\[/?left\]#si', '', $message);

        /* strip all remaining bbcode */
        $message = $this->stripBBCode($message);
        /* strip MODX tags */
        $message = str_replace(array('[',']'),array('&#91;','&#93;'),$message);
        return $message;
    }

    /**
     * Parse a bbcode quote tag and return result
     *
     * @param $message The string to parse
     * @return string The quoted message
     */
    public function parseQuote($message) {
        preg_match_all("#\[quote=?(.*?)[\"']?\](.*?)\[/quote\]#si",$message,$matches);
        if (!empty($matches)) {
            $quotes = array();
            $replace = array();
            $meta = array();
            $with = array();
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) { $replace[] = $match; }
                foreach ($matches[1] as $match) { $meta[] = $match; }
                foreach ($matches[2] as $match) { $with[] = $match; }
            }
            for ($i=0;$i<count($replace);$i++) {
                $auth = array();
                $mt = explode(' ',$meta[$i]);
                foreach ($mt as $m) {
                    if (empty($m)) continue;
                    $m = explode('=',$m);
                    switch ($m[0]) {
                        case 'author': $auth['user'] = $m[1]; break;
                        case 'date': $auth['date'] = $m[1]; break;
                        case 'link': $auth['link'] = $m[1]; break;
                    }
                }
                $cite = '';
                if (!empty($auth['user']) || !empty($auth['date'])) {
                    $cite = '<cite>Quote';
                    if (!empty($auth['user'])) $cite .= ' from: '.$auth['user'];
                    if (!empty($auth['date'])) $cite .= ' at '.strftime($this->xpdo->discuss->dateFormat,$auth['date']);
                    $cite .= '</cite>';
                }

                /* strip annoying starting br tags */
                $with[$i] = substr($with[$i],0,6) == '<br />' ? $with[$i] = substr($with[$i],6) : $with[$i];

                /* now insert our quote */
                $message = str_replace($replace[$i],$cite.'<blockquote>'.$with[$i].'</blockquote>',$message);
            }
        }
        return $message;
    }
}
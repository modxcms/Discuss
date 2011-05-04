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

            /* clear cache */
            $this->clearCache();
        }
        return $saved;
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
        /* first check to see if moving to spam/trash */
        if (!empty($doBoardMoveChecks) && !$this->get('private')) {
            $board = $this->getOne('Board');
            if (!empty($board)) {
                $isModerator = $board->isModerator($this->xpdo->discuss->user->get('id'));
                $isAdmin = $this->xpdo->discuss->user->isAdmin();
                if ($isAdmin || $isModerator) { /* move to spambox/recyclebin */
                    $spamBoard = $this->xpdo->getOption('discuss.spam_bucket_board',null,false);
                    if ($moveToSpam && !empty($spamBoard)) {
                        return $this->move($spamBoard);
                    } else {
                        $trashBoard = $this->xpdo->getOption('discuss.recycle_bin_board',null,false);
                        if (!empty($trashBoard)) {
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
        $message = preg_replace("#\[b\](.*?)\[/b\]#si",'<strong>\\1</strong>',$message);
        $message = preg_replace("#\[i\](.*?)\[/i\]#si",'<em>\\1</em>',$message);
        $message = preg_replace("#\[u\](.*?)\[/u\]#si",'<span style="text-decoration: underline;">\\1</span>',$message);
        $message = preg_replace("#\[s\](.*?)\[/s\]#si",'<del>\\1</del>',$message);
        $message = str_ireplace("[hr]",'<hr />',$message);
        $message = preg_replace("#\[sup\](.*?)\[/sup\]#si",'<sup>\\1</sup>',$message);
        $message = preg_replace("#\[sub\](.*?)\[/sub\]#si",'<sub>\\1</sub>',$message);
        $message = preg_replace("#\[ryan\](.*?)\[/ryan\]#si",'<blink>\\1</blink>',$message);
        $message = preg_replace("#\[tt\](.*?)\[/tt\]#si",'<tt>\\1</tt>',$message);
        $message = preg_replace("#\[rtl\](.*?)\[/rtl\]#si",'<div dir="rtl">\\1</div>',$message);


        /* align tags */
        $message = preg_replace("#\[center\](.*?)\[/center\]#si",'<div style="text-align: center;">\\1</div>',$message);
        $message = preg_replace("#\[right\](.*?)\[/right\]#si",'<div style="text-align: right;">\\1</div>',$message);
        $message = preg_replace("#\[left\](.*?)\[/left\]#si",'<div style="text-align: left;">\\1</div>',$message);


        $message = preg_replace("#\[quote\](.*?)\[/quote\]#si",'<blockquote>\\1</blockquote>',$message);
        $message = preg_replace("#\[cite\](.*?)\[/cite\]#si",'<blockquote>\\1</blockquote>',$message);
        $message = preg_replace("#\[hide\](.*?)\[/hide\]#si",'\\1',$message);
        $message = preg_replace_callback("#\[email\]([^/]*?)\[/email\]#si",array('disPost','parseEmailCallback'),$message);
        $message = preg_replace("#\[url\]([^/]*?)\[/url\]#si",'<a href="http://\\1">\\1</a>',$message);
        $message = preg_replace("#\[url\](.*?)\[/url\]#si",'\\1',$message);
        $message = preg_replace("#\[magic\](.*?)\[/magic\]#si",'<marquee>\\1</marquee>',$message);
        $message = preg_replace("#\[url=[\"']?(.*?)[\"']?\](.*?)\[/url\]#si",'<a href="\\1">\\2</a>',$message);
        $message = preg_replace("#\[php\](.*?)\[/php\]#si",'<pre class="brush:php">\\1</pre>',$message);
        $message = preg_replace("#\[mysql\](.*?)\[/mysql\]#si",'<pre class="brush:sql">\\1</pre>',$message);
        $message = preg_replace("#\[css\](.*?)\[/css\]#si",'<pre class="brush:css">\\1</pre>',$message);
        $message = preg_replace("#\[pre\](.*?)\[/pre\]#si",'<pre>\\1</pre>',$message);
        $message = preg_replace("#\[img=[\"']?(.*?)[\"']?\](.*?)\[/img\]#si",'<img src="\\1" alt="\\2" />',$message);
        $message = preg_replace("#\[img\](.*?)\[/img\]#si",'<img src="\\1" border="0" />',$message);
        $message = str_ireplace(array('[indent]', '[/indent]'), array('<div class="Indent">', '</div>'), $message);

        $message = preg_replace("#\[font=[\"']?(.*?)[\"']?\]#i",'<span style="font-family:\\1;">',$message);
        $message = preg_replace("#\[color=[\"']?(.*?)[\"']?\]#i",'<span style="color:\\1;">',$message);
        $message = preg_replace("#\[size=[\"']?(.*?)[\"']?\]#si",'<span style="font-size:\\1;">',$message);
        $message = str_ireplace(array("[/size]", "[/font]", "[/color]"), "</span>", $message);

        $message = preg_replace('#\[/?left\]#si', '', $message);

        /* convert [list]/[li] tags */
        $message = preg_replace("#\[li\](.*?)\[/li\]#si",'<li>\\1</li>',$message);
        $message = preg_replace_callback("#\[list\](.*?)\[/list\]#si",array('disPost','parseListCallback'),$message);

        /* auto-convert links */
        $message = preg_replace_callback("/(?<!<a href=\")(?<!\")(?<!\">)((?:https?|ftp):\/\/)([\@a-z0-9\x21\x23-\x27\x2a-\x2e\x3a\x3b\/;\x3f-\x7a\x7e\x3d]+)/msxi",array('disPost', 'parseLinksCallback'),$message);

        /* auto-add br tags to linebreaks for pretty formatting */
        $message = $this->_nl2br2($message);

        /* now do code blocks where we dont want linebreaks, so they can strip them out */
        $message = preg_replace_callback("#\[code\](.*?)\[/code\]#si",array('disPost','parseCodeCallback'),$message);
        $message = preg_replace_callback("#\[code=[\"']?(.*?)[\"']?\](.*?)\[/code\]#si",array('disPost','parseCodeSpecificCallback'),$message);
        $message = preg_replace('#\[/?code\]#si', '', $message);

        $message = $this->parseSmileys($message);
        
        /* strip all remaining bbcode */
        //$message = $this->stripBBCode($message);
        /* strip MODX tags */
        $message = str_replace(array('[',']'),array('&#91;','&#93;'),$message);
        return $message;
    }

    public static function parseCodeCallback($matches) {
        $code = disPost::stripBRTags($matches[1]);
        return '<div class="dis-code"><pre class="brush: php; toolbar: false">'.$code.'</pre></div>';
    }
    public static function parseCodeSpecificCallback($matches) {
        $type = !empty($matches[1]) ? $matches[1] : 'php';
        $availableTypes = array('applescript','actionscript3','as3','bash','shell','coldfusion','cf','cpp','c','c#','c-sharp','csharp','css','delphi','pascal','diff','patch','pas','erl','erlang','groovy','java','jfx','javafx','js','jscript','javascript','perl','pl','php','text','plain','py','python','ruby','rails','ror','rb','sass','scss','scala','sql','vb','vbnet','xml','xhtml','xslt','html');
        if (!in_array($type,$availableTypes)) $type = 'php';
        $code = disPost::stripBRTags($matches[2]);
        return '<div class="dis-code"><pre class="brush: '.$type.'; toolbar: false">'.$code.'</pre></div>';
    }

    public static function parseLinksCallback($matches) {
        $url = $matches[1].$matches[2];
        $noFollow = ' rel="nofollow"';
        return '<a href="'.$url.'" target="_blank"'.$noFollow.'>'.$url.'</a>';
    }

    public static function parseListCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',disPost::stripBRTags($matches[1]));
        $message = '<ul style="margin-top:0;margin-bottom:0;">'.$message.'</ul>';
        return $message;
    }

    public static function parseEmailCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',$matches[1]);
        return disPost::encodeEmail($message);
    }

    public static function stripBRTags($str) {
        return str_replace(array('<br>','<br />','<br/>'),'',$str);
    }

    public static function encodeEmail($email,$emailText = '') {
        $email = disPost::obfuscate($email);
        if (empty($emailText)) {
            $emailText = str_replace(array('&#64;','@'),'<em>&#64;</em>',$email);
        }
        return '<a href="mailto:'.$email.'" rel="nofollow">'.$emailText.'</a>';
    }
    
    public static function obfuscate($text) {
        $result = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $j = rand(0, 1);
            if ($j) {
                $result .= substr($text, $i, 1);
            } else {
                $k = rand(0, 1);
                if ($k) {
                    $result .= '&#' . ord(substr($text, $i, 1)) . ';';
                } else {
                    $result .= '&#x' . sprintf("%x", ord(substr($text, $i, 1))) . ';';
                }
            }
        }
        $k = rand(0, 1);
        if ($k) {
            return str_replace('@', '&#64;', $result);
        } else {
            return str_replace('@', '&#x40;', $result);
        }
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

    public function parseSmileys($message) {
        $imagesUrl = $this->xpdo->discuss->config['imagesUrl'].'smileys/';
        $smiley = array(
            '::)' => 'rolleyes',
            ':)' => 'smiley',
            ';)' => 'wink',
            ':D' => 'laugh',
            ';D' => 'grin',
            '>>:(' => 'angry2',
            '>:(' => 'angry',
            ':(' => 'sad',
            ':o' => 'shocked',
            '8)' => 'cool',
            '???' => 'huh',
            ':P' => 'tongue',
            ':-[' => 'embarrassed',
            ':-X' => 'lipsrsealed',
            ':-*' => 'kiss',
            ':-\\' => 'undecided',
            ":'(" => 'cry',
            '[hug]' => 'bear2',
            '[brew]' => 'brew',
            '[ryan2]' => 'ryan2',
            '[locke]' => 'locke',
            '[zelda]' => 'zelda',
            '[surrender]' => 'surrender',
            '[ninja]' => 'ninja',
            '[spam]' => 'spam',
            '[welcome]' => 'welcome',
            '[offtopic]' => 'offtopic',
            '[hijack]' => 'hijack',
            '[helpme]' => 'help',
            '[banned]' => 'banned',
        );
        $v = array_values($smiley);
        for ($i =0; $i < count($v); $i++) {
            $v[$i] = '<img src="'.$imagesUrl.$v[$i].'.gif" alt="" />';
        }
        return str_replace(array_keys($smiley),$v,$message);
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


    public function canReply() {
        if ($this->xpdo->discuss->user->isAdmin()) return true;
        $thread = $this->getOne('Thread');
        if (!$thread) return false;
        
        return $thread->canReply();
    }
}
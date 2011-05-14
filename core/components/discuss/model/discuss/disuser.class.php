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
 * @package discuss
 */
class disUser extends xPDOSimpleObject {
    const INACTIVE = 0;
    const ACTIVE = 1;
    const UNCONFIRMED = 2;
    const BANNED = 3;
    const AWAITING_MODERATION = 4;

    public $isAdmin;
    public $isGlobalModerator;
    public $isLoggedIn = false;
    public $readThreads = array();

    public function init() {
        $this->isLoggedIn = true;
        
        /* get cache of read threads */
        $this->prepareReadThreads();

        /* active user, update the disUser record */
        $this->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
        $this->set('ip',$this->xpdo->discuss->getIp());
        $this->save();

        return true;
    }

    /**
     * Prepare a cache of all read thread IDs for this user
     * 
     * @return array
     */
    public function prepareReadThreads() {
        $this->xpdo->exec('SET SESSION group_concat_max_len = 1000000');
        $stmt = $this->xpdo->query('SELECT GROUP_CONCAT(`thread`) AS `threads` FROM '.$this->xpdo->getTableName('disThreadRead').' WHERE `user` = '.$this->get('id').' GROUP BY `user`');
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($row)) {
                $this->readThreads = explode(',',$row['threads']);
            }
            sort($this->readThreads);
        }
        $this->xpdo->exec('SET SESSION group_concat_max_len = 6000');
        return $this->readThreads;
    }

    /**
     * Gets the avatar URL for this user, depending on the avatar service.
     * @return string
     */
    public function getAvatarUrl() {
        $avatarUrl = '';

        $avatarService = $this->get('avatar_service');
        $avatar = $this->get('avatar');
        if (!empty($avatar) || !empty($avatarService)) {
            if (!empty($avatarService)) {
                if ($avatarService == 'gravatar') {
                    $avatarUrl = $this->xpdo->getOption('discuss.gravatar_url',null,'http://www.gravatar.com/avatar/').md5($this->get('email'));
                    $avatarUrl .= '?d='.$this->xpdo->getOption('discuss.gravatar_default',null,'mm');
                    $avatarUrl .= '&r='.$this->xpdo->getOption('discuss.gravatar_rating',null,'g');
                }
            } else {
                $avatarUrl = $this->xpdo->getOption('discuss.files_url').'/profile/'.$this->get('user').'/'.$this->get('avatar');
            }
        }
        return $avatarUrl;
    }

    /**
     * Parse and return the signature for this user
     * @return string
     */
    public function parseSignature() {
        $message = $this->get('signature');
        if (!empty($message)) {
            $message = str_replace(array('&#91;','&#93;'),array('[',']'),$message);

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
                $message = $this->_nl2br2($message);
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
        }
        return $message;
    }

    /**
     * Convert all linebreaks to br tags
     * @param string $str
     * @return mixed
     */
    private function _nl2br2($str) {
        $str = str_replace("\r", '', $str);
        return preg_replace('/(?<!>)\n/', "<br />\n", $str);
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
        $message = preg_replace('#\[/?left\]#si', '', $message);

        /* strip all remaining bbcode */
        $message = $this->stripBBCode($message);
        /* strip MODX tags */
        $message = str_replace(array('[',']'),array('&#91;','&#93;'),$message);
        return $message;
    }

    /**
     * Strip BBCode from a string
     * 
     * @param $str
     * @return mixed
     */
    public function stripBBCode($str) {
         $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
         $replace = '';
         return preg_replace($pattern, $replace, $str);
    }
    
    /**
     * Get a count of # of unread messages for the user
     * 
     * @return int
     */
    public function countUnreadMessages() {
        $c = $this->xpdo->newQuery('disThread');
        $c->innerJoin('disThreadUser','Users');
        $c->leftJoin('disThreadRead','Reads','Reads.user = '.$this->get('id').' AND disThread.id = Reads.thread');
        $c->where(array(
            'disThread.private' => true,
            'Users.user' => $this->get('id'),
            'Reads.thread:IS' => null,
        ));
        return $this->xpdo->getCount('disThread',$c);
    }

    /**
     * Clear the cache for this User
     * 
     * @return void
     */
    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/user/'.$this->get('id'));
            $this->xpdo->cacheManager->delete('discuss/board/user/'.$this->get('id'));
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

    /**
     * Get user groups for the active user
     *
     * @return array An array of user groups
     */
    public function getUserGroups() {
        $groups = array();
        $this->getOne('User');
        if ($this->User) {
            $groups = $this->User->getUserGroups();
            $this->isAdmin();
        }
        return $groups;
    }

    /**
     * Return whether or not the user is an Administrator
     * @return boolean
     */
    public function isAdmin() {
        if (!$this->isLoggedIn) {
            $this->isAdmin = false;
        }
        if (!isset($this->isAdmin)) {
            $this->isAdmin = false;
            $adminGroups = $this->xpdo->getOption('discuss.admin_groups',null,'');
            $adminGroups = explode(',',$adminGroups);
            $level = 9999;
            if ($this->xpdo->user->isMember($adminGroups)) {
                $this->isAdmin = true;
            }
        }
        return $this->isAdmin;
    }

    /**
     * Return whether or not the user is a global Moderator
     * @return boolean
     */
    public function isGlobalModerator() {
        if (!$this->isLoggedIn) {
            $this->isGlobalModerator = false;
        }
        if (!isset($this->isGlobalModerator)) {
            $this->isGlobalModerator = false;
            $moderators = $this->xpdo->getOption('discuss.global_moderators',null,'');
            $moderators = explode(',',$moderators);
            if (in_array($this->xpdo->user->get('username'),$moderators)) {
                $this->isGlobalModerator = true;
            }
        }
        return $this->isGlobalModerator;
    }

    /**
     * Fetch a list of active users in the forum
     *
     * @static
     * @param xPDO $modx A reference to the modX object
     * @param int $timeAgo If set, will grab within X seconds
     * @param int $limit Limit results to this number
     * @param int $start Start at this index
     * @return array A response array of active users
     */
    public static function fetchActive(xPDO &$modx,$timeAgo = 0,$limit = 0,$start = 0) {
        $response = array();

        $c = $modx->newQuery('disUser');
        $c->innerJoin('disSession','Session',$modx->getSelectColumns('disSession','Session','',array('user')).' = '.$modx->getSelectColumns('disUser','disUser','',array('id')));
        $c->innerJoin('modUser','User');
        $c->leftJoin('disUserGroupProfile','PrimaryDiscussGroup');
        if (!empty($timeAgo)) {
            $c->where(array(
                'Session.access:>=' => $timeAgo,
            ));
        }
        if (!empty($limit)) {
            $c->limit($limit,$start);
        }
        $response['total'] = $modx->getCount('disUser',$c);
        $c->select(array(
            'disUser.id',
            'disUser.username',
            'PrimaryDiscussGroup.color',
        ));
        $c->groupby('disUser.id');
        $c->sortby('Session.access','ASC');
        $response['results'] = $modx->getCollection('disUser',$c);
        return $response;
    }

    /**
     * Return the last visited thread by the User
     * @return disThread
     */
    public function getLastVisitedThread() {
        $c = $this->xpdo->newQuery('disThread');
        $c->innerJoin('disBoard','Board');
        $c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
        $groups = $this->xpdo->discuss->user->getUserGroups();
        if (!empty($groups) && !$this->xpdo->discuss->user->isAdmin()) {
            /* restrict boards by user group if applicable */
            $g = array(
                'UserGroups.usergroup:IN' => $groups,
            );
            $g['OR:UserGroups.usergroup:='] = null;
            $where[] = $g;
            $c->andCondition($where,null,2);
        }
        $c->where(array(
            'Board.status:!=' => disBoard::STATUS_INACTIVE,
            'id' => $this->get('thread_last_visited'),
        ));
        return $this->xpdo->getObject('disThread',$c);
    }

    /**
     * Check to see if the user qualifies for any post-based groups
     * and if so, grant it to them
     * 
     * @return bool
     */
    public function checkForPostGroupAdvance() {
        $joined = false;
        $c = $this->xpdo->newQuery('disUserGroupProfile');
        $c->innerJoin('modUserGroup','UserGroup');
        $c->where(array(
            'post_based' => true,
            'min_posts:<=' => $this->get('posts'),
        ));
        $postGroups = $this->xpdo->getCollection('disUserGroupProfile',$c);
        if (!empty($postGroups)) {
            $joined = true;
            $user = $this->getOne('User');
            foreach ($postGroups as $group) {
                $user->joinGroup($group->get('usergroup'));
            }
        }
        return $joined;
    }

    /**
     * Merge another user into this account
     *
     * @param disUser $oldUser
     * @return boolean
     */
    public function merge(disUser &$oldUser) {
        $success = true;
        $user = $this->getOne('User');
        if (empty($user)) return false;

        $oldModxUser = $oldUser->getOne('User');
        if (empty($oldModxUser)) return false;
        
        $this->xpdo->beginTransaction();

        /* merge post count */
        $posts = $user->get('posts');
        $posts = $posts + $oldUser->get('posts');
        $this->set('posts',$posts);

        /* merge ignore boards */
        $ibs = $this->get('ignore_boards');
        $ibs = explode(',',$ibs);
        $oldIbs = $oldUser->get('ignore_boards');
        $oldIbs = explode(',',$oldIbs);
        $ibs = array_merge($oldIbs,$ibs);
        $this->set('ignore_boards',implode(',',$ibs));

        /* merge signature if needed */
        $signature = $this->get('signature');
        $oldSignature = $oldUser->get('signature');
        if (empty($signature) && !empty($oldSignature)) {
            $this->set('signature',$oldSignature);
        }

        /* merge title if needed */
        $title = $this->get('title');
        $oldTitle = $oldUser->get('title');
        if (empty($title) && !empty($oldTitle)) {
            $this->set('title',$oldTitle);
        }

        /* merge primary_group if needed */
        $pg = $this->get('primary_group');
        $oldPg = $oldUser->get('primary_group');
        if (empty($pg) && !empty($oldPg)) {
            $this->set('primary_group',$oldPg);
        }

        $this->set('integrated_id',$oldUser->get('integrated_id'));
        $this->set('synced',true);
        $this->set('syncedat',$this->xpdo->discuss->now());

        $this->save();

        /* grant old usergroups to this user */
        $oldUserGroups = $this->xpdo->getCollection('modUserGroupMember',array('member' => $oldModxUser->get('id')));
        $ugs = array();
        foreach ($oldUserGroups as $oldUserGroup) {
            $ugs[] = $oldUserGroup->get('user_group');
        }
        $ugs = array_unique($ugs);
        foreach ($ugs as $ug) {
            $user->joinGroup($ug);
        }
        
        /* merge in posts, change authors */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disPost').'
            SET `author` = '.$this->get('id').'
            WHERE `author` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThread').'
            SET `author_first` = '.$this->get('id').'
            WHERE `author_first` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThread').'
            SET `author_last` = '.$this->get('id').'
            WHERE `author_last` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disThreadRead */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThreadRead').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disThreadUser */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThreadUser').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);


        /* merge in disUserFriend */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disUserFriend').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);
        $sql = 'UPDATE '.$this->xpdo->getTableName('disUserFriend').'
            SET `friend` = '.$this->get('id').'
            WHERE `friend` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disUserNotification */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disUserNotification').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disModerator */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disModerator').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* remove old user sessions */
        $sql = 'DELETE FROM '.$this->xpdo->getTableName('disUserFriend').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge all PMs users fields for user */
        $c = $this->xpdo->newQuery('disThread');
        $c->innerJoin('disThreadUser','Users');
        $c->leftJoin('disThreadRead','Reads','Reads.user = '.$oldUser->get('id').' AND disThread.id = Reads.thread');
        $c->where(array(
            'disThread.private' => true,
            'Users.user' => $oldUser->get('id'),
        ));
        $pms = $this->xpdo->getCollection('disThread',$c);
        foreach ($pms as $pm) {
            $users = $pm->get('users');
            $users = explode(',',$users);
            $users = array_diff($users,array($oldUser->get('id')));
            $users[] = $this->get('id');
            $pm->set('users',implode(',',$users));
            $pm->save();
        }

        /* remove old users */
        $oldUser->remove();
        $oldModxUser->remove();

        /* check for post group advance */
        $this->checkForPostGroupAdvance();

        $this->xpdo->commit();
        return $success;
    }

    /**
     * Gets the badge for the Primary Group for this user
     *
     * @return string
     */
    public function getGroupBadge() {
        $badge = '';
        if (!$this->PrimaryDiscussGroup) {
            $this->getOne('PrimaryDiscussGroup');
        }
        if ($this->PrimaryDiscussGroup) {
            $badge = $this->PrimaryDiscussGroup->getBadge();
        }
        return $badge;
    }

    /**
     * Gets a User Setting (bypasses cache) 
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getSetting($key,$default = '') {
        $setting = $this->xpdo->getObject('modUserSetting',array(
            'key' => $key,
            'user' => $this->xpdo->user->get('id'),
        ));
        if ($setting) {
            $default = $setting->get('value');
        }
        return $default;
    }

    /**
     * Set a custom User Setting for this user
     *
     * @param string $key The key of the Setting
     * @param string $value The value to set
     * @param string $default The default value of the setting
     * @return bool True if successfully updated
     */
    public function setSetting($key,$value,$default = null) {
        $saved = false;
        $setting = $this->xpdo->getObject('modUserSetting',array(
            'key' => $key,
            'user' => $this->xpdo->user->get('id'),
        ));
        if ($setting) {
            if ($value == $default) {
                $setting->remove();
            } else {
                $setting->set('value',$value);
                $saved = $setting->save();
            }
        } else if ($value != $default) {
            $sysSetting = $this->xpdo->getObject('modSystemSetting',array(
                'key' => $key,
            ));
            $setting = $this->xpdo->newObject('modUserSetting');
            $setting->set('user',$this->xpdo->user->get('id'));
            $setting->set('key',$key);
            $setting->set('value',$value);
            $setting->set('namespace','discuss');
            if ($sysSetting) {
                $setting->set('xtype',$sysSetting->get('xtype'));
                $setting->set('area',$sysSetting->get('area'));
            } else {
                $setting->set('xtype','textfield');
                $setting->set('area','General');
            }
            $saved = $setting->save();
        }
        if ($saved) {
            $this->xpdo->reloadConfig();
        }
        return $saved;
    }
}
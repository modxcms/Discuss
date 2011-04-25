<?php
/**
 * @package discuss
 */
class disThread extends xPDOSimpleObject {


    public function remove(array $ancestors = array()) {
        $removed = parent::remove($ancestors);
        $this->clearCache();
        return $removed;
    }

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
            'GROUP_CONCAT(DISTINCT CONCAT_WS(":",User.id,User.username)) AS readers',
        ));
        $c->where(array(
            'disSession.place' => $placePrefix.':'.$this->get('id'),
        ));
        $c->groupby('disSession.user');
        $members = $this->xpdo->getObject('disSession',$c);
        if ($members) {
            $readers = explode(',',$members->get('readers'));
            $readers = array_unique($readers);
            $members = array();
            foreach ($readers as $reader) {
                $r = explode(':',$reader);
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

    public function stick() {
        $this->set('sticky',true);
        return $this->save();
    }

    public function unstick() {
        $this->set('sticky',false);
        return $this->save();
    }

    public function lock() {
        $this->set('locked',true);
        return $this->save();
    }

    public function unlock() {
        $this->set('locked',false);
        return $this->save();
    }


    public function hasSubscription($userId) {
        return $this->xpdo->getCount('disUserNotification',array(
            'user' => $userId,
            'thread' => $this->get('id'),
        )) > 0;
    }

    public function addSubscription($userId) {
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
            }
        }
        return true;
    }

    public function removeSubscription($userId) {
        $notify = $this->xpdo->getObject('disUserNotification',array(
            'user' => $userId,
            'thread' => $this->get('id'),
        ));
        if ($notify) {
            if (!$notify->remove()) {
                $this->xpdo->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not remove notification: '.print_r($notify->toArray(),true));
            }
        }
        return true;
    }

    public function isModerator($userId) {
        $moderator = $this->xpdo->getCount('disModerator',array(
            'user' => $userId,
            'board' => $this->get('board'),
        ));
        return $moderator > 0;
    }


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
}
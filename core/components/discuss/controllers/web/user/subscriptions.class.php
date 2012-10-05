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
 * @subpackage controllers
 */
/**
 * Edit a user's subscriptions to threads
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussUserSubscriptionsController extends DiscussController {

    public function initialize() {
        $this->modx->lexicon->load('discuss:user');
    }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.user_subscriptions_header',array('user' => $this->discuss->user->get('username')));
    }
    public function getSessionPlace() {
        return 'user/subscriptions';
    }
    public function process() {
        $this->setPlaceholders($this->discuss->user->toArray());
        $this->getSubscriptions();
        $this->getMenu();

    }

    public function handleActions() {
        /* handle unsubscribing */
        if (!empty($_POST) && !empty($this->scriptProperties['remove'])) {
            foreach ($this->scriptProperties['remove'] as $threadId) {
                /** @var disUserNotification $notification */
                $notification = $this->modx->getObject('disUserNotification',array('thread' => $threadId));
                if ($notification == null) continue;
                $notification->remove();
            }
            $url = $this->discuss->request->makeUrl(array('action' => 'user', 'user' => 'subscriptions'));
            $this->modx->sendRedirect($url);
        }
    }

    /**
     * Get all subscriptions to the User's thread
     * @return void
     */
    public function getSubscriptions() {
        $tpl = $this->getOption('subscriptionTpl','user/disUserSubscriptionRow');
        $rowCls = $this->getOption('rowCls','dis-board-li');
        $dateFormat = $this->getOption('dateFormat',$this->discuss->dateFormat);
        $rowSeparator = $this->getOption('rowSeparator',"\n");

        $c = $this->modx->newQuery('disThread');
        $c->select($this->modx->getSelectColumns('disThread','disThread'));
        $c->select(array(
            'board_name' => 'Board.name',
            'last_post_id' => 'LastPost.id',
            'createdon' => 'LastPost.createdon',
            'first_post_id' => 'FirstPost.id',
            'title' => 'FirstPost.title',
            'author' => 'FirstPost.author',
            'author_username' => 'FirstAuthor.username',
        ));
        $c->innerJoin('disUserNotification','Notifications');
        $c->innerJoin('disUser','FirstAuthor');
        $c->innerJoin('disPost','FirstPost');
        $c->innerJoin('disPost','LastPost');
        $c->leftJoin('disBoard','Board');
        $c->where(array(
            'Notifications.user' => $this->discuss->user->get('id'),
            'disThread.private' => false,
        ));
        $c->sortby('FirstPost.title','ASC');
        $subscriptions = $this->modx->getCollection('disThread',$c);
        $subs = array();
        /** @var disThread $subscription */
        foreach ($subscriptions as $subscription) {
            $subscriptionArray = $subscription->toArray('',true);
            $subscriptionArray['url'] = $subscription->getUrl();
            $subscriptionArray['class'] = $rowCls;
            $subscriptionArray['createdon'] = strftime($dateFormat,strtotime($subscriptionArray['createdon']));
            $subs[] = $this->discuss->getChunk($tpl,$subscriptionArray);
        }
        $this->setPlaceholder('subscriptions',implode($rowSeparator,$subs));
    }

    /**
     * Get the user menu on the left-hand side
     * @return void
     */
    public function getMenu() {
        $menuTpl = $this->getProperty('menuTpl','disUserMenu');
        $this->setPlaceholder('usermenu',$this->discuss->getChunk($menuTpl,$this->getPlaceholders()));
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );

        $trail[] = array(
            'text' => $this->modx->lexicon('discuss.user.trail',array('user' => $this->discuss->user->get('username'))),
            'url' => $this->discuss->request->makeUrl(array('action' => 'user'))
        );

        $trail[] = array('text' => $this->modx->lexicon('discuss.subscriptions'),'active' => true);
        return $trail;
    }
}

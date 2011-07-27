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
 * Display a thread of posts
 * @package discuss
 */
class DiscussMessagesViewController extends DiscussController {
    /** @var disThread $thread */
    public $thread;
    /** @var array $list */
    public $list = array();

    public function initialize() {
        $thread = $this->getProperty('thread',false);
        if (empty($thread)) $this->discuss->sendErrorPage();
        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$thread,disThread::TYPE_MESSAGE));
        if (empty($this->thread)) $this->modx->sendErrorPage();
        $this->modx->lexicon->load('discuss:post');
    }

    public function checkPermissions() {
        /* ensure user is IN this PM */
        $users = explode(',',$this->thread->get('users'));
        $inMessage = in_array($this->discuss->user->get('id'),$users);
        return $this->discuss->user->isLoggedIn && $inMessage;
    }
    public function getPageTitle() {
        return $this->thread->get('title');
    }
    public function getSessionPlace() {
        return 'message:'.$this->thread->get('id');
    }

    public function process() {
        $this->view();

        $this->thread->buildCssClass();
        $this->thread->buildCssClass();
        $threadArray = $this->thread->toArray();
        $threadArray['views'] = number_format($threadArray['views']);
        $threadArray['replies'] = number_format($threadArray['replies']);
        $this->setPlaceholders($threadArray);

        $this->getThreadPosts();
        $this->getViewing();

        $this->getActionButtons();
        $this->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('Error'));
        $this->setPlaceholder('discuss.thread',$this->thread->get('title'));
    }

    /**
     * Get all the readers of this thread
     * @return void
     */
    public function getViewing() {
        $this->setPlaceholder('readers',$this->thread->getViewing('message'));
    }

    public function getActionButtons() {
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) {
            if ($this->modx->hasPermission('discuss.pm_send')) {
                $actionButtons[] = array('url' => $this->discuss->request->makeUrl('messages/reply',array('thread' => $this->thread->get('id'))), 'text' => $this->modx->lexicon('discuss.reply_to_message'));
            }
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('messages/view',array('thread' => $this->thread->get('id'),'unread' => 1)), 'text' => $this->modx->lexicon('discuss.mark_unread'));
            if ($this->modx->hasPermission('discuss.pm_remove')) {
                $actionButtons[] = array('url' => $this->discuss->request->makeUrl('messages/remove',array('thread' => $this->thread->get('id'))), 'text' => $this->modx->lexicon('discuss.message_remove'));
            }
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    /**
     * Return all posts for this thread
     * @return void
     */
    public function getThreadPosts() {
        $this->list = $this->discuss->hooks->load('message/get',array(
            'thread' => &$this->thread,
        ));
        $this->setPlaceholder('posts',$this->list['results']);
    }

    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array(
            'count' => $this->list['total'],
            'id' => $this->thread->get('id'),
            'view' => 'messages/view',
            'limit' => $this->list['limit'],
        ));
    }

    /**
     * Mark this thread as viewed
     * @return boolean
     */
    public function view() {
        return $this->thread->view();
    }

    /**
     * Mark the thread as read
     * @return boolean
     */
    public function markAsRead() {
        return $this->thread->read($this->discuss->user->get('id'));
    }

    public function handleActions() {
        /* handle actions */
        if (!empty($this->scriptProperties['unread'])) {
            if ($this->thread->unread($this->discuss->user->get('id'))) {
                $this->modx->sendRedirect($this->discuss->request->makeUrl('messages'));
            }
        }
    }

    public function getBreadcrumbs() {
        return $this->thread->buildBreadcrumbs(array(array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        ),array(
            'url' => $this->discuss->request->makeUrl('messages'),
            'text' => $this->modx->lexicon('discuss.messages'),
        )));
    }
}
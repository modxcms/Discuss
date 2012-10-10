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
    /** @var disPost $lastPost */
    public $lastPost;

    public function initialize() {
        $thread = $this->getProperty('thread',false);
        if (empty($thread)) $this->discuss->sendErrorPage();
        /* Ensure disThread is loaded to prevent issue when not logged in */
        if (!class_exists('disThread')) $this->modx->loadClass('discuss.disThread', $this->discuss->config['modelPath']);
        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$thread,disThread::TYPE_MESSAGE));
        if (empty($this->thread)) {
            if ($this->discuss->user->isLoggedIn) $this->modx->sendErrorPage();
            else $this->discuss->sendUnauthorizedPage();
        }
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
        return 'messages/view:thread='.
            (($this->thread instanceof disThread) ? $this->thread->get('id') : $this->getProperty('thread',0)).
            ':'.$this->getProperty('page',1);
    }

    public function process() {
        $this->view();

        $this->thread->buildCssClass();
        $this->getQuickReplyForm();
        
        $threadArray = $this->thread->toArray();
        $threadArray['url'] = $this->thread->getUrl(false);
        $threadArray['views'] = number_format($threadArray['views']);
        $threadArray['replies'] = number_format($threadArray['replies']);
        $this->setPlaceholders($threadArray);

        $this->getThreadPosts();
        $this->getLastPost();
        $this->getViewing();

        $this->getActionButtons();
        $this->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('Error'));
        $this->setPlaceholder('discuss.thread',$this->thread->get('title'));
    }


    /**
     * Get the Quick Reply form
     * @return string
     */
    public function getQuickReplyForm() {
        $form = '';
        if ($this->canQuickReply()) {
            $this->handleAttachments();
            $this->getQuickReplyButtons();
            $phs = $this->getPlaceholders();
            $phs['view'] = 'messages/reply';
            $form = $this->discuss->getChunk('post/disQuickReply',$phs);
        }
        $this->setPlaceholder('quick_reply_form',$form);
        return $form;
    }

    /**
     * Check to see if user has access to quick reply
     * @return boolean
     */
    public function canQuickReply() {
        $canReply = $this->thread->canReply() && $this->discuss->user->isLoggedIn;
        $this->setPlaceholder('can_reply',$canReply);
        return $canReply;
    }

    /**
     * Loads the quick reply wysiwyg buttons for the form
     * @return string
     */
    public function getQuickReplyButtons() {
        $buttonsTpl = $this->getOption('buttonsTpl','disPostButtons');
        $buttons = $this->discuss->getChunk($buttonsTpl,array('buttons_url' => $this->discuss->config['imagesUrl'].'buttons/'));
        $this->setPlaceholder('reply_buttons',$buttons);
        return $buttons;
    }

    /**
     * Get the last post of the thread and set it as a placeholder
     * @return void
     */
    public function getLastPost() {
        $this->lastPost = $this->thread->getOne('LastPost');
        $lastPostArray = $this->lastPost->toArray('lastPost.');
        $this->setPlaceholders($lastPostArray);
    }

    /**
     * Handle the rendering and options of attachments being sent to the form
     * @return void
     */
    public function handleAttachments() {
        $this->setPlaceholder('max_attachments',$this->modx->getOption('discuss.attachments_max_per_post',null,5));
        if ($this->thread->canPostAttachments()) {
            $this->setPlaceholder('attachment_fields',$this->discuss->getChunk('post/disAttachmentFields',$this->getPlaceholders()));
        } else {
            $this->setPlaceholder('attachment_fields','');
        }
        $this->modx->regClientHTMLBlock('<script type="text/javascript">
        DIS.config.attachments_max_per_post = '.$this->getPlaceholder('max_attachments').';
        </script>');
    }
    
    /**
     * Get all the readers of this thread
     * @return void
     */
    public function getViewing() {
        $this->setPlaceholder('readers',$this->thread->getViewing('message'));
    }

    public function getActionButtons() {
        $links = array();
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) {
            if ($this->modx->hasPermission('discuss.pm_send')) {
                $links['actionlink_reply'] = $this->discuss->request->makeUrl('messages/reply',array('thread' => $this->thread->get('id')));
                $actionButtons[] = array('url' => $links['actionlink_reply'], 'text' => $this->modx->lexicon('discuss.reply_to_message'), 'cls' => 'dis-action-reply_to_message');
            }
            $links['actionlink_unread'] = $this->discuss->request->makeUrl('messages/view',array('thread' => $this->thread->get('id'),'unread' => 1));
            $actionButtons[] = array('url' => $links['actionlink_unread'], 'text' => $this->modx->lexicon('discuss.mark_unread'), 'cls' => 'dis-action-mark_unread');
            if ($this->modx->hasPermission('discuss.pm_remove')) {
                $links['actionlink_remove'] = $this->discuss->request->makeUrl('messages/remove',array('thread' => $this->thread->get('id')));
                $actionButtons[] = array('url' => $links['actionlink_remove'], 'text' => $this->modx->lexicon('discuss.message_remove'), 'cls' => 'dis-action-message_remove');
            }
        }
        $this->setPlaceholders($links);
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
        $this->discuss->hooks->load('pagination/build',array_merge(array(
            'count' => $this->list['total'],
            'id' => $this->thread->get('id'),
            'view' => 'messages/view',
            'limit' => $this->list['limit'],
            'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
        ), $this->options));
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

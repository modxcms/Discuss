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
 * Remove Thread page
 * 
 * @package discuss
 */
class DiscussMessagesRemovePostController extends DiscussController {
    /** @var disThread $thread */
    public $thread;
    /** @var disPost $post */
    public $post;

    public function initialize() {
        $post = $this->getProperty('post',false);
        $this->post = $this->modx->getObject('disPost',$post);
        if (empty($this->post)) $this->discuss->sendErrorPage();

        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$this->post->get('thread'),disThread::TYPE_MESSAGE));
        if (empty($this->thread)) $this->discuss->sendErrorPage();
    }

    public function checkPermissions() {
        $users = explode(',',$this->thread->get('users'));
        $inMessage = in_array($this->discuss->user->get('id'),$users);
        return $inMessage && $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.remove_message_header',array('title' => $this->thread->get('title')));
    }
    public function getSessionPlace() {
        return 'message-post-remove:'.$this->thread->get('id');
    }
    public function process() {
        $this->setPlaceholders($this->thread->toArray());
        if ($this->post->remove()) {
            $this->discuss->logActivity('message_post_remove',$this->post->toArray(),$this->post->getUrl());

            $posts = $this->thread->getMany('Posts');
            if (count($posts) <= 0) {
                $url = $this->discuss->request->makeUrl('messages');
            } else {
                $url = $this->discuss->request->makeUrl('messages/view',array('thread' => $this->thread->get('id')));
            }
            $this->modx->sendRedirect($url);
        }
    }
}
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
 * Mark Post as Spam page
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussPostSpamController extends DiscussController {
    /** @var disPost $post */
    public $post;
    /** @var disThread $thread */
    public $thread;

    public function initialize() {
        /** @var int|boolean $post */
        $post = $this->getProperty('post',false);
        if (empty($post)) $this->discuss->sendErrorPage();

        $this->post = $this->modx->getObject('disPost',$post);
        if (empty($this->post)) $this->discuss->sendErrorPage();

        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$this->post->get('thread')));
        if (empty($this->thread)) { $this->discuss->sendErrorPage(); }
    }
    public function getSessionPlace() { return ''; }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.post_spam_header',array('title' => $this->post->get('title')));
    }

    public function checkPermissions() {
        $isModerator = $this->thread->isModerator();
        $canRemovePost = $this->discuss->user->get('id') == $this->post->get('author') || $isModerator;
        if (!$canRemovePost) {
            return $this->thread->getUrl();
        }
        return true;
    }

    public function process() {
        if (!$this->post->remove(array(),true,true)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not remove post: '.print_r($this->post->toArray(),true));
        } else {
            $this->discuss->logActivity('post_spam_remove',$this->post->toArray(),$this->post->getUrl());
        }

        if ($this->thread->get('post_first') == $this->post->get('id')) {
            $redirectTo = $this->discuss->request->makeUrl(array('action' => 'board'),array('board' => $this->post->get('board')));
        } else {
            $redirectTo = $this->thread->getUrl();
        }
        $this->modx->sendRedirect($redirectTo);
    }
}
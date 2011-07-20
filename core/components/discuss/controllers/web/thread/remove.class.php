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
 * @subpackage controllers
 */
class DiscussThreadRemoveController extends DiscussController {
    /** @var disThread $thread */
    public $thread;

    public function getPageTitle() {
        return $this->modx->lexicon('discuss.remove_thread_header',array('title' => $this->thread->get('title')));
    }
    public function getSessionPlace() { return ''; }
    public function initialize() {
        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$this->scriptProperties['thread']));
        if (empty($this->thread)) $this->discuss->sendErrorPage();
    }

    public function checkPermissions() {
        if (!$this->thread->canRemove()) {
            return $this->thread->getUrl();
        }
        return true;
    }

    public function process() {
        $this->setPlaceholders($this->thread->toArray());
        $this->setPlaceholder('url',$this->thread->getUrl());
        $this->modx->setPlaceholder('discuss.thread',$this->thread->get('title'));
    }

    public function getBreadcrumbs() {
        return $this->thread->buildBreadcrumbs();
    }

    public function handleActions() {
        if (!empty($this->scriptProperties['remove-thread'])) {
            if ($this->thread->remove(array(),true)) {
                $this->discuss->logActivity('thread_remove',$this->thread->toArray(),$this->thread->getUrl());

                $url = $this->discuss->request->makeUrl('board',array('board' => $this->thread->get('board')));
                $this->modx->sendRedirect($url);
            }
        }
    }
}
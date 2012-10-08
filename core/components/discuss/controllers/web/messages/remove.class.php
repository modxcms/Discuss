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
 * Remove Private Message page
 * 
 * @package discuss
 * @subpackage controllers
 */
class DiscussMessagesRemoveController extends DiscussController {
    /** @var disThread $thread */
    public $thread;

    public function initialize() {
        /* get thread root */
        $c = $this->modx->newQuery('disThread');
        $c->innerJoin('disPost','FirstPost');
        $c->select($this->modx->getSelectColumns('disThread','disThread'));
        $c->select(array(
            'FirstPost.title',
            '(SELECT GROUP_CONCAT(pAuthor.id)
                FROM '.$this->modx->getTableName('disPost').' AS pPost
                INNER JOIN '.$this->modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
                WHERE pPost.thread = disThread.id
             ) AS participants',
        ));
        $c->where(array('id' => $this->getProperty('thread',false)));
        $this->thread = $this->modx->getObject('disThread',$c);
        if (empty($this->thread)) $this->discuss->sendErrorPage();
        $this->modx->lexicon->load('discuss:post');
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
        return 'messages/remove:thread='.$this->thread->get('id');
    }
    public function process() {
        $this->setPlaceholders($this->thread->toArray());
        $this->modx->setPlaceholder('discuss.thread',$this->thread->get('title'));
    }
    public function getBreadcrumbs() {
        return $this->thread->buildBreadcrumbs();
    }

    public function handleActions() {
        /* process form */
        if (!empty($this->scriptProperties['remove-message'])) {
            if ($this->thread->remove()) {
                /* log activity */
                $this->discuss->logActivity('message_thread_remove',$this->thread->toArray(),$this->thread->getUrl());

                $url = $this->discuss->request->makeUrl('messages');
                $this->modx->sendRedirect($url);
            }
        }

    }
}

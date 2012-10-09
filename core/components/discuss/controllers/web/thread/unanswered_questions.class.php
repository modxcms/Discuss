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
 * Get all unanswered Q&A threads
 *
 * @package discuss
 * @subpackage controllers
 */
require_once dirname(__FILE__).'/unread.class.php';
class DiscussThreadUnansweredQuestionsController extends DiscussThreadUnreadController {
    public $method = 'fetchUnansweredQuestions';

    public function getPageTitle() {
        return $this->modx->lexicon('discuss.unanswered_questions') . ' ('.number_format($this->threads['total']).')';
    }

    public function getSessionPlace() {
        return 'thread/unanswered_questions::'.$this->getProperty('page',1);
    }

    /**
     * Build the pagination for this view
     * @return void
     */
    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array_merge(array(
            'count' => $this->threads['total'],
            'id' => 0,
            'view' => 'thread/unanswered_questions',
            'limit' => $this->threads['limit'],
        ), $this->options));
    }

    /**
     * Get the action buttons for this view
     * @return void
     */
    public function getActionButtons() {
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) {
            //$actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/new_replies_to_posts',array('read' => 1)), 'text' => $this->modx->lexicon('discuss.mark_all_as_read'), 'cls' => 'dis-action-mark_all_as_read');
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.unanswered_questions').' ('.number_format($this->threads['total']).')','active' => true);
        return $trail;
    }

    public function handleActions() { }
}

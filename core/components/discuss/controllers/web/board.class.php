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
 * Displays the Board
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussBoardController extends DiscussController {
    /** @var disBoard $board */
    public $board;

    
    public function initialize() {
        /* get board */
        if (empty($this->scriptProperties['board'])) $this->discuss->sendErrorPage();
        $integrated = $this->getProperty('i',false);
        if (!empty($integrated)) $integrated = true;
        $this->board = $this->modx->call('disBoard','fetch',array(&$this->modx,$this->scriptProperties['board'],$integrated));
        if ($this->board == null) $this->discuss->sendErrorPage();
    }
    public function getSessionPlace() {
        return 'board:'.$this->board->get('id');
    }
    public function getPageTitle() {
        return $this->board->get('name');
    }

    public function handleActions() {
        /* add user to board readers */
        if ($this->getProperty('read',false) && $this->discuss->user->isLoggedIn) {
            $this->board->read($this->discuss->user->get('id'));
        }
    }

    
    public function process() {
        $this->setPlaceholders($this->board->toArray());

        /* grab all subboards */
        if (!empty($this->options['showSubBoards'])) {
            $this->getSubBoards();
        }

        /* get all threads in board */
        $limit = $this->getProperty('limit',$this->modx->getOption('discuss.threads_per_page',null,20));
        $start = $this->getProperty('start',0);
        if (empty($start)) {
            $page = $this->getProperty('page',1);
            $page = $page <= 0 ? 1 : $page;
            $start = ($page-1) * $limit;
        }

        if (!empty($this->options['showPosts'])) {
            $c = array(
                'limit' => $limit,
                'start' => $start,
                'board' => &$this->board,
            );
            $posts = $this->discuss->hooks->load('board/post/getlist',$c);
            $this->setPlaceholder('posts',implode("\n",$posts['results']));
            $this->discuss->config['pa'] = $posts['total'];
        }


        /* get viewing users */
        if (!empty($this->options['showReaders'])) {
            $this->setPlaceholder('readers',$this->board->getViewing());
        }

        /* get pagination */
        $this->discuss->hooks->load('pagination/build',array(
            'count' => !empty($posts) ? $posts['total'] : 0,
            'id' => $this->board->get('id'),
            'view' => 'board',
            'limit' => $limit,
            'param' => $this->modx->getOption('discuss.page_param',$this->scriptProperties,'page'),
        ));

        /* get moderators */
        if (!empty($this->options['showModerators'])) {
            $this->setPlaceholder('moderators',$this->board->getModeratorsList());
        }

        /* action buttons */
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) {
            if ($this->modx->hasPermission('discuss.thread_create') && $this->board->canPost()) {
                $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/new',array('board' => $this->board->get('id'))), 'text' => $this->modx->lexicon('discuss.thread_new'));
            }
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('board',array('board' => $this->board->get('id'),'read' => 1)), 'text' => $this->modx->lexicon('discuss.mark_all_as_read'));
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
        unset($actionButtons);

        /* Render board event */
        $this->setPlaceholders(array(
            'top' => '',
            'bottom' => '',
            'aboveThreads' => '',
            'belowBoards' => '',
            'belowThreads' => '',
        ));
        $eventOutput = $this->discuss->invokeRenderEvent('OnDiscussRenderBoard',$this->getPlaceholders());
        if (!empty($eventOutput)) {
            $this->setPlaceholders($eventOutput);
        }
        $this->setPlaceholder('discuss.board',$this->board->get('name'));
    }

    public function getBreadcrumbs() {
        return $this->board->buildBreadcrumbs();
    }

    public function getSubBoards() {
        $boards = $this->discuss->hooks->load('board/getlist',array(
            'board' => &$this->board,
        ));
        if (!empty($boards)) {
            $this->setPlaceholder('boards',$boards);
        } else {
            $this->setPlaceholder('boards_toggle','display:none;');
        }
    }
}
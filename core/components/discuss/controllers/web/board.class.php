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
 * Displays the Board
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussBoardController extends DiscussController {
    /** @var disBoard $board */
    public $board;
    /** @var array $list */
    public $list = array();

    
    public function initialize() {
        $board = $this->getProperty('board',false);
        if (empty($board)) $this->discuss->sendErrorPage();
        $integrated = $this->getProperty('i',false);
        if (!empty($integrated)) $integrated = true;
        $this->board = $this->modx->call('disBoard','fetch',array(&$this->modx,$board,$integrated));
        if ($this->board == null) $this->discuss->sendErrorPage();
        $this->setOptions();
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function getDefaultOptions() {
        return array(
            'tpl' => 'post/disBoardPost',
            'mode' => 'post',
            'get_category_name' => false,
            'lastPostTpl' => 'board/disLastPostBy',
        );
    }
    
    /**
     * {@inheritdoc}
     * @return array
     */
    public function getSessionPlace() {
        return 'board:'.$this->board->get('id').':'.(int)$this->getProperty('page',1);
    }
    /**
     * {@inheritdoc}
     * @return array
     */
    public function getPageTitle() {
        return $this->board->get('name');
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function handleActions() {
        /* add user to board readers */
        if ($this->getProperty('read',false) && $this->discuss->user->isLoggedIn) {
            $this->board->read($this->discuss->user->get('id'));
        }
    }

    /**
     * {@inheritdoc}
     * @return array
     */
    public function process() {
        $this->setPlaceholders($this->board->toArray());

        /* grab all subboards */
        if (!empty($this->options['showSubBoards'])) {
            $this->getSubBoards();
        }

        $this->getThreads();
        $this->buildPagination();

        if (!empty($this->options['showReaders'])) {
            $this->getViewing();
        }
        if (!empty($this->options['showModerators'])) {
            $this->getModerators();
        }
        $this->getActionButtons();
        $this->onRenderBoard();
        $this->setPlaceholder('discuss.board',$this->board->get('name'));
    }

    /**
     * Get all threads in board
     */
    public function getThreads() {
        $limit = $this->getProperty('limit',$this->modx->getOption('discuss.threads_per_page',null,20));
        $start = $this->getProperty('start',0);
        if (empty($start)) {
            $page = (int)$this->getProperty('page',1);
            $page = $page <= 0 ? 1 : $page;
            $start = ($page-1) * $limit;
        }
        if ($this->getOption('showPosts',true)) {
            $c = array_merge($this->options,array(
                'limit' => $limit,
                'start' => $start,
                'board' => &$this->board,
            ));
            $this->list = $this->discuss->hooks->load('board/post/getlist',$c);
            $this->setPlaceholder('posts',implode("\n",$this->list['results']));
        }
    }

    /**
     * Get a list of moderators for this board
     * @return void
     */
    public function getModerators() {
        $this->setPlaceholder('moderators',$this->board->getModeratorsList());
    }

    /**
     * {@inheritdoc}
     */
    public function getViewing() {
        $this->setPlaceholder('readers',$this->board->getViewing());
    }

    /**
     * Build the pagination for this grid
     * @return void
     */
    public function buildPagination() {
        $pagination = $this->discuss->hooks->load('pagination/build',array(
            'count' => !empty($this->list) ? $this->list['total'] : 0,
            'id' => $this->board->get('id'),
            'view' => 'board',
            'limit' => $this->list['limit'],
            'param' => $this->modx->getOption('discuss.page_param',$this->scriptProperties,'page'),
            'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
        ));
        $this->setPlaceholder('pagination',$pagination);
    }

    public function getActionButtons() {
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) {
            if ($this->modx->hasPermission('discuss.thread_create') && $this->board->canPost()) {
                $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/new',array('board' => $this->board->get('id'))), 'text' => $this->modx->lexicon('discuss.thread_new'), 'cls' => 'dis-action-thread_new');
            }
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('board',array('board' => $this->board->get('id'),'read' => 1)), 'text' => $this->modx->lexicon('discuss.mark_all_as_read'), 'cls' => 'dis-action-mark_all_as_read');
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function onRenderBoard() {
        $this->setPlaceholders(array(
            'top' => '',
            'bottom' => '',
            'aboveBoards' => '',
            'aboveThreads' => '',
            'belowBoards' => '',
            'belowThreads' => '',
        ));
        $eventOutput = $this->discuss->invokeRenderEvent('OnDiscussRenderBoard',$this->getPlaceholders());
        if (!empty($eventOutput)) {
            $this->setPlaceholders($eventOutput);
        }
    }

    /**
     * @return array
     */
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

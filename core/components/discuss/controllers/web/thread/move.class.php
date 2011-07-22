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
 * Move Thread page
 * 
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadMoveController extends DiscussController {
    /** @var disThread $thread */
    public $thread;
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.move_thread_header',array('title' => $this->thread->get('title')));
    }
    public function getSessionPlace() { return ''; }
    
    public function initialize() {
        $thread = $this->getProperty('thread',false);
        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$thread));
        if (empty($this->thread)) $this->discuss->sendErrorPage();
    }

    public function getDefaultOptions() {
        return array(
            'tplBoardOption' => 'board/disBoardOpt',
        );
    }

    /**
     * Ensure user can move this thread
     * 
     * @return boolean|string
     */
    public function checkPermissions() {
        if (!$this->thread->canMove()) {
            return $this->thread->getUrl();
        }
        return true;
    }

    /**
     * Process the page
     * @return void
     */
    public function process() {
        $this->setPlaceholders($this->thread->toArray());
        $this->setPlaceholder('url',$this->thread->getUrl());
        $this->getBoardList();

        /* output */
        $this->modx->setPlaceholder('discuss.thread',$this->thread->get('title'));
    }

    /**
     * Get the board dropdown list
     * @return void
     */
    public function getBoardList() {
        $boards = $this->modx->call('disBoard','fetchList',array(&$this->modx));
        $boardOutput = array();
        foreach ($boards as $board) {
            $board['selected'] = !empty($this->scriptProperties['board']) && $this->scriptProperties['board'] == $board['id'] ? ' selected="selected"' : '';
            $board['name'] = str_repeat('--',$board['depth']-1).$board['name'];
            $boardOutput[] = $this->discuss->getChunk($this->getOption('tplBoardOption'),$board);
        }
        $this->setPlaceholder('boards',implode("\n",$boardOutput));
    }

    /**
     * @return array|string
     */
    public function getBreadcrumbs() {
        return $this->thread->buildBreadcrumbs();
    }

    /**
     * Process the form
     * @return void
     */
    public function handleActions() {
        if (!empty($this->scriptProperties['move-thread']) && !empty($this->scriptProperties['board'])) {
            if ($this->thread->move($this->getProperty('board'))) {
                $this->discuss->logActivity('thread_move',$this->thread->toArray());

                $url = $this->discuss->request->makeUrl('board',array('board' => $this->thread->get('board')));
                $this->modx->sendRedirect($url);
            }
        }
    }
}

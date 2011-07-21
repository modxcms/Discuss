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
 * Renders the ignore boards form
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussUserIgnoreboardsController extends DiscussController {
    public function initialize() {
        $this->modx->lexicon->load('discuss:user');
    }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.user_ignore_boards_header',array('user' => $this->discuss->user->get('username')));
    }
    public function getSessionPlace() {
        return 'user-ignore-boards:'.$this->discuss->user->get('id');
    }
    
    public function process() {
        $this->setPlaceholders($this->discuss->user->toArray());

        $this->getBoards();
        $this->getMenu();
    }

    
    public function handleActions() {
        /* handle ignoring */
        if (!empty($_POST) && !empty($this->scriptProperties['boards'])) {
            $ignores = array();
            foreach ($this->scriptProperties['boards'] as $board) {
                $ignores[] = $board;
            }
            $ignores = array_unique($ignores);
            sort($ignores);
            $this->discuss->user->set('ignore_boards',implode(',',$ignores));
            if ($this->discuss->user->save()) {
                $this->discuss->user->clearCache();
                $url = $this->discuss->request->makeUrl('user/ignoreboards');
                $this->modx->sendRedirect($url);
            }
        }
    }

    /**
     * Get the list of boards to be able to ignore
     * @return void
     */
    public function getBoards() {
        /* build query */
        $boards = $this->modx->call('disBoard','fetchList',array(&$this->modx,false));

        /* now loop through boards */
        $list = array();
        $currentCategory = 0;
        $rowClass = 'even';
        $boardList = array();
        $categoryIgnoreList = array('checked' => array(),'all' => array());
        $ignores = $this->discuss->user->get('ignore_boards');
        $ignores = explode(',',$ignores);
        $idx = 0;
        foreach ($boards as $board) {
            $boardArray = $board;
            /* get current category */
            $currentCategory = $board['category'];
            if (!isset($lastCategory)) {
                $lastCategory = $board['category'];
                $lastCategoryName = $board['category_name'];
            }

            $boardArray['cls'] = 'dis-board-cb '.$rowClass;
            if (in_array($boardArray['id'],$ignores)) {
                $boardArray['checked'] = 'checked="checked"';
                $categoryIgnoreList['checked'][] = $boardArray['id'];
            }
            $categoryIgnoreList['all'][] = $boardArray['id'];

            if ($currentCategory != $lastCategory) {
                $ba['list'] = implode("\n",$boardList);
                unset($ba['rowClass']);
                $ba['checked'] = (count($categoryIgnoreList['all'])-1 == count($categoryIgnoreList['checked'])) ? ' checked="checked"' : '';
                if (empty($ba['category_name'])) $ba['category_name'] = $lastCategoryName;
                $list[] = $this->discuss->getChunk('board/disBoardCategoryIb',$ba);
                $categoryIgnoreList = array('checked' => array(),'all' => array());

                $ba = $boardArray;
                $boardList = array(); /* reset current category board list */
                $ba['cls'] = 'dis-board-cb '.$rowClass;
                $lastCategory = $board['category'];
                $boardList[] = $this->discuss->getChunk('board/disBoardCheckbox',$ba);

            } else { /* otherwise add to temp board list */
                if ($boardArray['depth'] - 1 > 0) {
                    $boardArray['name'] = str_repeat('---',$boardArray['depth'] - 1).$boardArray['name'];
                }
                $boardList[] = $this->discuss->getChunk('board/disBoardCheckbox',$boardArray);
                $rowClass = ($rowClass == 'alt') ? 'even' : 'alt';
            }
            $idx++;
        }
        
        if (count($boards) > 0) {
            if (in_array($boardArray['id'],$ignores)) {
                $boardArray['checked'] = 'checked="checked"';
            }
            /* Last category */
            $boardArray['list'] = implode("\n",$boardList);
            $boardArray['rowClass'] = 'dis-board-cb '.$rowClass;
            $list[] = $this->discuss->getChunk('board/disBoardCategoryIb',$boardArray);
        }
        $this->setPlaceholder('boards',implode("\n",$list));
    }

    /**
     * Get the user menu on the left-hand side
     * @return void
     */
    public function getMenu() {
        $menuTpl = $this->getProperty('menuTpl','disUserMenu');
        $this->setPlaceholder('usermenu',$this->discuss->getChunk($menuTpl,$this->getPlaceholders()));
    }
}
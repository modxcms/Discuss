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
 * Renders the ignore boards form
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussUserIgnoreboardsController extends DiscussController {
    public $boards = array();
    public $categoryMap = array();

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
        return 'user/ignoreboards';
    }

    public function process() {
        $this->setPlaceholders($this->discuss->user->toArray());

        $this->getBoards();
        $this->prepareBoards();
        $this->getMenu();
    }


    public function handleActions() {
        /* handle ignoring */
        if (!empty($_POST)) {
            if (!empty($this->scriptProperties['boards'])) {
                $ignores = array();
                foreach ($this->scriptProperties['boards'] as $board) {
                    $ignores[] = $board;
                }
                $ignores = array_unique($ignores);
                sort($ignores);
                $this->discuss->user->set('ignore_boards',implode(',',$ignores));
            } else {
                $this->discuss->user->set('ignore_boards', '');
            }
            if ($this->discuss->user->save()) {
                $this->discuss->user->clearCache();
                $url = $this->discuss->request->makeUrl('user/ignoreboards');
                $this->modx->sendRedirect($url);
            }
        }
    }

    /**
     * Get the list of boards to be able to ignore
     * @return array
     */
    public function getBoards() {
        /* build query */
        $this->boards = $this->modx->call('disBoard','fetchList',array(&$this->modx,false));
        return $this->boards;
    }

    /**
     * Adds board to category map in correct place in tree
     * @param $board
     * @param $map
     */
    public function mapRecursion($boards) {
        $temp = array();
        $tree = array();

        foreach ($boards as $board) {
            if (!isset($temp[$board['id']])) {
                $temp[$board['id']] = $board;
            }
            if ($board['parent'] != 0) {
                $temp[$board['parent']]['boards'][$board['id']] =& $temp[$board['id']];
            } else {
                $tree[$board['id']] =& $temp[$board['id']];
            }
        }
        // Place each tree to right category
        foreach ($tree as $boardTree) {
            $this->categoryMap[$boardTree['category']]['boards'][] = $boardTree;
        }
    }

    /**
     * Linearizes board map
     * @param $boards
     * @return string
     * @todo Better recursion to get full <ul><ul>... structure
     */
    private function _mapToLinear($boards) {
        $boardTpl = $this->getOption('boardTpl','board/disBoardCheckbox');
        $subBoardUl = $this->getOption('subBoardUl', 'board/disBoardChildUl');
        $depth = null;
        $out = '';
        foreach ($boards as $board) {
            if ($depth === null) {
                $depth = $board['depth'];
            }
            $out .= $this->discuss->getChunk($boardTpl, $board);
            if (array_key_exists('boards', $board)) {
                $out .= $this->_mapToHTML($board['boards']);
            }
        }
        return $out;
    }
    /**
     * Iterate through and list boards
     * @return void
     */
    public function prepareBoards() {
        $rowSeparator = $this->getOption('rowSeparator',"\n");
        $subBoardPaddingString = $this->getOption('subBoardPaddingString','---');
        $categoryTpl = $this->getOption('categoryTpl','board/disBoardCategoryIb');
        $categories = array();

        $ignores = $this->discuss->user->get('ignore_boards');
        $ignores = explode(',',$ignores);
        $boards = array();
        foreach($this->boards as $board) {
            if (!array_key_exists($board['category'], $this->categoryMap)) {
                $this->categoryMap[$board['category']]['name'] = $board['category_name'];
                $this->categoryMap[$board['category']]['boardscount'] = 0;
                $this->categoryMap[$board['category']]['checked'] = 0;
            }

            if (in_array($board['id'],$ignores)) {
                $board['checked'] = 'checked="checked"';
                $this->categoryMap[$board['category']]['checked']++;
            } else {
                $board['checked'] = '';
            }
            $board['cls'] = 'dis-board-cb';
            $board['cls'] .= " depth-{$board['depth']}";
            if ($board['depth'] > 1) {
                $board['name'] = str_repeat($subBoardPaddingString,$board['depth']).$board['name'];
            }
            $this->categoryMap[$board['category']]['boardscount']++;
            $boards[$board['id']] = $board;
        }
        $this->mapRecursion($boards);
        foreach ($this->categoryMap as $catId => $category) {
            $categories[] = $this->discuss->getChunk($categoryTpl, array(
                'id' => $catId,
                'category_name' => $category['name'],
                'list' => $this->_mapToLinear($category['boards']),
                'checked' => ($category['boardscount'] == $category['checked']) ? 'checked="checked"' : ''
            ));
        }
        $this->setPlaceholder('boards',implode($rowSeparator,$categories));
    }

    /**
     * Get the user menu on the left-hand side
     * @return void
     */
    public function getMenu() {
        $menuTpl = $this->getProperty('menuTpl','disUserMenu');
        $this->setPlaceholder('usermenu',$this->discuss->getChunk($menuTpl,$this->getPlaceholders()));
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array(
            'text' => $this->modx->lexicon('discuss.user.trail',array('user' => $this->discuss->user->get('username'))),
            'url' => $this->discuss->request->makeUrl('user')
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.ignore_preferences'),'active' => true);
        return $trail;
    }
}

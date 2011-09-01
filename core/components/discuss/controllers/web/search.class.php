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
 * Search the forums
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussSearchController extends DiscussController {
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.search_forums');
    }
    public function getSessionPlace() { return 'search'; }

    public function process() {
        $placeholders = array();

        $s = $this->getProperty('s',false);
        if (!empty($s)) {
            $this->search($s);
        }
        $this->getBoardList();
        if (!empty($this->scriptProperties['user'])) {
            $placeholders['user'] = strip_tags($this->discuss->convertMODXTags($this->scriptProperties['user']));
        }
        $this->setPlaceholders($placeholders);
    }

    /**
     * Get data for board dropdown list
     * @return void
     */
    public function getBoardList() {
        /* board dropdown list */
        $boards = $this->modx->call('disBoard','fetchList',array(&$this->modx));
        $boardOutput = array();
        $boardOutput[] = $this->discuss->getChunk('board/disBoardOpt',array('id' => '','name' => $this->modx->lexicon('discuss.board_all'),'selected' => ''));
        foreach ($boards as $board) {
            $board['selected'] = !empty($this->scriptProperties['board']) && $this->scriptProperties['board'] == $board['id'] ? ' selected="selected"' : '';
            $pad = $board['depth']-1;
            if ($pad < 0) $pad = 0;
            $board['name'] = str_repeat('--',$pad).$board['name'];
            $boardOutput[] = $this->discuss->getChunk('board/disBoardOpt',$board);
        }
        $this->setPlaceholder('boards',implode("\n",$boardOutput));
    }

    /**
     * @param string $s
     * @return void
     */
    public function search($s) {
        $placeholders = array();
        $resultRowTpl = $this->modx->getOption('resultRowTpl',$this->scriptProperties,'disSearchResult');
        $toggle = $this->modx->getOption('toggle',$this->scriptProperties,'+');
        $limit = !empty($this->scriptProperties['limit']) ? $this->scriptProperties['limit'] : $this->modx->getOption('discuss.threads_per_page',null,20);
        $page = !empty($this->scriptProperties['page']) ? $this->scriptProperties['page'] : 1;
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;
        $end = $start+$limit;

        $string = urldecode(str_replace(array(';',':'),'',strip_tags($s)));
        $this->setPlaceholder('search',$this->discuss->convertMODXTags($string));
        $this->setPlaceholder('start',number_format($start+1));

        if ($this->discuss->loadSearch()) {
            $conditions = $this->getConditions();
            $searchResponse = $this->discuss->search->run($string,$limit,$start,$conditions);

            $results= array();
            $maxScore = 0;
            if (!empty($searchResponse['results'])) {
                foreach ($searchResponse['results'] as $postArray) {
                    if (isset($postArray['score'])) {
                        if ($postArray['score'] > $maxScore) {
                            $maxScore = $postArray['score'];
                        }

                        $postArray['relevancy'] = @number_format(($postArray['score']/$maxScore)*100,0);
                    }

                    if ($postArray['parent']) {
                        $postArray['cls'] = 'dis-search-result dis-result-'.$postArray['thread'];
                    } else {
                        $postArray['toggle'] = $toggle;
                        $postArray['cls'] = 'dis-search-parent-result dis-parent-result-'.$postArray['thread'];
                    }
                    $postArray['message'] = strip_tags($postArray['message']);
                    $position = intval(strpos($postArray['message'],$string));
                    $length = strlen($postArray['message']);
                    if ($position > 0 && $length > $position) {
                        $postArray['message'] = ($position != 0 ? '...' : '').substr($postArray['message'],$position,$position+100).'...';
                    } else {
                        $postArray['message'] = substr($postArray['message'],0,100).($length > 100 ? '...' : '');
                    }
                    if (empty($postArray['url'])) {
                        $postArray['url'] = $this->discuss->request->makeUrl('thread',array('thread' => $postArray['thread'])).'#dis-post-'.$postArray['id'];
                    }
                    $postArray['replies'] = number_format($postArray['replies'],0);

                    $results[] = $this->discuss->getChunk($resultRowTpl,$postArray);
                }
                $results = implode("\n",$results);
            } else {
                $results = $this->modx->lexicon('discuss.search_no_results');
            }
            $this->setPlaceholders(array(
                'results' => $results,
                'total' => number_format($searchResponse['total']),
                'end' => number_format($end > $searchResponse['total'] ? $searchResponse['total'] : $end),
            ));
            /* get pagination */
            $this->discuss->hooks->load('pagination/build',array(
                'count' => $searchResponse['total'],
                'view' => 'search',
                'limit' => $limit,
            ));
        } else {
            $this->setPlaceholders(array(
                'pagination' => '',
                'total' => 0,
                'results' => 'Could not load search class.',
            ));
        }
        $this->setPlaceholders($placeholders);
    }

    /**
     * Build the conditions for the search
     * @return array
     */
    public function getConditions() {
        $conditions = array();
        if (!empty($this->scriptProperties['board'])) { $conditions['board'] = $this->scriptProperties['board']; }
        if (!empty($this->scriptProperties['category'])) { $conditions['category'] = $this->scriptProperties['category']; }
        if (!empty($this->scriptProperties['user'])) {
            if (intval($this->scriptProperties['user']) <= 0) {
                /** @var disUser $user */
                $user = $this->modx->getObject('disUser',array('username' => $this->scriptProperties['user']));
                if ($user) {
                    $conditions['author'] = $user->get('id');
                }
            } else {
                $conditions['author'] = $this->scriptProperties['user'];
            }
        }
        $conditions['private'] = 0;
        return $conditions;
    }



    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array(
            'text' => $this->modx->lexicon('discuss.search'),
            'active' => true,
        );
        return $trail;
    }
}
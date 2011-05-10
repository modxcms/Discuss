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
 * Search the forums
 *
 * @package discuss
 */
$discuss->setSessionPlace('search');
$discuss->setPageTitle($modx->lexicon('discuss.search_forums'));

/* setup default properties */
$cssSearchResultCls = $modx->getOption('cssSearchResultCls',$scriptProperties,'dis-search-result');
$cssSearchResultParentCls = $modx->getOption('cssSearchResultParentCls',$scriptProperties,'dis-search-parent-result');
$resultRowTpl = $modx->getOption('resultRowTpl',$scriptProperties,'disSearchResult');
$toggle = $modx->getOption('toggle',$scriptProperties,'+');

$limit = !empty($scriptProperties['limit']) ? $scriptProperties['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;
$end = $start+$limit;

/* do search */
$placeholders = array();
if (!empty($scriptProperties['s'])) {
    $string = urldecode(str_replace(array(';',':'),'',strip_tags($scriptProperties['s'])));
    $placeholders['search'] = $discuss->convertMODXTags($string);
    $placeholders['start'] = number_format($start+1);

    if ($discuss->loadSearch()) {
        $conditions = array();
        if (!empty($scriptProperties['board'])) { $conditions['board'] = $scriptProperties['board']; }
        if (!empty($scriptProperties['category'])) { $conditions['category'] = $scriptProperties['category']; }
        if (!empty($scriptProperties['user'])) {
            if (intval($scriptProperties['user']) <= 0) {
                $user = $modx->getObject('disUser',array('username' => $scriptProperties['user']));
                if ($user) {
                    $conditions['author'] = $user->get('id');
                }
            } else {
                $conditions['author'] = $scriptProperties['user'];
            }
        }
        $searchResponse = $discuss->search->run($string,$limit,$start,$conditions);

        $placeholders['results'] = array();
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
                    $postArray['url'] = $discuss->url.'thread/?thread='.$postArray['thread'].'#dis-post-'.$postArray['id'];
                }

                $placeholders['results'][] = $discuss->getChunk('disSearchResult',$postArray);
            }
            $placeholders['results'] = implode("\n",$placeholders['results']);
        } else {
            $placeholders['results'] = $modx->lexicon('discuss.search_no_results');
        }
        $placeholders['total'] = number_format($searchResponse['total']);
        $placeholders['end'] = number_format($end > $searchResponse['total'] ? $searchResponse['total'] : $end);

        /* get pagination */
        $discuss->hooks->load('pagination/build',array(
            'count' => $searchResponse['total'],
            'view' => 'search',
            'limit' => $limit,
        ));
    } else {
        $placeholders['pagination'] = '';
        $placeholders['total'] = 0;
        $placeholders['results'] = 'Could not load search class.';
    }
}

/* board dropdown list */
$boards = $modx->call('disBoard','fetchList',array(&$modx));
$placeholders['boards'] = array();
$placeholders['boards'][] = $discuss->getChunk('board/disBoardOpt',array('id' => '','name' => $modx->lexicon('discuss.board_all'),'selected' => ''));
foreach ($boards as $board) {
    $board['selected'] = !empty($scriptProperties['board']) && $scriptProperties['board'] == $board['id'] ? ' selected="selected"' : '';
    $board['name'] = str_repeat('--',$board['depth']-1).$board['name'];
    $placeholders['boards'][] = $discuss->getChunk('board/disBoardOpt',$board);
}
$placeholders['boards'] = implode("\n",$placeholders['boards']);

unset($boards,$board,$list,$c);

if (!empty($scriptProperties['user'])) {
    $placeholders['user'] = strip_tags($discuss->convertMODXTags($scriptProperties['user']));
}

/* get breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array(
    'text' => $modx->lexicon('discuss.search'),
    'active' => true,
);
$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

return $placeholders;
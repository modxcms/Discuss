<?php
/**
 * Get a list of boards
 *
 * @package discuss
 */

$board = isset($scriptProperties['board']) ? (is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board']) : 0;

$cacheKey = 'discuss/board/index';
/* cant use caching yet b/c of unread status
$list = $modx->cacheManager->get($cacheKey);
if (!empty($list)) {
    //return $list;
}*/

/* get main query */
$category = $modx->getOption('category',$scriptProperties,false);
$category = (int)(is_object($scriptProperties['category']) ? $scriptProperties['category']->get('id') : $scriptProperties['category']);
$response = $modx->call('disBoard','getList',array(&$modx,$board,$category));

$boards = array();
foreach ($response['results'] as $board) {
    $boards[] = $board->toArray();
}
unset($c);

/* now loop through boards */
$list = array();
$currentCategory = 0;
$rowClass = 'even';
$boardList = array();

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

foreach ($boards as $board) {
    $board['unread-cls'] = ($board['unread'] > 0 && $discuss->isLoggedIn) ? 'dis-unread' : 'dis-read';
    if (!empty($board['last_post_createdon'])) {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($board['last_post_createdon'])),
            'user' => $board['last_post_author'],
            'username' => $board['last_post_username'],
            'author_link' => $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->url.'user/?user='.$board['last_post_author'].'">'.$board['last_post_username'].'</a>' : $board['last_post_username'],
        );
        $lp = $discuss->getChunk('board/disLastPostBy',$phs);
        $board['lastPost'] = $lp;
    } else {
        $board['lastPost'] = '';
    }

    $board['subforums'] = '';
    if (!empty($board['subboards'])) {
        $subBoards = explode('||',$board['subboards']);
        $ph = array();
        $sbl = array();
        foreach ($subBoards as $subboard) {
            $sb = explode(':',$subboard);
            $ph['id'] = $sb[0];
            $ph['title'] = $sb[1];

            $sbl[] = $discuss->getChunk('board/disSubForumLink',$ph);
        }
        $board['subforums'] = implode(",\n",$sbl);
    }

    /* get current category */
    $currentCategory = $board['category'];
    if (!isset($lastCategory)) {
        $lastCategory = $board['category'];
    }

    $board['post_stats'] = $modx->lexicon('discuss.board_post_stats',array(
        'posts' => number_format($board['total_posts']),
        'topics' => number_format($board['num_topics']),
        'unread' => number_format($board['unread']),
    ));

    /* if changing categories */
    if ($currentCategory != $lastCategory) {
        $ba['list'] = implode("\n",$boardList);
        unset($ba['rowClass']);
        $list[] = $discuss->getChunk('category/disCategoryLi',$ba);

        $ba = $board;
        $boardList = array(); /* reset current category board list */
        $ba['rowClass'] = $rowClass;
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk('board/disBoardLi',$ba);

    } else { /* otherwise add to temp board list */
        $ba = $board;
        $ba['rowClass'] = $rowClass;
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk('board/disBoardLi',$ba);
        $rowClass = ($rowClass == 'alt') ? 'even' : 'alt';
    }
}
if (count($boards) > 0) {
    /* Last category */
    $ba['list'] = implode("\n",$boardList);
    $ba['rowClass'] = $rowClass;
    $list[] = $discuss->getChunk('category/disCategoryLi',$ba);
    $list = implode("\n",$list);
    unset($currentCategory,$ba,$boards,$board,$lp);

    $modx->cacheManager->set($cacheKey,$list,3600);
}

return $list;
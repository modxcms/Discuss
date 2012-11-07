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
 * Get a list of boards
 *
 * @var array $options
 * @var array $scriptProperties
 * @var modX $modx
 * @var Discuss $discuss
 *
 * @package discuss
 * @subpackage hooks
 */
//var_dump($options);
$board = isset($scriptProperties['board']) ? (is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board']) : 0;
$lastPostTpl = $modx->getOption('lastPostTpl',$options,'board/disLastPostBy');
$subBoardTpl = $modx->getOption('subBoardTpl',$options,'board/disSubForumLink');
$subBoardSeparator = $modx->getOption('subBoardSeparator',$options,",\n");
$categoryRowTpl = $modx->getOption('categoryRowTpl',$options,'category/disCategoryLi');
$boardRowTpl = $modx->getOption('boardRowTpl',$options,'board/disBoardLi');
$checkUnread = $modx->getOption('checkUnread',$options,true);

/* check cache first */
/** @var int|disCategory $category */
$category = $modx->getOption('category',$scriptProperties,false);
$category = (int)(is_object($category) ? $category->get('id') : $category);
$c = array(
    'board' => $board,
    'category' => $category,
);
$cacheKey = 'discuss/board/index/'.md5(serialize($c));
$boards = $modx->cacheManager->get($cacheKey);
if (empty($boards)) {
    /* get main query */
    $response = $modx->call('disBoard','getList',array(&$modx,$board,$category));

    $boards = array();
    /** @var disBoard $board */
    foreach ($response['results'] as $board) {
        $board->calcLastPostPage();
        $board->getLastPostUrl();
        $boards[] = $board->toArray('',true,true);
    }
    $modx->cacheManager->set($cacheKey,$boards,$modx->getOption('discuss.cache_time',null,0));
}

/* now loop through boards */
$list = array();
$currentCategory = 0;
$rowClass = 'even';
$boardList = array();

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');
$groups = $discuss->user->getUserGroups();
$isAdmin = $discuss->user->isAdmin();
foreach ($boards as $board) {
    /* check usergroup perms */
    if (!$isAdmin) {
        $bgroups = explode(',',$board['usergroups']);
        if (!empty($groups) && !empty($board['usergroups'])) {
            $in = false;
            foreach ($bgroups as $bg) {
                if (in_array((int)$bg,$groups)) {
                    $in = true;
                }
            }
            if (!$in) continue;
        } else if (!empty($board['usergroups'])) {
            continue;
        }
    }
    /* check ignore boards */
    if ($discuss->user->isLoggedIn) {
        if (in_array($board['id'],explode(',',$discuss->user->get('ignore_boards')))) {
            continue;
        }
    }
    /* check for read status */
    if ($checkUnread) {
        $board['unread'] = $discuss->user->isLoggedIn ? $discuss->user->isBoardRead($board['id']) : 1;
        $board['unread-cls'] = $board['unread'] ? 'dis-unread' : 'dis-read';
        $board['unread-count'] = $discuss->user->isLoggedIn ? $discuss->user->getUnreadCount($board['id']) : 0;
    } else {
        $board['unread'] = $discuss->user->isLoggedIn ? 0 : 1;
        $board['unread-cls'] = $discuss->user->isLoggedIn ? 'dis-read' : 'dis-unread';
        $board['unread-count'] = 0;
    }

    if (!empty($board['last_post_createdon'])) {
        $username = $board['last_post_username'];
        if (!empty($board['last_post_udn']) && !empty($board['last_post_display_name'])) {
            $username = $board['last_post_display_name'];
        }
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($board['last_post_createdon'])),
            'user' => $board['last_post_author'],
            'username' => $username,
            'thread' => $board['last_post_thread'],
            'id' => $board['last_post_id'],
            'url' => $board['last_post_url'],
            'author_link' => $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->request->makeUrl('u/'.$board['last_post_username']).'">'.$username.'</a>' : $username,
        );
        $lp = $discuss->getChunk($lastPostTpl,$phs);
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

            $sbl[] = $discuss->getChunk($subBoardTpl,$ph);
        }
        $board['subforums'] = implode($subBoardSeparator,$sbl);
    }

    /* get current category */
    $currentCategory = $board['category'];
    if (!isset($lastCategory)) {
        $lastCategory = $board['category'];
    }

    $board['post_stats'] = $modx->lexicon('discuss.board_post_stats',array(
        'posts' => number_format($board['total_posts']),
        'topics' => number_format($board['num_topics']),
        'unread' => !empty($board['unread']) ? number_format((int)$board['unread']) : 0,
    ));

    $board['is_locked'] = !empty($board['locked']) ? '<div class="dis-board-locked"></div>' : '';

    /* if changing categories */
    if ($currentCategory != $lastCategory) {
        $ba['list'] = implode("\n",$boardList);
        unset($ba['rowClass']);
        if (!empty($ba['list'])) {
            $list[] = $discuss->getChunk($categoryRowTpl,$ba);
        }

        $ba = $board;
        $boardList = array(); /* reset current category board list */
        $ba['rowClass'] = $rowClass;
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk($boardRowTpl,$ba);

    } else { /* otherwise add to temp board list */
        $ba = $board;
        $ba['rowClass'] = $rowClass;
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk($boardRowTpl,$ba);
        $rowClass = ($rowClass == 'alt') ? 'even' : 'alt';
    }
}
if (count($boards) > 0) {
    /* Last category */
    $ba['list'] = implode("\n",$boardList);
    $ba['rowClass'] = $rowClass;
    $list[] = $discuss->getChunk($categoryRowTpl,$ba);
    $list = implode("\n",$list);
    unset($currentCategory,$ba,$boards,$board,$lp);
}

return $list;

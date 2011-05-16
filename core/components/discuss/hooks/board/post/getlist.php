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
 * Get a list of posts in a board
 *
 * @package discuss
 */
$response = array(
    'start' => $scriptProperties['start'],
    'limit' => $scriptProperties['limit'],
);

/* setup default properties */
$tpl = $modx->getOption('tpl',$scriptProperties,'post/disBoardPost');
$mode = $modx->getOption('mode',$scriptProperties,'post');
$board = (int)(is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board']);

$c = array();
$c['limit'] = !empty($scriptProperties['limit']) ? $scriptProperties['limit'] : 0;
$c['start'] = !empty($scriptProperties['start']) ? $scriptProperties['start'] : 0;
$cacheKey = 'discuss/board/'.$board.'/posts/'.$mode.'-'.md5(serialize($c));
$threadCollection = $modx->cacheManager->get($cacheKey);

if (empty($threadCollection)) {
    /* build query */
    $c = $modx->newQuery('disThread');
    $c->innerJoin('disPost','FirstPost');
    $c->innerJoin('disPost','LastPost');
    $c->innerJoin('disThread','LastPostThread','LastPostThread.id = LastPost.thread');
    $c->innerJoin('disUser','LastAuthor');
    $c->where(array(
        'disThread.board' => $board,
    ));
    $response['total'] = $modx->getCount('disThread',$c);
    $c->select(array(
        'LastPost.*',
        'LastPost.id AS last_post_id',
        'LastPost.id AS post_id',
        'LastPostThread.replies AS last_post_replies',
        'FirstPost.title',
        'LastAuthor.username',
        'LastAuthor.user AS user',
        'disThread.id',
        'disThread.replies',
        'disThread.views',
        'disThread.sticky',
        'disThread.locked',
        'disThread.post_last',
        '(SELECT GROUP_CONCAT(pAuthor.id)
            FROM '.$modx->getTableName('disPost').' AS pPost
            INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
            WHERE pPost.thread = disThread.id
         ) AS participants',
    ));
    if ($modx->getOption('get_category_name',$scriptProperties,false)) {
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disCategory','Category','Board.category = Category.id');
        $c->select(array(
            'Category.name AS category_name',
        ));
    }
    if ($modx->getOption('discuss.enable_sticky',null,true)) {
        $c->sortby('disThread.sticky','DESC');
    }
    $c->sortby('LastPost.createdon','DESC');
    if (!empty($scriptProperties['limit'])) {
        $c->limit($scriptProperties['limit'],$scriptProperties['start']);
    }
    $threads = $modx->getCollection('disThread',$c);

    $threadCollection = array();
    foreach ($threads as $thread) {
        $thread->getUrl();
        $thread->calcLastPostPage();
        $thread->buildCssClass('board-post');
        $thread->buildIcons();
        $threadArray = $thread->toArray();
        if ($mode != 'rss') {
            $threadArray['excerpt'] = '';
            $threadArray['views'] = number_format($threadArray['views']);
            $threadArray['replies'] = number_format($threadArray['replies']);
            $threadArray['latest.id'] = $thread->get('last_post_id');

            $lastPost = $thread->getOne('LastPost');
            if ($lastPost) {
                $threadArray['excerpt'] = $lastPost->get('message');
                $threadArray['excerpt'] = $lastPost->stripBBCode($threadArray['excerpt']);
                $threadArray['excerpt'] = strip_tags($threadArray['excerpt']);
                if (strlen($threadArray['excerpt']) > 500) {
                    $threadArray['excerpt'] = substr($threadArray['excerpt'],0,500).'...';
                }
            }
        } else {
            $threadArray['title'] = strip_tags($threadArray['title']);
            $threadArray['createdon'] = strftime('%a, %d %b %Y %I:%M:%S %z',strtotime($threadArray['createdon']));
            $threadArray['url'] = $modx->getOption('site_url').$threadArray['url'];
        }
        $threadCollection[] = $threadArray;
    }

    /* set to cache */
    $modx->cacheManager->set($cacheKey,$threadCollection,$modx->getOption('discuss.cache_time',null,3600));
}

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

$unread = $discuss->user->getUnreadThreadsForBoard($board);

/* iterate through threads */
reset($threadCollection);
$response['results'] = array();
foreach ($threadCollection as $threadArray) {
    if ($mode != 'rss') {
        /* last post */
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($threadArray['createdon'])),
            'user' => $threadArray['author'],
            'username' => $threadArray['username'],
            'author_link' => $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->url.'user/?user='.$threadArray['user'].'">'.$threadArray['username'].'</a>' : $threadArray['username'],
        );
        $latestText = $discuss->getChunk('board/disLastPostBy',$phs);
        $threadArray['latest'] = $latestText;
        
        /* unread class */
        $threadArray['unread'] = '';
        if ($discuss->user->isLoggedIn && in_array($threadArray['id'],$unread)) {
            $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
        }
    }
    $response['results'][] = $discuss->getChunk($tpl,$threadArray);
}
return $response;
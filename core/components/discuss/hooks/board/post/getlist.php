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
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $scriptProperties
 *
 * @package discuss
 * @subpackage hooks
 */
$response = array();
$response['limit'] = !empty($scriptProperties['limit']) ? intval($scriptProperties['limit']) : 0;
$response['start'] = !empty($scriptProperties['start']) ? intval($scriptProperties['start']) : 0;


/* setup default properties */
$tpl = $modx->getOption('tpl',$scriptProperties,'post/disBoardPost');
$lastPostTpl = $modx->getOption('lastPostTpl',$scriptProperties,'board/disLastPostBy');
$mode = $modx->getOption('mode',$scriptProperties,'post');
$useLastPost = $modx->getOption('useLastPost',$scriptProperties,true);
$board = (int)(is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board']);

$c = array();
$c['limit'] = $response['limit'];
$c['start'] = $response['start'];
$cacheKey = 'discuss/board/'.$board.'/posts/'.$mode.($useLastPost?'1':'0').'-'.md5(serialize($c));
$cache = $modx->cacheManager->get($cacheKey);

if (empty($cache)) {
    $cache = array();
    /* build query */
    $c = $modx->newQuery('disThread');
    $c->innerJoin('disPost','FirstPost');
    $c->innerJoin('disPost','LastPost');
    $c->innerJoin('disThread','LastPostThread','LastPostThread.id = LastPost.thread');
    $c->innerJoin('disUser','LastAuthor');
    $c->innerJoin('disUser','FirstAuthor');
    $c->where(array(
        'disThread.board' => $board,
    ));
    $cache['total'] = $modx->getCount('disThread',$c);
    $c->select($modx->getSelectColumns('disPost','LastPost'));
    $c->select(array(
        'last_post_id' => 'LastPost.id',
        'post_id' => 'LastPost.id',
        'last_post_replies' => 'LastPostThread.replies',
        'last_post_username' => 'LastAuthor.username',
        'last_post_udn' => 'LastAuthor.use_display_name',
        'last_post_display_name' => 'LastAuthor.display_name',
        'first_post_username' => 'FirstAuthor.username',
        'first_post_udn' => 'FirstAuthor.use_display_name',
        'first_post_display_name' => 'FirstAuthor.display_name',
        'FirstPost.title',
        'user' => 'LastAuthor.user',
        'disThread.id',
        'disThread.replies',
        'disThread.views',
        'disThread.sticky',
        'disThread.locked',
        'disThread.post_last',
        'disThread.post_first',
        'disThread.answered',
        'disThread.class_key',
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
    if ($modx->getOption('discuss.enable_sticky',null,true) && $mode != 'rss') {
        $c->sortby('disThread.sticky','DESC');
    }
    $c->sortby('disThread.post_last_on','DESC');
    if (!empty($scriptProperties['limit'])) {
        $c->limit($scriptProperties['limit'],$scriptProperties['start']);
    }
    $threads = $modx->getCollection('disThread',$c);

    $cache['results'] = array();
    /** @var disThread $thread */
    foreach ($threads as $thread) {
        $thread->getUrl();
        $thread->buildCssClass('board-post');
        $thread->buildIcons();
        $threadArray = $thread->toArray();
        if ($mode != 'rss') {
            $threadArray['excerpt'] = '';
            $threadArray['views'] = number_format($threadArray['views']);
            $threadArray['replies'] = number_format($threadArray['replies']);
            $threadArray['latest.id'] = $thread->get('last_post_id');

            /** @var disPost $lastPost */
            $lastPost = $thread->getOne('LastPost');
            if ($lastPost) {
                $threadArray = array_merge($threadArray,$thread->toArray('post.'));
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
            $threadArray['url'] = $modx->getOption('site_url').ltrim($threadArray['url'],'/');

            /** @var disPost $post */
            $alias = $useLastPost ? 'LastPost' : 'FirstPost';
            $post = $thread->getOne($alias);
            if ($post) {
                $threadArray = array_merge($threadArray,$thread->toArray('post.'));
                $threadArray['excerpt'] = $post->get('message');
                $threadArray['excerpt'] = $post->stripBBCode($threadArray['excerpt']);
                $threadArray['excerpt'] = strip_tags($threadArray['excerpt']);
                if (strlen($threadArray['excerpt']) > 500) {
                    $threadArray['excerpt'] = substr($threadArray['excerpt'],0,500).'...';
                }
            }
        }
        $cache['results'][] = $threadArray;
    }

    /* set to cache */
    $modx->cacheManager->set($cacheKey,$cache,$modx->getOption('discuss.cache_time',null,3600));
}

$response['total'] = $cache['total'];

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

$unread = $discuss->user->getUnreadThreadsForBoard($board);

/* iterate through threads */
reset($cache['results']);
$response['results'] = array();
foreach ($cache['results'] as $threadArray) {
    if ($mode != 'rss') {
        /* last post */

        $threadArray['post.username'] = $threadArray['last_post_username'];
        if (!empty($threadArray['last_post_udn']) && !empty($threadArray['last_post_display_name'])) {
            $threadArray['post.username'] = $threadArray['last_post_display_name'];
        }
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($threadArray['createdon'])),
            'user' => $threadArray['author'],
            'username' => $threadArray['last_post_username'],
            'author_link' => $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->request->makeUrl('u/'.$threadArray['last_post_username']).'">'.$threadArray['post.username'].'</a>' : $threadArray['post.username'],
        );
        $latestText = $discuss->getChunk($lastPostTpl,$phs);
        $threadArray['author_link'] = $phs['author_link'];
        $threadArray['createdon'] = $phs['createdon'];
        $threadArray['latest'] = $latestText;

        /* unread class */
        $threadArray['unread'] = '';
        $threadArray['unreadCls'] = '';
        if ($discuss->user->isLoggedIn) {
            if (in_array($threadArray['id'],$unread)) {
                $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
                $threadArray['unreadCls'] = 'dis-unread';
            } else {
                $threadArray['unreadCls'] = 'dis-read';
            }
        }
    }
    $response['results'][] = $discuss->getChunk($tpl,$threadArray);
}
return $response;
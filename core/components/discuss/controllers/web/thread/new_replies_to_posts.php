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
 * Get all new replies to posts authored by user
 *
 * @package discuss
 */
if (!$discuss->user->isLoggedIn) $discuss->sendUnauthorizedPage();
$discuss->setSessionPlace('new_replies_to_posts');
$discuss->setPageTitle($modx->lexicon('discuss.unread_posts'));
$placeholders = array();

/* setup default properties */
$limit = !empty($scriptProperties['limit']) ? $scriptProperties['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;

$sortBy = $modx->getOption('sortBy',$scriptProperties,'LastPost.createdon');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'DESC');

/* handle marking all as read */
if (!empty($scriptProperties['read']) && $discuss->user->isLoggedIn) {
    $discuss->hooks->load('thread/read_all');
}


/* get new replies threads */
$threads = $modx->call('disThread','fetchNewReplies',array(&$modx,$sortBy,$sortDir,$limit,$start));
$posts = array();

$canViewProfiles = $modx->hasPermission('discuss.view_profiles');
$hotThreadThreshold = $modx->getOption('discuss.hot_thread_threshold',null,10);
$enableSticky = $modx->getOption('discuss.enable_sticky',null,true);
$enableHot = $modx->getOption('discuss.enable_hot',null,true);
$list = array();
foreach ($threads['results'] as $thread) {
    $thread->calcLastPostPage();
    $thread->getUrl();
    $threadArray = $thread->toArray();
    $threadArray['class'] = 'dis-board-li';
    $threadArray['createdon'] = strftime($discuss->dateFormat,strtotime($threadArray['createdon']));
    $threadArray['icons'] = '';

    /* set css class */
    $class = array('board-post');
    if ($enableHot) {
        $threshold = $hotThreadThreshold;
        if ($discuss->user->get('id') == $threadArray['author'] && $discuss->user->isLoggedIn) {
            $class[] = $threadArray['replies'] < $threshold ? 'dis-my-normal-thread' : 'dis-my-veryhot-thread';
        } else {
            $class[] = $threadArray['replies'] < $threshold ? '' : 'dis-veryhot-thread';
        }
    }
    $threadArray['class'] = implode(' ',$class);

    /* if sticky/locked */
    $icons = array();
    if ($threadArray['locked']) { $icons[] = '<div class="dis-thread-locked"></div>'; }
    if ($enableSticky && $threadArray['sticky']) {
        $icons[] = '<div class="dis-thread-sticky"></div>';
    }
    $threadArray['icons'] = implode("\n",$icons);

    $threadArray['views'] = number_format($threadArray['views']);
    $threadArray['replies'] = number_format($threadArray['replies']);

    /* unread class */
    $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
    $threadArray['author_link'] = $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->request->makeUrl('user',array('user' => $threadArray['author'])).'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];

    $list[] = $discuss->getChunk('post/disPostLi',$threadArray);
}
$placeholders['threads'] = implode("\n",$list);

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->request->makeUrl(),
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.new_replies_to_posts').' ('.number_format($threads['total']).')','active' => true);

$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* action buttons */
$actionButtons = array();
if ($discuss->user->isLoggedIn) {
    $actionButtons[] = array('url' => $discuss->request->makeUrl('thread/new_replies_to_posts',array('read' => 1)), 'text' => $modx->lexicon('discuss.mark_all_as_read'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* build pagination */
$discuss->hooks->load('pagination/build',array(
    'count' => $threads['total'],
    'id' => 0,
    'view' => 'thread/unread',
    'limit' => $limit,
));

return $placeholders;
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
/* get default options */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));
$start = $modx->getOption('start',$scriptProperties,0);

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

/* recent posts */
$c = $modx->newQuery('disThread');
$c->query['distinct'] = 'DISTINCT';
$c->innerJoin('disBoard','Board');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disThread','LastPostThread','LastPostThread.id = LastPost.thread');
$c->innerJoin('disUser','LastAuthor');
$c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
$c->where(array(
    'Board.status:!=' => disBoard::STATUS_INACTIVE,
));

/* ignore spam/recycle bin boards */
$spamBoard = $modx->getOption('discuss.spam_bucket_board',null,false);
if (!empty($spamBoard)) {
    $c->where(array(
        'Board.id:!=' => $spamBoard,
    ));
}
$trashBoard = $modx->getOption('discuss.recycle_bin_board',null,false);
if (!empty($trashBoard)) {
    $c->where(array(
        'Board.id:!=' => $trashBoard,
    ));
}

/* usergroup protection */
$groups = $discuss->user->getUserGroups();
if (!$discuss->user->isAdmin()) {
    if (!empty($groups)) {
        // restrict boards by user group if applicable
        $g = array(
            'UserGroups.usergroup:IN' => $groups,
        );
        $g['OR:UserGroups.usergroup:IS'] = null;
        $where[] = $g;
        $c->andCondition($where,null,2);
    } else {
        $c->where(array(
            'UserGroups.usergroup:IS' => null,
        ));
    }
}
/* if showing only recent posts for a user */
if (!empty($scriptProperties['user'])) {
    $c->innerJoin('disPost','Posts');
    $c->where(array(
        'Posts.author' => $scriptProperties['user'],
    ));
}
/* ignore boards */
if ($discuss->user->isLoggedIn) {
    $ignoreBoards = $discuss->user->get('ignore_boards');
    if (!empty($ignoreBoards)) {
        $c->where(array(
            'Board.id:NOT IN' => explode(',',$ignoreBoards),
        ));
    }
}
/* if requesting total */
if (!empty($scriptProperties['getTotal'])) {
    $total = $modx->getCount('disThread',$c);
}
$c->select(array(
    'disThread.id',
    'disThread.replies',
    'disThread.views',
    'disThread.sticky',
    'disThread.locked',
    'disThread.board',
    'FirstPost.title',
    'Board.name AS board_name',
    'LastPost.id AS post_id',
    'LastPost.thread',
    'LastPost.author',
    'LastPost.createdon',
    'LastPostThread.replies AS last_post_replies',
    'LastAuthor.username AS author_username',
));
if (!empty($scriptProperties['showIfParticipating'])) {
    $c->select(array(
        '(SELECT GROUP_CONCAT(pAuthor.id)
            FROM '.$modx->getTableName('disPost').' AS pPost
            INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
            WHERE pPost.thread = disThread.id
         ) AS participants',
    ));
}
$c->sortby('LastPost.createdon','DESC');
$c->limit($limit,$start);
$recentPosts = $modx->getCollection('disThread',$c);

/* iterate */
$list = array();
$idx = 0;
foreach ($recentPosts as $thread) {
    $thread->buildIcons();
    $thread->buildCssClass('board-post');
    $thread->calcLastPostPage();
    $thread->getUrl();
    $threadArray = $thread->toArray();
    $threadArray['idx'] = $idx;
    $threadArray['createdon'] = strftime($discuss->dateFormat,strtotime($threadArray['createdon']));
    $threadArray['author_link'] = $canViewProfiles ? '<a href="'.$discuss->request->makeUrl('user',array('user' => $threadArray['author'])).'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];
    $threadArray['views'] = '';
    $threadArray['replies'] = number_format($threadArray['replies']);
    $threadArray['unread'] = '';

    /* unread class */
    $list[] = $discuss->getChunk('post/disPostLi',$threadArray);
    $idx++;
}
$list = implode("\n",$list);

return array(
    'results' => $list,
    'total' => isset($total) ? $total : null,
    'start' => $start,
    'limit' => $limit,
);
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
 * @var Discuss $discuss
 * @var modX $modx
 * @var array $scriptProperties
 * 
 * @package discuss
 */
/* get default options */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));
$start = $modx->getOption('start',$scriptProperties,0);
$fromDays = $modx->getOption('discuss.recent_threshold_days', null, 42);

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');
$postTpl = $modx->getOption('postTpl',$scriptProperties,'post/disPostLi');

$cacheKey = 'discuss/recent/'.$discuss->user->get('id').'.'.md5(serialize($scriptProperties));
$cache = $modx->cacheManager->get($cacheKey);
if (!empty($cache)) {
    if (!empty($cache['cachedon'])) {
        $c = $modx->newQuery('disPost');
        $c->where(array(
            'disPost.createdon:>=' => strftime(Discuss::DATETIME_FORMATTED, $cache['cachedon']),
        ));
        if ($modx->getCount('disPost', $c) < 1) {
            return $cache;
        }
    }
};

/* get posts */
$c = $modx->newQuery('disPost');
$c->innerJoin('disUser','Author');
$c->innerJoin('disThread','Thread');
$c->innerJoin('disUser','ThreadAuthorFirst', 'Thread.author_first = ThreadAuthorFirst.id');
$c->innerJoin('disUser','ThreadAuthorLast', 'Thread.author_last = ThreadAuthorLast.id');
$c->innerJoin('disBoard','Board','Board.id = Thread.board');
$c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
$c->where(array(
    'Board.status:!=' => 0,
    'AND:disPost.createdon:>=' => strftime(Discuss::DATETIME_FORMATTED, time() - ($fromDays * 24 * 60 * 60)),
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
/* ignore boards */
if ($discuss->user->isLoggedIn) {
    $ignoreBoards = $discuss->user->get('ignore_boards');
    if (!empty($ignoreBoards)) {
        $c->where(array(
            'Board.id:NOT IN' => explode(',',$ignoreBoards),
        ));
    }
}

$c->select($modx->getSelectColumns('disPost','disPost'));
$c->select($modx->getSelectColumns('disUser','Author', 'author_'));
$c->select($modx->getSelectColumns('disBoard','Board','board_'));
$c->select($modx->getSelectColumns('disThread','Thread','thread_'));
$c->select($modx->getSelectColumns('disUser','ThreadAuthorFirst','thread_author_first_'));
$c->select($modx->getSelectColumns('disUser','ThreadAuthorLast','thread_author_last_'));

$total = $modx->getCount('disPost',$c);

$c->sortby('disPost.createdon','DESC');
$c->limit($limit,$start);

$posts = $modx->getCollection('disPost', $c);

/* iterate */
$list = array();
$idx = 0;
/** @var disPost $post */
foreach ($posts as $post) {
    $post->getUrl();
    $postArray = $post->toArray();
    $postArray['idx'] = $idx;
    $postArray['createdon'] = strftime($discuss->dateFormat,strtotime($postArray['createdon']));
    $postArray['unread'] = false;
    $postArray['unread-cls'] = '';

    $list[] = $discuss->getChunk($postTpl,$postArray);
    $idx++;
}
$list = implode("\n",$list);

$output = array(
    'results' => $list,
    'total' => $total,
    'start' => $start,
    'limit' => $limit,
    'cachedon' => time(),
);


$modx->cacheManager->set($cacheKey,$output,$modx->getOption('discuss.cache_time',null,3600));

return $output;

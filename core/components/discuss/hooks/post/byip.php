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
 * Get all posts by an IP address
 *
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $scriptProperties
 */
/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.post_per_page',null,10));
$start = $modx->getOption('start',$scriptProperties,0);

/* build query */
$c = $modx->newQuery('disPost');
$c->innerJoin('disThread','Thread');
$c->innerJoin('disBoard','Board');
$c->innerJoin('disUser','Author');
$c->where(array(
    'disPost.ip' => $scriptProperties['ip'],
    'Thread.private' => 0,
));
if ($discuss->user->isLoggedIn) {
    $ignoreBoards = $discuss->user->get('ignore_boards');
    if (!empty($ignoreBoards)) {
        $c->where(array(
            'Board.id:NOT IN' => explode(',',$ignoreBoards),
        ));
    }
}
$total = $modx->getCount('disPost',$c);
$c->select($modx->getSelectColumns('disPost','disPost'));
$c->select(array(
    'author_username' => 'Author.username',
    'board_name' => 'Board.name',
    'Thread.sticky',
    'Thread.locked',
    'Thread.private',
    'Thread.replies',
));
$c->groupby('disPost.thread');
$c->sortby('disPost.createdon','ASC');
$c->limit($limit,$start);
$postObjects = $modx->getCollection('disPost',$c);


/* iterate */
$list = array();
$idx = 0;
/** @var disPost $post */
foreach ($postObjects as $post) {
    $post->getUrl();
    $postArray = $post->toArray();
    $postArray['class'] = '';
    $postArray['idx'] = $idx;
    $postArray['sticky'] = '';
    $postArray['createdon'] = strftime($discuss->dateFormat,strtotime($postArray['createdon']));
    $postArray['author_link'] = $canViewProfiles ? '<a href="'.$discuss->request->makeUrl('user',array('user' => $postArray['author'])).'">'.$postArray['author_username'].'</a>' : $postArray['author_username'];
    $postArray['unread'] = '';

    /* unread class */
    $list[] = $discuss->getChunk('post/disPostLi',$postArray);
    $idx++;
}
$list = implode("\n",$list);

return array(
    'results' => $list,
    'total' => isset($total) ? $total : null,
    'start' => $start,
    'limit' => $limit,
);
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

/* build query */
$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disThread','LastPostThread','LastPostThread.id = LastPost.thread');
$c->innerJoin('disUser','LastAuthor');
$c->leftJoin('disThreadRead','Reads','Reads.user = '.$discuss->user->get('id').' AND disThread.id = Reads.thread');
$c->where(array(
    'disThread.board' => is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board'],
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
    'Reads.thread AS viewed',
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

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

/* iterate through threads */
$response['results'] = array();
foreach ($threads as $thread) {
    if ($mode != 'rss') {
        $thread->buildCssClass('board-post');
        $thread->buildIcons();
    }
    $thread->calcLastPostPage();
    $thread->getUrl();
    $threadArray = $thread->toArray();

    if ($mode != 'rss') {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($threadArray['createdon'])),
            'user' => $threadArray['author'],
            'username' => $threadArray['username'],
            'author_link' => $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->url.'user/?user='.$threadArray['user'].'">'.$threadArray['username'].'</a>' : $threadArray['username'],
        );
        $latestText = $discuss->getChunk('board/disLastPostBy',$phs);
        $threadArray['latest'] = $latestText;
        $threadArray['latest.id'] = $thread->get('last_post_id');
        $threadArray['views'] = number_format($threadArray['views']);
        $threadArray['replies'] = number_format($threadArray['replies']);
        
        /* unread class */
        $threadArray['unread'] = '';
        if (!$threadArray['viewed'] && $discuss->isLoggedIn) {
            $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
        }
    } else {
        $threadArray['title'] = strip_tags($threadArray['title']);
        $threadArray['createdon'] = strftime('%a, %d %b %Y %I:%M:%S %z',strtotime($threadArray['createdon']));
        $threadArray['url'] = $modx->getOption('site_url').$discuss->url.'thread/?thread='.$thread->get('id').'#dis-post-'.$thread->get('last_post_id');
        $lastPost = $thread->getOne('LastPost');
        $threadArray['excerpt'] = '';
        if ($lastPost) {
            $threadArray['excerpt'] = $lastPost->get('message');
            $threadArray['excerpt'] = $lastPost->stripBBCode($threadArray['excerpt']);
            $threadArray['excerpt'] = strip_tags($threadArray['excerpt']);
            if (strlen($threadArray['excerpt']) > 500) {
                $threadArray['excerpt'] = substr($threadArray['excerpt'],0,500).'...';
            }
        }
    }

    $response['results'][] = $discuss->getChunk($tpl,$threadArray);
}
return $response;
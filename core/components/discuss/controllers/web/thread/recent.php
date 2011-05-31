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
 * Show Recent Posts
 * 
 * @package discuss
 */
$discuss->setSessionPlace('home');
$discuss->setPageTitle($modx->lexicon('discuss.recent_posts'));

/* get default options */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));
$start = $modx->getOption('start',$scriptProperties,0);
$page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;

/* recent posts */
$recent = $discuss->hooks->load('post/recent',array(
    'limit' => $limit,
    'start' => $start,
    'getTotal' => true,
));
$placeholders['recent_posts'] = $recent['results'];

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->request->makeUrl(),
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.recent_posts').' ('.number_format($recent['total']).')','active' => true);

$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* build pagination */
$discuss->hooks->load('pagination/build',array(
    'count' => $recent['total'],
    'id' => 0,
    'view' => 'thread/recent',
    'limit' => $limit,
));

return $placeholders;
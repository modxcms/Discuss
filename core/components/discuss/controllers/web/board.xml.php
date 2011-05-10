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
 * Displays posts for a Board in RSS format
 *
 * @package discuss
 */
/* get board */
if (empty($scriptProperties['board'])) $modx->sendErrorPage();
$integrated = $modx->getOption('i',$scriptProperties,false);
if (!empty($integrated)) $integrated = true;
$board = $modx->call('disBoard','fetch',array(&$modx,$scriptProperties['board'],$integrated));
if ($board == null) $modx->sendErrorPage();

/* set meta */
$discuss->setPageTitle($board->get('name'));

/* add user to board readers */
if (!empty($scriptProperties['read']) && $discuss->isLoggedIn) {
    $board->read($discuss->user->get('id'));
}

$placeholders = $board->toArray();

/* setup default properties */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.threads_per_page',null,20));
$start = $modx->getOption('start',$scriptProperties,0);
$param = $modx->getOption('discuss.page_param',$scriptProperties,'page');

/* get all threads in board */
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;
$posts = $discuss->hooks->load('board/post/getList',array(
    'board' => &$board,
    'limit' => $limit,
    'start' => $start,
    'tpl' => 'post/disBoardPostXml',
    'mode' => 'rss',
    'get_category_name' => true,
));
$placeholders['posts'] = implode("\n",$posts['results']);

@header('Content-type: application/xhtml+xml');
return $placeholders;
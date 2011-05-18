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
 * Displays the Board
 *
 * @package discuss
 */
/* get board */
if (empty($scriptProperties['board'])) $discuss->sendErrorPage();
$integrated = $modx->getOption('i',$scriptProperties,false);
if (!empty($integrated)) $integrated = true;
$board = $modx->call('disBoard','fetch',array(&$modx,$scriptProperties['board'],$integrated));
if ($board == null) $discuss->sendErrorPage();

/* set meta */
$discuss->setSessionPlace('board:'.$scriptProperties['board']);
$discuss->setPageTitle($board->get('name'));

/* add user to board readers */
if (!empty($scriptProperties['read']) && $discuss->user->isLoggedIn) {
    $board->read($discuss->user->get('id'));
}

$placeholders = $board->toArray();

/* grab all subboards */
if (!empty($options['showSubBoards'])) {
    $placeholders['boards'] = $discuss->hooks->load('board/getlist',array(
        'board' => &$board,
    ));
    if (empty($placeholders['boards'])) $placeholders['boards_toggle'] = 'display:none;';
}

/* get all threads in board */
$limit = !empty($scriptProperties['limit']) ? $scriptProperties['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$start = $modx->getOption('start',$scriptProperties,0);
if (empty($start)) {
    $page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
    $page = $page <= 0 ? 1 : $page;
    $start = ($page-1) * $limit;
}

if (!empty($options['showPosts'])) {
    $c = array(
        'limit' => $limit,
        'start' => $start,
        'board' => &$board,
    );
    $posts = $discuss->hooks->load('board/post/getlist',$c);
    $placeholders['posts'] = implode("\n",$posts['results']);
    $discuss->config['pa'] = $posts['total'];
}

/* get board breadcrumb trail */
if (!empty($options['showBreadcrumbs'])) {
    $placeholders['trail'] = $board->buildBreadcrumbs();
}

/* get viewing users */
if (!empty($options['showReaders'])) {
    $placeholders['readers'] = $board->getViewing();
}

/* get pagination */
$discuss->hooks->load('pagination/build',array(
    'count' => !empty($posts) ? $posts['total'] : 0,
    'id' => $board->get('id'),
    'view' => 'board',
    'limit' => $limit,
    'param' => $modx->getOption('discuss.page_param',$scriptProperties,'page'),
));

unset($count,$start,$limit,$url);

/* get moderators */
if (!empty($options['showModerators'])) {
    $placeholders['moderators'] = $board->getModeratorsList();
}

/* action buttons */
$actionButtons = array();
if ($discuss->user->isLoggedIn) {
    if ($modx->hasPermission('discuss.thread_create') && $board->canPost()) {
        $actionButtons[] = array('url' => $discuss->request->makeUrl('thread/new',array('board' => $board->get('id'))), 'text' => $modx->lexicon('discuss.thread_new'));
    }
    $actionButtons[] = array('url' => $discuss->request->makeUrl('board',array('board' => $board->get('id'),'read' => 1)), 'text' => $modx->lexicon('discuss.mark_all_as_read'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$modx->setPlaceholder('discuss.board',$board->get('name'));

return $placeholders;
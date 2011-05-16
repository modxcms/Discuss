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
 * Remove Thread page
 * 
 * @package discuss
 */
/* get thread root */
$thread = $modx->call('disThread', 'fetch', array(&$modx,$scriptProperties['thread']));
if (empty($thread)) $discuss->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.move_thread_header',array('title' => $thread->get('title'))));

/* get breadcrumb trail */
$thread->buildBreadcrumbs();
$placeholders = $thread->toArray();
$placeholders['url'] = $thread->getUrl();

/* process form */
if (!empty($scriptProperties['move-thread']) && !empty($scriptProperties['board'])) {
    if ($thread->move($scriptProperties['board'])) {
        $url = $discuss->url.'board?board='.$thread->get('board');
        $modx->sendRedirect($url);
    }
}

/* board dropdown list */
$boards = $modx->call('disBoard','fetchList',array(&$modx));
$placeholders['boards'] = array();
foreach ($boards as $board) {
    $board['selected'] = !empty($scriptProperties['board']) && $scriptProperties['board'] == $board['id'] ? ' selected="selected"' : '';
    $board['name'] = str_repeat('--',$board['depth']-1).$board['name'];
    $placeholders['boards'][] = $discuss->getChunk('board/disBoardOpt',$board);
}
$placeholders['boards'] = implode("\n",$placeholders['boards']);

unset($boards,$board,$list,$c);

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

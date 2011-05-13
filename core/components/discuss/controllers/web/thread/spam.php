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
 * Mark Thread as Spam page
 *
 * @package discuss
 */
/* get thread root */
$thread = $modx->call('disThread', 'fetch', array(&$modx,$scriptProperties['thread']));
if (empty($thread)) $modx->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.spam_thread_header',array('title' => $thread->get('title'))));

/* get breadcrumb trail */
$thread->buildBreadcrumbs();
$placeholders = $thread->toArray();
$placeholders['url'] = $thread->getUrl();

/* process form */
if (!empty($scriptProperties['spam-thread'])) {
    if ($thread->remove(array(),true,true)) {
        $url = $discuss->url.'board?board='.$thread->get('board');
        $modx->sendRedirect($url);
    }
}

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

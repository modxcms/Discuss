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
$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->select($modx->getSelectColumns('disThread','disThread'));
$c->select(array(
    'FirstPost.title',
    '(SELECT GROUP_CONCAT(pAuthor.id)
        FROM '.$modx->getTableName('disPost').' AS pPost
        INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
        WHERE pPost.thread = disThread.id
     ) AS participants',
));
$c->where(array('id' => $scriptProperties['thread']));
$thread = $modx->getObject('disThread',$c);
if (empty($thread)) $discuss->sendErrorPage();

/* ensure user is IN this PM */
$users = explode(',',$thread->get('users'));
if (!in_array($discuss->user->get('id'),$users)) {
    $discuss->sendErrorPage();
}

$discuss->setPageTitle($modx->lexicon('discuss.remove_message_header',array('title' => $thread->get('title'))));

/* get breadcrumb trail */
$thread->buildBreadcrumbs();
$placeholders = $thread->toArray();

/* process form */
if (!empty($scriptProperties['remove-message'])) {
    if ($thread->remove()) {
        $url = $discuss->request->makeUrl('messages');
        $modx->sendRedirect($url);
    }
}

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

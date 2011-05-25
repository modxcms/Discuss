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
$post = $modx->getObject('disPost',$scriptProperties['post']);
if (empty($post)) $discuss->sendErrorPage();

$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread'),disThread::TYPE_MESSAGE));
if (empty($thread)) $discuss->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.remove_message_header',array('title' => $thread->get('title'))));

/* ensure user is IN this PM */
$users = explode(',',$thread->get('users'));
if (!in_array($discuss->user->get('id'),$users)) {
    $discuss->sendErrorPage();
}

if ($post->remove()) {
    $discuss->logActivity('message_post_remove',$post->toArray(),$post->getUrl());

    $posts = $thread->getMany('Posts');
    if (count($posts) <= 0) {
        $url = $discuss->request->makeUrl('messages');
    } else {
        $url = $discuss->request->makeUrl('messages/view',array('thread' => $thread->get('id')));
    }
    $modx->sendRedirect($url);
}

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

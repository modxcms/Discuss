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
 * Display a thread of posts
 * @package discuss
 */
/* get default properties */
$integrated = $modx->getOption('i',$scriptProperties,false);
if (!empty($integrated)) $integrated = true;
$thread = $modx->getOption('thread',$scriptProperties,false);
if (empty($thread)) $discuss->sendErrorPage();
$thread = $modx->call('disThread', 'fetch', array(&$modx,$thread,'post',$integrated));
if (empty($thread)) $discuss->sendErrorPage();

$discuss->setSessionPlace('thread:'.$thread->get('id'));

/* mark unread if user clicks mark unread */
if (isset($scriptProperties['unread'])) {
    if ($thread->unread($discuss->user->get('id'))) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (!empty($scriptProperties['sticky'])) {
    if ($thread->stick()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (isset($scriptProperties['sticky']) && $scriptProperties['sticky'] == 0) {
    if ($thread->unstick()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (!empty($scriptProperties['lock'])) {
    if ($thread->lock()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (isset($scriptProperties['lock']) && $scriptProperties['lock'] == 0) {
    if ($thread->unlock()) {
        $modx->sendRedirect($discuss->url.'board?board='.$thread->get('board'));
    }
}
if (!empty($scriptProperties['subscribe'])) {
    if ($thread->addSubscription($discuss->user->get('id'))) {
        $modx->sendRedirect($discuss->url.'thread?thread='.$thread->get('id'));
    }
}
if (!empty($scriptProperties['unsubscribe'])) {
    if ($thread->removeSubscription($discuss->user->get('id'))) {
        $modx->sendRedirect($discuss->url.'thread?thread='.$thread->get('id'));
    }
}


/* get posts */
if (!empty($options['showPosts'])) {
    $posts = $discuss->hooks->load('post/getThread',array(
        'thread' => &$thread,
    ));
    $thread->set('posts',$posts['results']);
    unset($postsOutput,$pa,$plist,$userUrl,$profileUrl);
}

/* get board breadcrumb trail */
if (!empty($options['showBreadcrumbs']) && empty($scriptProperties['print'])) {
    $thread->buildBreadcrumbs();
}

/* up view count for thread */
if (empty($scriptProperties['print'])) {
    $thread->view();
}

$placeholders = $thread->toArray();
$placeholders['views'] = number_format($placeholders['views']);
$placeholders['replies'] = number_format($placeholders['replies']);

/* set css class of thread */
$thread->buildCssClass();

/* get viewing users */
if (!empty($options['showViewing']) && empty($scriptProperties['print'])) {
    $placeholders['readers'] = empty($scriptProperties['print']) ? $thread->getViewing() : '';
}

/* get moderator status */
$isModerator = $thread->isModerator($discuss->user->get('id'));
$isAdmin = $discuss->user->isAdmin();

/* action buttons */
$actionButtons = array();
if ($discuss->user->isLoggedIn && empty($scriptProperties['print'])) {
    $board = $thread->getOne('Board');
    if ($board->canPost() && $thread->canReply()) {
        $actionButtons[] = array('url' => $discuss->url.'thread/reply?thread='.$thread->get('id'), 'text' => $modx->lexicon('discuss.reply_to_thread'));
    }
    $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&unread=1', 'text' => $modx->lexicon('discuss.mark_unread'));
    if ($thread->canUnsubscribe()) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&unsubscribe=1', 'text' => $modx->lexicon('discuss.unsubscribe'));
    } elseif ($thread->canSubscribe()) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&subscribe=1', 'text' => $modx->lexicon('discuss.subscribe'));
    }
    /* TODO: Send thread by email - 1.1
     * if ($modx->hasPermission('discuss.thread_send') {
     *   $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_send'));
     * }
     */
     if ($thread->canPrint()) {
         $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&print=1', 'text' => $modx->lexicon('discuss.print'));
     }
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* thread action buttons */
$actionButtons = array();
if ($discuss->user->isLoggedIn && ($isModerator || $isAdmin) && empty($scriptProperties['print'])) {
    if ($thread->canMove()) {
        $actionButtons[] = array('url' => $discuss->url.'thread/move?thread='.$thread->get('id'), 'text' => $modx->lexicon('discuss.thread_move'));
    }
    if ($thread->canRemove()) {
        $actionButtons[] = array('url' => $discuss->url.'thread/remove?thread='.$thread->get('id'), 'text' => $modx->lexicon('discuss.thread_remove'));
        $actionButtons[] = array('url' => $discuss->url.'thread/spam?thread='.$thread->get('id'), 'text' => $modx->lexicon('discuss.thread_spam'));
    }

    if ($thread->canUnlock()) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&lock=0', 'text' => $modx->lexicon('discuss.thread_unlock'));
    } else if ($thread->canLock()) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&lock=1', 'text' => $modx->lexicon('discuss.thread_lock'));
    }
    if ($thread->canUnstick()) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&sticky=0', 'text' => $modx->lexicon('discuss.thread_unstick'));
    } else if ($thread->canStick()) {
        $actionButtons[] = array('url' => $discuss->url.'thread?thread='.$thread->get('id').'&sticky=1', 'text' => $modx->lexicon('discuss.thread_stick'));
    }
    /**
     * TODO: Merge thread - 1.1
     * $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.thread_merge'));
     */
}
$placeholders['threadactionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$placeholders['discuss.error_panel'] = $discuss->getChunk('Error');
$placeholders['discuss.thread'] = $thread->get('title');

/* get pagination */
if (empty($scriptProperties['print'])) {
    $discuss->hooks->load('pagination/build',array(
        'count' => $posts['total'],
        'id' => $thread->get('id'),
        'view' => 'thread/',
        'limit' => $posts['limit'],
    ));
} else {
    $placeholders['pagination'] = '';
}

/* mark as read */
if (empty($scriptProperties['print'])) {
    $thread->read($discuss->user->get('id'));
}

$discuss->setPageTitle($thread->get('title'));
return $placeholders;
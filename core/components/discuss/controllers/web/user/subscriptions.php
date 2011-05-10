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
 *
 * @package discuss
 */
/* get user */
if (empty($scriptProperties['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$scriptProperties['user']);
if ($user == null) { $modx->sendErrorPage(); }

if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}

$modx->lexicon->load('discuss:user');
$placeholders = $user->toArray();
$discuss->setPageTitle($modx->lexicon('discuss.user_subscriptions_header',array('user' => $user->get('username'))));

/* handle unsubscribing */
if (!empty($_POST) && !empty($_POST['remove'])) {
    foreach ($_POST['remove'] as $threadId) {
        $notification = $modx->getObject('disUserNotification',array('thread' => $threadId));
        if ($notification == null) continue;
        $notification->remove();
    }
    $url = $discuss->url.'user/subscriptions?user='.$user->get('id');
    $modx->sendRedirect($url);
}

/* get notifications */
$c = $modx->newQuery('disThread');
$c->select($modx->getSelectColumns('disThread','disThread'));
$c->select(array(
    'Board.name AS board_name',
    'LastPost.id AS last_post_id',
    'LastPost.createdon AS createdon',
    'FirstPost.id AS first_post_id',
    'FirstPost.title AS title',
    'FirstPost.author AS author',
    'FirstAuthor.username AS author_username',
));
$c->innerJoin('disUserNotification','Notifications');
$c->innerJoin('disUser','FirstAuthor');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disBoard','Board');
$c->where(array(
    'Notifications.user' => $user->get('id'),
));
$c->sortby('FirstPost.title','ASC');
$subscriptions = $modx->getCollection('disThread',$c);
$placeholders['subscriptions'] = array();
foreach ($subscriptions as $subscription) {
    $subscriptionArray = $subscription->toArray();
    $subscriptionArray['class'] = 'dis-board-li';
    $subscriptionArray['createdon'] = strftime($discuss->dateFormat,strtotime($subscriptionArray['createdon']));
    $placeholders['subscriptions'][] = $discuss->getChunk('user/disUserSubscriptionRow',$subscriptionArray);
}
$placeholders['subscriptions'] = implode("\n",$placeholders['subscriptions']);

/* output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;
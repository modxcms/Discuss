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
 * Send out all notifications for a post
 *
 * @var Discuss $discuss
 * @var modX $modx
 * @var array $scriptProperties
 * 
 * @package discuss
 * @subpackage hooks
 */
if (empty($scriptProperties['title']) || empty($scriptProperties['thread'])) return false;

if (!$modx->getOption('discuss.enable_notifications',null,true)) {
    return true;
}
/* setup default properties */
$type = $modx->getOption('type',$scriptProperties,'thread');
$subject = $modx->getOption('subject',$scriptProperties,$modx->getOption('discuss.notification_new_post_subject',null,'New Post'));
$subject = str_replace('[[+title]]',$scriptProperties['title'],$subject);

$tpl = $modx->getOption('tpl',$scriptProperties,$modx->getOption('discuss.notification_new_post_chunk',null,'emails/disNotificationEmail'));

/* get notification subscriptions */
$c = $modx->newQuery('disUserNotification');
if (!empty($scriptProperties['thread'])) {
    $c->where(array(
        'thread' => $scriptProperties['thread'],
    ));
}
if (!empty($scriptProperties['board']) && empty($scriptProperties['thread'])) {
    $c->orCondition(array(
        'board:=' => $scriptProperties['board'],
        'AND:thread:=' => 0,
    ),null,2);
}
$notifications = $modx->getCollection('disUserNotification',$c);

/* build thread url */
$url = '';
$view = ($type == 'message') ? 'messages/view' : 'thread/';
if (!empty($scriptProperties['post'])) {
    /* @var disPost $post */
    $post = $modx->getObject('disPost', (int)$scriptProperties['post']);
    if ($post) {
        $url = $post->getUrl($view);
    }
}
if (empty($url)) {
    /* @var disThread $thread */
    $thread = $modx->getObject('disThread',(int)$scriptProperties['thread']);
    if ($thread) {
        $url = $thread->getUrl(true);
    }
}

/* send out notifications */
/** @var disUserNotification $notification */
foreach ($notifications as $notification) {
    /** @var disUser $user */
    $user = $modx->getObject('disUser',$notification->get('user'));
    if ($user == null) { continue; }
    /** @var disThread $thread */
    $thread = $modx->getObject('disThread',$notification->get('thread'));
    if ($thread == null) { continue; }
    /* dont notify on own posts! */
    //if ($thread->get('author_last') == $notification->get('user')) { continue; }

    $emailProperties = array_merge($scriptProperties,$user->toArray());
    $emailProperties['tpl'] = $tpl;
    $emailProperties['name'] = $scriptProperties['title'];
    $emailProperties['type'] = $type;
    $emailProperties['url'] = $url;
    $sent = $discuss->sendEmail($user->email,$user->get('username'),$subject,$emailProperties);
    unset($emailProperties);
}
return true;

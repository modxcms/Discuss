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
 * Post a reply to a post
 *
 * @var modX $modx
 * @var Discuss $discuss
 * @var fiHooks $hook
 * @var string $submitVar
 * 
 * @package discuss
 */
$discuss =& $modx->discuss;
$modx->lexicon->load('discuss:post');
$fields = $hook->getValues();
unset($fields[$submitVar]);

if (empty($fields['post'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
/** @var disPost $post */
$post = $modx->getObject('disPost',$fields['post']);
if ($post == null) return false;

/** @var disThread $thread */
$thread = $post->getOne('Thread');
if ($thread == null) return false;

/* first check attachments for validity */
$attachments = array();
if (!empty($_FILES) && !empty($_FILES['attachment1']['tmp_name'])) {
    $result = $discuss->hooks->load('post/attachment/verify',array(
        'attachments' => &$_FILES,
    ));
    if (!empty($result['errors'])) {
        $hook->addError('attachments',implode('<br />',$result['errors']));
    }
    $attachments = $result['attachments'];
}

/* if any errors, return */
if (!empty($hook->errors)) {
    return false;
}

$maxSize = (int)$modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > 0) {
    if ($maxSize > strlen($fields['message'])) $maxSize = strlen($fields['message']);
    $fields['message'] = substr($fields['message'],0,$maxSize);
}

/* create post object and set fields */
/** @var disPost $newPost */
$newPost = $modx->newObject('disPost');
$newPost->fromArray($fields);
$newPost->set('author',$discuss->user->get('id'));
$newPost->set('parent',$post->get('id'));
$newPost->set('board',$post->get('board'));
$newPost->set('createdon',$discuss->now());
$newPost->set('ip',$discuss->getIp());

/* fire before post save event */
$rs = $modx->invokeEvent('OnDiscussBeforePostSave',array(
    'post' => &$newPost,
    'thread' => &$thread,
    'mode' => 'reply',
));
$canSave = $discuss->getEventResult($rs);
if (!empty($canSave)) {
    return $modx->error->failure($canSave);
}
/* save post */
if ($newPost->save() == false) {
    $hook->addError('title',$modx->lexicon('discuss.post_err_save'));
    return false;
}

/* upload attachments */
foreach ($attachments as $file) {
    /** @var disPostAttachment $attachment */
    $attachment = $modx->newObject('disPostAttachment');
    $attachment->set('post',$newPost->get('id'));
    $attachment->set('board',$newPost->get('board'));
    $attachment->set('filename',$file['name']);
    $attachment->set('filesize',$file['size']);
    $attachment->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));

    if ($attachment->upload($file)) {
        $attachment->save();
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] An error occurred while trying to upload the attachment: '.print_r($file,true));
    }
}

/* allow editing of actionbutton values */
if ($thread->canSubscribe() || $thread->canUnsubscribe()) {
    if(!empty($fields['notify']) && $fields['notify'] == 1) {
        $thread->addSubscription($discuss->user->get('id'));
    } else {
        if($thread->hasSubscription($discuss->user->get('id'))) {
            $thread->removeSubscription($discuss->user->get('id'));
        }
    }
}
if($thread->canStick() || $thread->canUnstick()) {
    if (!empty($fields['sticky']) && $fields['sticky'] == 1) {
        $thread->stick();
    } else {
        if($thread->get('sticky')) {
            $thread->unstick();
        }
    }
}
if($thread->canLock() || $thread->canUnlock()) {
    if (!empty($fields['locked']) && $fields['locked'] == 1) {
        $thread->lock();
    } else {
        if($thread->get('locked')) {
            $thread->unlock();
        }
    }
}

$discuss->user->checkForPostGroupAdvance();

/* send out notifications */
$discuss->hooks->load('notifications/send',array(
    'board' => $newPost->get('board'),
    'post' => $newPost->get('id'),
    'thread' => $thread->get('id'),
    'title' => $newPost->get('title'),
    'message' => $newPost->getContent(),
    'author' => $discuss->user->get('username'),
    'subject' => $modx->getOption('discuss.notification_new_post_subject',null,'New Post'),
    'tpl' => $modx->getOption('discuss.notification_new_post_chunk',null,'emails/disNotificationEmail'),
));

/* fire post save event */
$modx->invokeEvent('OnDiscussPostSave',array(
    'post' => &$newPost,
    'thread' => &$thread,
    'mode' => 'reply',
));

/* log activity */
$discuss->logActivity('thread_reply',$thread->toArray(),$thread->getUrl());

/* mark thread unread for all users, except poster */
$thread->unreadForAll();
$thread->read($discuss->user->get('id'));

/* clear recent posts cache */
$modx->cacheManager->delete('discuss/board/recent/');

$url = $newPost->getUrl('thread/',true);
$modx->sendRedirect($url);
return true;

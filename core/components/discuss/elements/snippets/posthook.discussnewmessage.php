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
 * @var array $fields
 * @var Discuss $discuss
 *
 * @var string $submitVar
 * @var fiHooks $hook
 *
 * @package discuss
 */
$discuss =& $modx->discuss;
$modx->lexicon->load('discuss:post');
$fields = $hook->getValues();
unset($fields[$submitVar]);

/* first check attachments for validity */
$attachments = array();
if (!empty($_FILES) && $_FILES['attachment1']['error'] == 0) {
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

/* validate post length */
$maxSize = (int)$modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > 0) {
    if ($maxSize > strlen($fields['message'])) $maxSize = strlen($fields['message']);
    $fields['message'] = substr($fields['message'],0,$maxSize);
}

/* get participants */
$participantsIds = array();
$participants = explode(',',$fields['participants_usernames']);
foreach ($participants as $participant) {
    /** @var disUser $user */
    $user = $modx->getObject('disUser',array('username' => $participant));
    if ($user) {
        $participantsIds[] = $user->get('id');
    }
}
$participantsIds = array_unique($participantsIds);

/* create post object and set fields */
/** @var disPost $post */
$post = $modx->newObject('disPost');
$post->fromArray($fields);
$post->set('author',$discuss->user->get('id'));
$post->set('parent',0);
$post->set('board',0);
$post->set('thread',0);
$post->set('createdon',$discuss->now());
$post->set('ip',$discuss->getIp());
$post->set('private',true);

/* fire before post save event */
$rs = $modx->invokeEvent('OnDiscussBeforePostSave',array(
    'post' => &$post,
    'mode' => 'new',
));
$canSave = $discuss->getEventResult($rs);
if (!empty($canSave)) {
    $hook->addError('title',$modx->error->failure($canSave));
    return false;
}

/* save post */
if (!$post->save()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not create new thread: '.print_r($post->toArray(),true));
    $hook->addError('title',$modx->lexicon('discuss.post_err_create'));
    return false;
}

/* @var disThread $thread */
$thread = $post->getOne('Thread');
$thread->set('private',true);
$thread->set('users',implode(',',$participantsIds));
$thread->save();

/* set participants, add notifications */
foreach ($participantsIds as $participant) {
    /** @var disThreadUser $threadUser */
    $threadUser = $modx->newObject('disThreadUser');
    $threadUser->set('thread',$thread->get('id'));
    $threadUser->set('user',$participant);
    $threadUser->set('author',$thread->get('author_first') == $participant ? true : false);
    $threadUser->save();

    /** @var disUserNotification $subscription */
    $subscription = $modx->newObject('disUserNotification');
    $subscription->set('thread',$thread->get('id'));
    $subscription->set('user',$participant);
    $subscription->set('board',0);
    $subscription->save();
}

/* upload attachments */
foreach ($attachments as $file) {
    /** @var disPostAttachment $attachment */
    $attachment = $modx->newObject('disPostAttachment');
    $attachment->set('post',$post->get('id'));
    $attachment->set('board',$post->get('board'));
    $attachment->set('filename',$file['name']);
    $attachment->set('filesize',$file['size']);

    if ($attachment->upload($file)) {
        $attachment->save();
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] '.$modx->lexicon('attachment_err_upload',array(
            'error' => print_r($file,true),
        )));
    }
}

/* send notifications */
$discuss->hooks->load('notifications/send',array(
    'post' => $post->get('id'),
    'thread' => $thread->get('id'),
    'title' => $post->get('title'),
    'message' => $post->getContent(),
    'sender' => $discuss->user->get('username'),
    'type' => 'message',
    'subject' => $modx->getOption('discuss.notification_new_message_subject',null,'New Message'),
    'tpl' => $modx->getOption('discuss.notification_new_message_chunk',null,'emails/disMessageNotificationEmail'),
));


/* fire post save event */
$modx->invokeEvent('OnDiscussPostSave',array(
    'post' => &$post,
    'thread' => &$thread,
    'board' => &$board,
    'mode' => 'new',
));

$url = $post->getUrl('messages/view',true);
$modx->sendRedirect($url);
return true;

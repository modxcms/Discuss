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
 * @var fiHooks $hook
 * @var Discuss $discuss
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
if (!empty($_FILES) && ($_FILES['attachment1']['error'] === 0)) {
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

/* format post */
$maxSize = (int)$modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > 0) {
    if ($maxSize > strlen($fields['message'])) $maxSize = strlen($fields['message']);
    $fields['message'] = substr($fields['message'],0,$maxSize);
}

/* get participants */
$participantsIds = array();
if (isset($fields['add_participants']) && !empty($fields['add_participants']) && $modx->discuss->user->get('id') == $thread->get('author_first')) {
    $participants = explode(',',$fields['add_participants']);
    foreach ($participants as $participant) {
        /** @var disUser $user */
        $user = $modx->getObject('disUser',array('username' => $participant));
        if ($user) {
            $participantsIds[] = $user->get('id');
        }
    }
    $participantsIds = array_unique($participantsIds);
}

/* create post object and set fields */
/** @var disPost $newPost */
$newPost = $modx->newObject('disPost');
$newPost->fromArray($fields);
$newPost->set('author',$discuss->user->get('id'));
$newPost->set('parent',$post->get('id'));
$newPost->set('board',0);
$newPost->set('createdon',$discuss->now());
$newPost->set('ip',$discuss->getIp());
$newPost->set('private',true);

/* fire before post save event */
$rs = $modx->invokeEvent('OnDiscussBeforeMessageSave',array(
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
    return $modx->error->failure($modx->lexicon('discuss.post_err_reply'));
}

/* add participants, add notifications */
if (isset($participantsIds) && !empty($participantsIds) && $modx->discuss->user->get('id') == $thread->get('author_first')) {
    $users = $thread->get('users');
    $users = explode(',', $users);
    $users = array_merge($users, $participantsIds);
    $users = array_unique($users);
    $thread->set('users',implode(',',$users));
    $thread->save();
    foreach ($participantsIds as $participant) {
        /** @var disThreadUser $threadUser */
        $threadUser = $modx->newObject('disThreadUser');
        $threadUser->set('thread',$thread->get('id'));
        $threadUser->set('user',$participant);
        $threadUser->set('author',$thread->get('author_first') == $participant ? true : false);
        $threadUser->save();

        /* add new notification */
        /** @var disUserNotification $notify */
        $notify = $modx->newObject('disUserNotification');
        $notify->set('thread',$thread->get('id'));
        $notify->set('user',$participant);
        $notify->set('board',0);
        $notify->save();

    }
}
/* Remove read status for all other users */
$modx->removeCollection('disThreadRead', array('thread' => $thread->get('id'), 'user:!=' => $discuss->user->get('id')));
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

/* send out notifications */
$discuss->hooks->load('notifications/send',array(
    'post' => $newPost->get('id'),
    'thread' => $thread->get('id'),
    'title' => $newPost->get('title'),
    'message' => $newPost->getContent(),
    'sender' => $discuss->user->get('username'),
    'type' => 'message',
    'subject' => $modx->getOption('discuss.notification_new_message_subject',null,'New Message'),
    'tpl' => $modx->getOption('discuss.notification_new_message_chunk',null,'emails/disMessageNotificationEmail'),
));

/* fire post save event */
$modx->invokeEvent('OnDiscussMessageSave',array(
    'post' => &$newPost,
    'thread' => &$thread,
    'mode' => 'reply',
));

$url = $newPost->getUrl('messages/view',true);
$modx->sendRedirect($url);
return true;
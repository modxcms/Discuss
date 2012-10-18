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
 * Modify a post in a Thread
 * @package discuss
 */
$discuss =& $modx->discuss;
$modx->lexicon->load('discuss:post');
$fields = $hook->getValues();
unset($fields[$submitVar]);

if (empty($fields['post'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$post = $modx->getObject('disPost',$fields['post']);
if ($post == null) return false;

$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread'),disThread::TYPE_MESSAGE));
if (empty($thread)) return false;

/* first check attachments for validity */
$attachments = array();
if (!empty($_FILES)) {
    $result = $discuss->hooks->load('post/attachment/verify',array(
        'attachments' => &$_FILES,
    ));
    if (!empty($result['errors'])) {
        $hook->addError('attachment1',implode('<br />',$result['errors']));
    }
    $attachments = $result['attachments'];
}

/* format post */
$maxSize = (int)$modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > 0) {
    if ($maxSize > strlen($fields['message'])) $maxSize = strlen($fields['message']);
    $fields['message'] = substr($fields['message'],0,$maxSize);
}

/* get participants */
$participantsIds = array();
$participants = explode(',',$fields['add_participants']);
foreach ($participants as $participant) {
    $user = $modx->getObject('disUser',array('username' => $participant));
    if ($user) {
        $participantsIds[] = $user->get('id');
    }
}
$participantsIds = array_unique($participantsIds);

/* set fields */
$post->fromArray($fields);
$post->set('ip',$discuss->getIp());

/* if past courtesy wait time, set editedby */
$courtesyWait = $modx->getOption('discuss.courtesy_edit_wait',null,60);
$createdon = strtotime($post->get('createdon'));
$diff = time() - $createdon;
if ($diff > $courtesyWait) {
    $post->set('editedon',$discuss->now());
    $post->set('editedby',$discuss->user->get('id'));
}

$oldAttachments = $post->getMany('Attachments');

/* get rid of removed attachments */
$idx = 1;
foreach ($oldAttachments as $oldAttachment) {
    if (!isset($_POST['attachment'.$idx])) {
        $oldAttachment->remove();
    }
    $idx++;
}

/* upload new attachments */
foreach ($attachments as $file) {
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

if (!$post->save()) {
    $hook->addError('title',$modx->lexicon('discuss.post_err_modify'));
    return false;
}

/* set participants, add notifications */
$users = $thread->get('users');
$users = explode(',', $users);
$users = array_merge($users, $participantsIds);
$users = array_unique($users);
$thread->set('users',implode(',',$users));
$thread->save();
foreach ($participantsIds as $participant) {
    $threadUser = $modx->newObject('disThreadUser');
    $threadUser->set('thread',$thread->get('id'));
    $threadUser->set('user',$participant);
    $threadUser->set('author',$thread->get('author_first') == $participant ? true : false);
    $threadUser->save();

    $notify = $modx->newObject('disUserNotification');
    $notify->set('thread',$thread->get('id'));
    $notify->set('user',$participant);
    $notify->set('board',0);
    $notify->save();
}

/* redirect */
$url = $post->getUrl('messages/view',true);
$modx->sendRedirect($url);
return true;
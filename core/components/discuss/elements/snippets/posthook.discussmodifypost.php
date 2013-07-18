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
 *
 * @var modX $modx
 * @var fiHooks $hook
 * @var string $submitVar
 * @package discuss
 */
/** @var Discuss $discuss */
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
if (!empty($_FILES)) {
    $result = $discuss->hooks->load('post/attachment/verify',array(
        'attachments' => &$_FILES,
    ));
    if (!empty($result['errors'])) {
        $hook->addError('attachment1',implode('<br />',$result['errors']));
    }
    $attachments = $result['attachments'];
}

$maxSize = (int)$modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > 0) {
    if ($maxSize > strlen($fields['message'])) $maxSize = strlen($fields['message']);
    $fields['message'] = substr($fields['message'],0,$maxSize);
}

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
/** @var disPostAttachment $oldAttachment */
foreach ($oldAttachments as $oldAttachment) {
    if (!isset($_POST['attachment'.$idx])) {
        $oldAttachment->remove();
    }
    $idx++;
}

/* upload new attachments */
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

/* allow editing of actionbutton values */
if (!empty($fields['notify']) && $fields['notify'] == 1) {
    $thread->addSubscription($discuss->user->get('id'));
} else {
    if($thread->hasSubscription($discuss->user->get('id'))) {
        $thread->removeSubscription($discuss->user->get('id'));
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

$class_key = '';
switch($fields['class_key']) {
    case 'disThreadQuestion':
        $class_key = $fields['class_key'];
        break;
        
    case 'disThreadDiscussion':
    default:
        $class_key = 'disThreadDiscussion';
}
$thread->set('class_key',$class_key);
$thread->save();

if (!$post->save()) {
    $hook->addError('title',$modx->lexicon('discuss.post_err_modify'));
    return false;
}

/* log activity */
$discuss->logActivity('post_modify',$post->toArray(),$post->getUrl());

$url = $post->getUrl('thread/');
$modx->sendRedirect($url);
return true;
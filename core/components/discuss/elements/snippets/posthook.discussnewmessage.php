<?php
/**
 * Post a reply to a post
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
    $user = $modx->getObject('disUser',array('username' => $participant));
    if ($user) {
        $participantsIds[] = $user->get('id');
    }
}
$participantsIds = array_unique($participantsIds);

/* create post object and set fields */
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

/* update thread */
$thread = $post->getOne('Thread');
$thread->set('private',true);
$thread->set('users',implode(',',$participantsIds));
$thread->save();

/* set participants, add notifications */
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

/* upload attachments */
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

$url = $post->getUrl('messages/view');
$modx->sendRedirect($url);
return true;
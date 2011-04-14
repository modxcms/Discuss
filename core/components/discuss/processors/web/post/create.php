<?php
/**
 * Create a new thread
 *
 * @package discuss
 * @subpackage processors
 */
$modx->lexicon->load('discuss:post');

/* field validation */
if (empty($_POST['title'])) $modx->error->addField('title',$modx->lexicon('discuss.post_err_ns_title'));
if (empty($_POST['message'])) $modx->error->addField('message',$modx->lexicon('discuss.post_err_ns_message'));

/* first check attachments for validity */
$attachments = array();
if (!empty($_FILES)) {
    $result = $discuss->hooks->load('post/attachment/verify',array(
        'attachments' => &$_FILES,
    ));
    if (!empty($result['errors'])) {
        $modx->error->addField('attachments',implode('<br />',$result['errors']));
    }
    $attachments = $result['attachments'];
}

/* if any errors, return */
if ($modx->error->hasError()) {
    return $modx->error->failure();
}

/* validate post length */
$maxSize = $modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > strlen($_POST['message'])) $maxSize = strlen($_POST['message']);
$_POST['message'] = substr($_POST['message'],0,$maxSize);

/* create post object and set fields */
$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',0);
$post->set('board',$board->get('id'));

/* fire before post save event */
$rs = $modx->invokeEvent('OnDiscussBeforePostSave',array(
    'post' => &$post,
    'mode' => 'new',
));
$canSave = $discuss->getEventResult($rs);
if (!empty($canSave)) {
    return $modx->error->failure($canSave);
}

/* save post */
if (!$post->save()) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_create'));
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
    'board' => $board->get('id'),
    'thread' => $post->get('id'),
    'title' => $post->get('title'),
    'subject' => $modx->getOption('discuss.notification_new_post_subject'),
    'tpl' => $modx->getOption('discuss.notification_new_post_chunk'),
));

/* fire post save event */
$modx->invokeEvent('OnDiscussPostSave',array(
    'post' => &$post,
    'mode' => 'new',
));

return $modx->error->success();
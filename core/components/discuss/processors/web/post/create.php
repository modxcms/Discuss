<?php
/**
 * Create a new thread
 *
 * @package discuss
 * @subpackage processors
 */

/* field validation */
if (empty($_POST['title'])) $modx->error->addField('title','Please enter a valid post title.');
if (empty($_POST['message'])) $modx->error->addField('message','Please enter a message.');

/* first check attachments for validity */
$attachments = array();
if (!empty($_FILES)) {
    $result = $modx->hooks->load('post/attachment/verify',array(
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

/* create post */
$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',0);
$post->set('board',$board->get('id'));
$post->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
$post->set('ip',$_SERVER['REMOTE_ADDR']);

if (!$post->save()) {
    $modx->error->failure('An error occurred while trying to save the new thread.');
}

/* upload attachments */
foreach ($attachments as $file) {
    $attachment = $modx->newObject('disPostAttachment');
    $attachment->set('post',$post->get('id'));
    $attachment->set('board',$post->get('board'));
    $attachment->set('filename',$file['name']);
    $attachment->set('filesize',$file['size']);
    $attachment->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));

    if ($attachment->upload($file)) {
        $attachment->save();
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] An error occurred while trying to upload the attachment: '.print_r($file,true));
    }
}

/* send notifications */
$modx->hooks->load('notifications/send',array(
    'board' => $board->get('id'),
    'thread' => $post->get('id'),
    'title' => $post->get('title'),
    'subject' => '[Discuss] A New Post Has Been Made',
));

return $modx->error->success();
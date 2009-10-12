<?php
/**
 * Post a reply to a post via AJAX
 *
 * @package discuss
 */
if (empty($_POST['post'])) return $modx->error->failure('Parent Post not specified');
$parent = $modx->getObject('disPost',$_POST['post']);
if ($parent == null) return $modx->error->failure('Parent Post not found.');

$thread = $parent->getThreadRoot();
if ($thread == null) return $modx->error->failure('Thread not found.');

/* validation */
if (empty($_POST['title'])) { $modx->error->addField('title','Please enter a title for this post.'); }
if (empty($_POST['message'])) { $modx->error->addField('message','Please enter a valid message.'); }

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
    return $modx->error->failure('Please correct the errors in your form.');
}

$maxSize = $modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > strlen($_POST['message'])) $maxSize = strlen($_POST['message']);
$_POST['message'] = substr($_POST['message'],0,$maxSize);

$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',$parent->get('id'));
$post->set('board',$parent->get('board'));
$post->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
$post->set('ip',$_SERVER['REMOTE_ADDR']);


if ($post->save() == false) {
    return $modx->error->failure('An error occurred while trying to post a reply.');
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
        $modx->log(MODX_LOG_LEVEL_ERROR,'[Discuss] An error occurred while trying to upload the attachment: '.print_r($file,true));
    }
}

if (!empty($_POST['notify'])) {
    $notify = $modx->newObject('disUserNotification');
    $notify->set('user',$modx->user->get('id'));
    $notify->set('post',$post->get('id'));
    $notify->save();
}

/* send out notifications */
$modx->hooks->load('notifications/send',array(
    'board' => $post->get('board'),
    'thread' => $thread->get('id'),
    'title' => $thread->get('title'),
    'subject' => '[Discuss] A Reply Has Been Made',
));

return $modx->error->success('',$post);
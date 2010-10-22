<?php
/**
 * Post a reply to a post via AJAX
 *
 * @package discuss
 */
$modx->lexicon->load('discuss:post');

if (empty($_POST['post'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$parent = $modx->getObject('disPost',$_POST['post']);
if ($parent == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));

$thread = $parent->getThreadRoot();
if ($thread == null) return $modx->error->failure($modx->lexicon('discuss.thread_err_nf'));

/* validation */
if (empty($_POST['title'])) { $modx->error->addField('title',$modx->lexicon('discuss.post_err_ns_title')); }
if (empty($_POST['message'])) { $modx->error->addField('message',$modx->lexicon('discuss.post_err_ns_message')); }

/* first check attachments for validity */
$attachments = array();
if (!empty($_FILES) && $_FILES['attachment1']['error'] == 0) {
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
    return $modx->error->failure($modx->lexicon('discuss.correct_errors'));
}

$maxSize = $modx->getOption('discuss.maximum_post_size',null,30000);
if ($maxSize > strlen($_POST['message'])) $maxSize = strlen($_POST['message']);
$_POST['message'] = substr($_POST['message'],0,$maxSize);

/* create post object and set fields */
$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',$parent->get('id'));
$post->set('board',$parent->get('board'));

/* fire before post save event */
$rs = $modx->invokeEvent('OnDiscussBeforePostSave',array(
    'post' => &$post,
    'mode' => 'reply',
));
$canSave = $discuss->getEventResult($rs);
if (!empty($canSave)) {
    return $modx->error->failure($canSave);
}

/* save post */
if ($post->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_reply'));
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
    'subject' => $modx->getOption('discuss.notification_new_post_subject'),
    'tpl' => $modx->getOption('discuss.notification_new_post_chunk'),
));

/* fire post save event */
$modx->invokeEvent('OnDiscussPostSave',array(
    'post' => &$post,
    'mode' => 'reply',
));

return $modx->error->success('',$post);
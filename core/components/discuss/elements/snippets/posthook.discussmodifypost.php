<?php
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

$url = $post->getUrl();
$modx->sendRedirect($url);

return true;
<?php
/**
 * Modify a post
 *
 * @package discuss
 * @subpackage processors
 */
$modx->lexicon->load('discuss:post');

$errors = array();
if (empty($_POST['title'])) $errors['title'] = $modx->lexicon('discuss.post_err_ns_title');
if (empty($_POST['message'])) $errors['message'] = $modx->lexicon('discuss.post_err_ns_message');

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

if (empty($errors)) {
    $_POST['message'] = substr($_POST['message'],0,$modx->getOption('discuss.maximum_post_size',null,30000));

    $post->fromArray($_POST);
    $post->set('author',$modx->user->get('id'));
    $post->set('ip',$_SERVER['REMOTE_ADDR']);

    /* if past courtesy wait time, set editedby */
    $courtesyWait = $modx->getOption('discuss.courtesy_edit_wait',null,60);
    $createdon = strtotime($post->get('createdon'));
    $diff = time() - $createdon;
    if ($diff > $courtesyWait) {
        $post->set('editedon',strftime('%Y-%m-%d %H:%M:%S'));
        $post->set('editedby',$modx->user->get('id'));
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


    $post->save();

    $url = $modx->makeUrl($modx->getOption('discuss.thread_resource')).'?thread='.$thread->get('id');
    $modx->sendRedirect($url);
}
$modx->toPlaceholders($errors,'error');
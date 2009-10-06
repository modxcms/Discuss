<?php
/**
 * courtesy_edit_wait
 *
 * @package discuss
 * @subpackage processors
 */

$errors = array();
if (empty($_POST['title'])) $errors['title'] = 'Please enter a valid post title.';
if (empty($_POST['message'])) $errors['message'] = 'Please enter a message.';

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

    $post->save();

    $url = $modx->makeUrl($modx->getOption('discuss.thread_resource')).'?thread='.$thread->get('id');
    $modx->sendRedirect($url);
}
$modx->toPlaceholders($errors,'error');
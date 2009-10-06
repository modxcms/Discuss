<?php
/**
 * @package discuss
 */
$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',0);
$post->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
$post->set('ip',$_SERVER['REMOTE_ADDR']);

/* now output html back to browser */
$post->set('username',$modx->user->get('username'));

$o = $modx->discuss->getChunk('disPost',$post->toArray());

return $modx->error->success($o,$post);
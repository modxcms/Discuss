<?php
/**
 * Show a preview of the post before it is made
 *
 * @package discuss
 * @subpackage processors
 */
$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',0);
$post->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
$post->set('ip',$_SERVER['REMOTE_ADDR']);

/* now output html back to browser */
$post->set('username',$modx->user->get('username'));

$postArray = $post->toArray();
$postArray['content'] = $post->getContent();

$o = $discuss->getChunk('disPost',$postArray);

return $modx->error->success($o,$post);
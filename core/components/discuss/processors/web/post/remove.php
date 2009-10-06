<?php
/**
 * @package discuss
 */

if (empty($_POST['post'])) return $modx->error->failure('Post not specified!');
$post = $modx->getObject('disPost',$_POST['post']);
if ($post == null) return $modx->error->failure('Post not found!');

$board = $post->getOne('Board');

if ($post->remove() == false) {
    return $modx->error->failure('An error occurred while trying to remove the post.');
}

$board->set('num_posts',$board->get('num_posts')-1);
$board->set('total_posts',$board->get('total_posts')-1);
$board->save();

return $modx->error->success();
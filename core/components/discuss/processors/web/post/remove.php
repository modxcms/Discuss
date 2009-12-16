<?php
/**
 * @package discuss
 */
$modx->lexicon->load('discuss:post');

if (empty($_POST['post'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$post = $modx->getObject('disPost',$_POST['post']);
if ($post == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));

$board = $post->getOne('Board');

if ($post->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_remove'));
}

$board->set('num_posts',$board->get('num_posts')-1);
$board->set('total_posts',$board->get('total_posts')-1);
$board->save();

return $modx->error->success();
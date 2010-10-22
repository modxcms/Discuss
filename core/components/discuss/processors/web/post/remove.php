<?php
/**
 * @package discuss
 */
$modx->lexicon->load('discuss:post');

if (empty($_POST['post'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$post = $modx->getObject('disPost',$_POST['post']);
if ($post == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));

$board = $post->getOne('Board');

/* fire pre-remove event */
$rs = $modx->invokeEvent('OnDiscussBeforePostRemove',array(
    'post' => &$post,
    'board' => &$board,
    'mode' => 'remove',
));
$canRemove = $discuss->getEventResult($rs);
if (!empty($canRemove)) {
    return $modx->error->failure($canSave);
}

if ($post->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_remove'));
}

$board->set('num_posts',$board->get('num_posts')-1);
$board->set('total_posts',$board->get('total_posts')-1);
$board->save();

$modx->invokeEvent('OnDiscussPostRemove',array(
    'post' => &$post,
    'board' => &$board,
    'mode' => 'remove',
));


return $modx->error->success();
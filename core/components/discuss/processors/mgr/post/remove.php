<?php
/**
 * Remove a post
 *
 * @package discuss
 * @subpackage processors
 */
/* get object */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$post = $modx->getObject('disPost',$scriptProperties['id']);
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

/* remove */
if ($post->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_remove'));
}

$board->set('num_posts',$board->get('num_posts')-1);
$board->set('total_posts',$board->get('total_posts')-1);
$board->save();

/* fire post-remove event */
$modx->invokeEvent('OnDiscussPostRemove',array(
    'post' => &$post,
    'board' => &$board,
    'mode' => 'remove',
));

return $modx->error->success('',$post);
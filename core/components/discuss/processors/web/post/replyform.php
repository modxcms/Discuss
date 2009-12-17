<?php
/**
 * @package discuss
 */
$modx->lexicon->load('discuss:post');

$post = $modx->getObject('disPost',$_REQUEST['post']);
if ($post == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));

$output = $discuss->getChunk('disPostReplyForm',array(
    'id' => $post->get('id'),
    'title' => $post->get('title'),
));


return $modx->error->success($output);
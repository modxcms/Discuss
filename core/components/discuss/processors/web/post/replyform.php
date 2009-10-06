<?php
/**
 * @package discuss
 */

$post = $modx->getObject('disPost',$_REQUEST['post']);
if ($post == null) return $modx->error->failure('Post not found.');

$output = $discuss->getChunk('disPostReplyForm',array(
    'id' => $post->get('id'),
    'title' => $post->get('title'),
));


return $modx->error->success($output);
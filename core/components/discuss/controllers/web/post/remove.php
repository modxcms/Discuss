<?php
/**
 * Remove Post page
 *
 * @package discuss
 */
/* get thread root */
$post = $modx->getObject('disPost',$scriptProperties['post']);
if ($post == null) $modx->sendErrorPage();

$isModerator = $modx->getCount('disModerator',array(
    'user' => $discuss->user->get('id'),
    'board' => $post->get('board'),
)) > 0 ? true : false;

$canRemovePost = $discuss->user->get('id') == $post->get('author') || $isModerator;
if (!$canRemovePost) {
    $modx->sendErrorPage();
}

$thread = $post->getOne('Thread');

if (!$post->remove()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not remove post: '.print_r($post->toArray(),true));
}

if ($thread->get('post_first') == $post->get('id')) {
    $redirectTo = $discuss->url.'board/?board='.$post->get('board');
} else {
    $redirectTo = $discuss->url.'thread/?thread='.$thread->get('id');
}
$modx->sendRedirect($redirectTo);
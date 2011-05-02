<?php
/**
 * Remove Post page
 *
 * @package discuss
 */
/* get thread root */
$post = $modx->getObject('disPost',$scriptProperties['post']);
if ($post == null) $modx->sendErrorPage();
$discuss->setPageTitle($modx->lexicon('discuss.post_remove_header',array('title' => $post->get('title'))));
$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread')));
if (empty($thread)) { $modx->sendErrorPage(); }

$isModerator = $thread->isModerator($discuss->user->get('id'));
$canRemovePost = $discuss->user->get('id') == $post->get('author') || $isModerator || $discuss->user->isAdmin();
if (!$canRemovePost) {
    $modx->sendErrorPage();
}


if (!$post->remove(array(),true)) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not remove post: '.print_r($post->toArray(),true));
}

if ($thread->get('post_first') == $post->get('id')) {
    $redirectTo = $discuss->url.'board/?board='.$post->get('board');
} else {
    $redirectTo = $discuss->url.'thread/?thread='.$thread->get('id');
}
$modx->sendRedirect($redirectTo);
<?php
/**
 * Remove Thread page
 * 
 * @package discuss
 */
/* get thread root */
$post = $modx->getObject('disPost',$scriptProperties['post']);
if (empty($post)) $modx->sendErrorPage();

$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread'),disThread::TYPE_MESSAGE));
if (empty($thread)) $modx->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.remove_message_header',array('title' => $thread->get('title'))));

if ($post->remove()) {
    $posts = $thread->getMany('Posts');
    if (count($posts) <= 0) {
        $url = $discuss->url.'messages';
    } else {
        $url = $discuss->url.'messages/view?thread='.$thread->get('id');
    }
    $modx->sendRedirect($url);
}

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

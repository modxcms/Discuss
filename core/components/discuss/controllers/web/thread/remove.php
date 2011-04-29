<?php
/**
 * Remove Thread page
 * 
 * @package discuss
 */
/* get thread root */
$thread = $modx->call('disThread', 'fetch', array(&$modx,$scriptProperties['thread']));
if (empty($thread)) $modx->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.remove_thread_header',array('title' => $thread->get('title'))));

/* get breadcrumb trail */
$thread->buildBreadcrumbs();
$placeholders = $thread->toArray();

/* process form */
if (!empty($scriptProperties['remove-thread'])) {
    if ($thread->remove()) {
        $url = $discuss->url.'board?board='.$thread->get('board');
        $modx->sendRedirect($url);
    }
}

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

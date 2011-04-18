<?php
/**
 * Remove Thread page
 * 
 * @package discuss
 */
/* get thread root */
$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->select($modx->getSelectColumns('disThread','disThread'));
$c->select(array(
    'FirstPost.title',
    '(SELECT GROUP_CONCAT(pAuthor.id)
        FROM '.$modx->getTableName('disPost').' AS pPost
        INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
        WHERE pPost.thread = disThread.id
     ) AS participants',
));
$c->where(array('id' => $scriptProperties['thread']));
$thread = $modx->getObject('disThread',$c);
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

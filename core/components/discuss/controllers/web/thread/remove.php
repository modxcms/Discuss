<?php
/**
 * Remove Thread page
 * 
 * @package discuss
 */
/* get thread root */
$thread = $modx->getObject('disPost',$_REQUEST['thread']);
if ($thread == null) $modx->sendErrorPage();
$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'));

/* get breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $thread->get('board'),
));
$c->sortby('Ancestors.depth','ASC');
$ancestors = $modx->getCollection('disBoard',$c);
$trail = array();
$trail[] = array(
    'url' => $currentResourceUrl,
    'text' => $modx->getOption('discuss.forum_title'),
);
foreach ($ancestors as $ancestor) {
    $trail[] = array(
        'url' => $currentResourceUrl.'board?board='.$ancestor->get('id'),
        'text' => $ancestor->get('name'),
    );
}
$trail[] = array( 
    'url' => $currentResourceUrl.'thread?thread='.$thread->get('id'),
    'text' => $thread->get('title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.thread_remove'),'active' => true);
$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$thread->set('trail',$trail);

/* process form */
if (!empty($_POST)) {
    $url = $currentResourceUrl.'board?board='.$thread->get('board');
    $thread->remove();
    $modx->sendRedirect($url);
}
$placeholders = $thread->toArray();

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

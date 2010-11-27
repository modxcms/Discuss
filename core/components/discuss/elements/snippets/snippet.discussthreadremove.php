<?php
/**
 * Remove Thread page
 * 
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));

/* get thread root */
$thread = $modx->getObject('disPost',$_REQUEST['thread']);
if ($thread == null) $modx->sendErrorPage();

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
    'url' => $modx->makeUrl($modx->getOption('discuss.board_list_resource')),
    'text' => $modx->getOption('discuss.forum_title'),
);
foreach ($ancestors as $ancestor) {
    $trail[] = array(
        'url' => $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$ancestor->get('id')),
        'text' => $ancestor->get('name'),
    );
}
$trail[] = array( 
    'url' => $modx->makeUrl($modx->getOption('discuss.thread_resource',null,1),'',array('thread' => $thread->get('id'))),
    'text' => $thread->get('title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.thread_remove'),'active' => true);
$trail = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$thread->set('trail',$trail);

/* process form */
if (!empty($_POST)) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_resource')).'?board='.$thread->get('board');
    $thread->remove();
    $modx->sendRedirect($url);
}
$placeholders = $thread->toArray();

/* output */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.thread.js');
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $discuss->output('thread/remove',$placeholders);

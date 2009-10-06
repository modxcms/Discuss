<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.thread.js');

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
$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">'
    .'[[++discuss.forum_title]]'
    .'</a> / ';
foreach ($ancestors as $ancestor) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$ancestor->get('id'));
    $trail .= '<a href="'.$url.'">'.$ancestor->get('name').'</a>';
    $trail .= ' / ';
}
$trail .= '<a href="[[~[[++discuss.thread_resource]]]]?thread='.$thread->get('id').'">'.$thread->get('title').'</a>';
$trail .= ' / Remove Thread';
$thread->set('trail',$trail);

/* process form */
if (!empty($_POST)) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_resource')).'?board='.$thread->get('board');
    $thread->remove();
    $modx->sendRedirect($url);
}




/* output */
$properties = $thread->toArray();
$output = $discuss->getChunk('disThreadRemove',$properties);

$modx->setPlaceholder('discuss.thread',$thread->get('title'));

return $discuss->output($output);


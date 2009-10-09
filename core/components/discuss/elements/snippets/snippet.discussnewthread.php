<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('newthread:'.$_REQUEST['board']);

if (empty($_REQUEST['board'])) { $modx->sendErrorPage(); }
$board = $modx->getObject('disBoard',$_REQUEST['board']);
if ($board == null) { $modx->sendErrorPage(); }

/* setup defaults */
$properties = array(
    'board' => $board->get('id'),
);

/* get board breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $board->get('id'),
));
$c->sortby('Ancestors.depth','ASC');
$ancestors = $modx->getCollection('disBoard',$c);

$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">Home</a> / ';
foreach ($ancestors as $ancestor) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$ancestor->get('id'));
    $trail .= '<a href="'.$url.'">'.$ancestor->get('name').'</a>';
    $trail .= ' / ';
}
$trail .= 'New Thread';
$properties['trail'] = $trail;


/* if POST, process new thread request */
if (!empty($_POST)) {
    $modx->toPlaceholders($_POST,'post');
    include $discuss->config['processorsPath'].'web/post/create.php';
}

/* output form to browser */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.thread.new.js');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));

return $discuss->output('thread/new',$properties);
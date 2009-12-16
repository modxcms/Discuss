<?php
/**
 * Display form to post a new thread
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('newthread:'.$_REQUEST['board']);

if (empty($_REQUEST['board'])) { $modx->sendErrorPage(); }
$board = $modx->getObject('disBoard',$_REQUEST['board']);
if ($board == null) $modx->sendErrorPage();

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

$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">'.$modx->getOption('discuss.forum_title').'</a> / ';
foreach ($ancestors as $ancestor) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$ancestor->get('id'));
    $trail .= '<a href="'.$url.'">'.$ancestor->get('name').'</a>';
    $trail .= ' / ';
}
$trail .= $modx->lexicon('new_thread');
$properties['trail'] = $trail;


/* if POST, process new thread request */
if (!empty($_POST)) {
    $result = include $discuss->config['processorsPath'].'web/post/create.php';
    if ($discuss->processResult($result)) {
        $url = $modx->makeUrl($modx->getOption('discuss.board_resource')).'?board='.$board->get('id');
        $modx->sendRedirect($url);
    }
    $modx->toPlaceholders($_POST,'post');
    $modx->toPlaceholders($errors,'error');
}

/* set max attachment limit */
$properties['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
$(function() { DIS.config.attachments_max_per_post = '.$properties['max_attachments'].'; });
</script>');

/* output form to browser */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.thread.new.js');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));

return $discuss->output('thread/new',$properties);
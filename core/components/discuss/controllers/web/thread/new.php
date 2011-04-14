<?php
/**
 * Display form to post a new thread
 *
 * @package discuss
 */
$discuss->setSessionPlace('newthread:'.$scriptProperties['board']);

if (empty($scriptProperties['board'])) { $modx->sendErrorPage(); }
$board = $modx->getObject('disBoard',$scriptProperties['board']);
if ($board == null) $modx->sendErrorPage();

/* setup defaults */
$placeholders = array(
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

/* build breadcrumbs */
$placeholders['trail'] = $board->buildBreadcrumbs(array(
    array(
        'text' => $modx->lexicon('discuss.thread_new'),
        'active' => true,
    ),
),true);

/* if POST, process new thread request */
if (!empty($_POST)) {
    $result = include $discuss->config['processorsPath'].'web/post/create.php';
    if ($discuss->processResult($result)) {
        $url = $scriptProperties.'thread/?thread='.$post->get('id');
        $modx->sendRedirect($url);
    }
    $modx->toPlaceholders($_POST,'post');
    $modx->toPlaceholders($errors,'error');
}

/* set max attachment limit */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);

/* load theme options */
$discuss->config['max_attachments'] = $placeholders['max_attachments'];

/* output form to browser */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('Error'));

return $placeholders;
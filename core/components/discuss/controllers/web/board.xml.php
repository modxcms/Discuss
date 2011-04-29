<?php
/**
 * Displays posts for a Board in RSS format
 *
 * @package discuss
 */
/* get board */
if (empty($scriptProperties['board'])) $modx->sendErrorPage();
$board = $modx->call('disBoard','fetch',array(&$modx,$scriptProperties['board']));
if ($board == null) $modx->sendErrorPage();

/* set meta */
$discuss->setPageTitle($board->get('name'));

/* add user to board readers */
if (!empty($scriptProperties['read']) && $discuss->isLoggedIn) {
    $board->read($discuss->user->get('id'));
}

$placeholders = $board->toArray();

/* setup default properties */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.threads_per_page',null,20));
$start = $modx->getOption('start',$scriptProperties,0);
$param = $modx->getOption('discuss.page_param',$scriptProperties,'page');

/* get all threads in board */
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;
$posts = $discuss->hooks->load('board/post/getList',array(
    'board' => &$board,
    'limit' => $limit,
    'start' => $start,
    'tpl' => 'post/disBoardPostXml',
    'mode' => 'rss',
    'get_category_name' => true,
));
$placeholders['posts'] = implode("\n",$posts['results']);

return $placeholders;
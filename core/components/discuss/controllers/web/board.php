<?php
/**
 * Displays the Board
 *
 * @package discuss
 */
$discuss->setSessionPlace('board:'.$scriptProperties['board']);

/* get board */
$board = $modx->getObject('disBoard',$scriptProperties['board']);
if ($board == null) $modx->sendErrorPage();

$placeholders = $board->toArray();

/* setup default properties */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.threads_per_page',null,20));
$start = $modx->getOption('start',$scriptProperties,0);
$param = $modx->getOption('discuss.page_param',$scriptProperties,'page');

$category = array();
$category['category_name'] = $modx->lexicon('discuss.subboards');

$category['list'] = array();

$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'));

/* grab all subboards */
$placeholders['boards'] = $modx->hooks->load('board/getList',array(
    'board' => &$board,
));

/* get all threads in board */
$posts = $modx->hooks->load('board/post/getList',array(
    'board' => &$board,
));

/* load theme options */
$discuss->config['pa'] = $posts;

/* parse threads */
$postsOutput = array();
if (count($posts) > 0) {
    foreach ($posts as $postArray) {
        $postsOutput[] = $discuss->getChunk('disBoardPost',$postArray);
    }
    $placeholders['posts'] = implode("\n",$postsOutput);
}
unset($postsOutput,$pa,$post);

/* get board breadcrumb trail */
$placeholders['trail'] = $board->buildBreadcrumbs();

/* start placeholders */
if (empty($placeholders['boards'])) $placeholders['boards_toggle'] = 'display:none;';
unset($trail,$ancestors,$c);

/* get viewing users */
$placeholders['readers'] = $board->getViewing();

/* get pagination */
$count = count($posts);
$modx->hooks->load('pagination/build',array(
    'count' => $count,
    'id' => $board->get('id'),
    'view' => 'board',
    'limit' => $limit,
    'param' => $param,
));

unset($count,$start,$limit,$url);

/* action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) {
    $actionButtons[] = array('url' => $currentResourceUrl.'thread/new?board='.$board->get('id'), 'text' => $modx->lexicon('discuss.thread_new'));
    $actionButtons[] = array('url' => $currentResourceUrl.'board?board='.$board->get('id').'&read=1', 'text' => $modx->lexicon('discuss.mark_read'));
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.notify'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$modx->setPlaceholder('discuss.board',$board->get('name'));

return $placeholders;
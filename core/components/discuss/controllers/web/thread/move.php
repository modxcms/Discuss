<?php
/**
 * Remove Thread page
 * 
 * @package discuss
 */
/* get thread root */
$thread = $modx->call('disThread', 'fetch', array(&$modx,$scriptProperties['thread']));
if (empty($thread)) $modx->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.move_thread_header',array('title' => $thread->get('title'))));

/* get breadcrumb trail */
$thread->buildBreadcrumbs();
$placeholders = $thread->toArray();

/* process form */
if (!empty($scriptProperties['move-thread']) && !empty($scriptProperties['board'])) {
    if ($thread->move($scriptProperties['board'])) {
        $url = $discuss->url.'board?board='.$thread->get('board');
        $modx->sendRedirect($url);
    }
}

/* board dropdown list */
$boards = $modx->call('disBoard','fetchList',array(&$modx));
$placeholders['boards'] = array();
foreach ($boards as $board) {
    $board['selected'] = !empty($scriptProperties['board']) && $scriptProperties['board'] == $board['id'] ? ' selected="selected"' : '';
    $board['name'] = str_repeat('--',$board['depth']-1).$board['name'];
    $placeholders['boards'][] = $discuss->getChunk('board/disBoardOpt',$board);
}
$placeholders['boards'] = implode("\n",$placeholders['boards']);

unset($boards,$board,$list,$c);

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

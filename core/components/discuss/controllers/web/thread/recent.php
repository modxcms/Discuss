<?php
/**
 * Show Recent Posts
 * 
 * @package discuss
 */
$discuss->setSessionPlace('home');
$discuss->setPageTitle($modx->lexicon('discuss.recent_posts'));

/* get default options */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));
$start = $modx->getOption('start',$scriptProperties,0);
$page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;

/* recent posts */
$recent = $discuss->hooks->load('post/recent',array(
    'limit' => $limit,
    'start' => $start,
));
$placeholders['recent_posts'] = $recent['results'];

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.recent_posts').' ('.number_format($recent['total']).')','active' => true);

$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* build pagination */
$discuss->hooks->load('pagination/build',array(
    'count' => $recent['total'],
    'id' => 0,
    'view' => 'thread/recent',
    'limit' => $limit,
));

return $placeholders;
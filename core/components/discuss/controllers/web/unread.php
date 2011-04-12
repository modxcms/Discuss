<?php
/**
 * Get all unread posts by user
 * 
 * @package discuss
 */
$discuss->setSessionPlace('unread');
$placeholders = array();

/* setup default properties */
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$start = !empty($_REQUEST['start']) ? $_REQUEST['start'] : 0;

$cssRowCls = $modx->getOption('cssRowCls',$scriptProperties,'dis-board-li');
$rowTpl = $modx->getOption('rowTpl',$scriptProperties,'disPostLi');
$sortBy = $modx->getOption('sortBy',$scriptProperties,'createdon');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'ASC');

/* get unread posts */
$c = $modx->newQuery('disPost');
$c->select('
    `disPost`.*,
    `Board`.`name` AS `board_name`,
    `Author`.`username` AS `author_username`,
    `Thread`.`title` AS `thread_title`
');
$c->innerJoin('disPost','Thread');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->leftJoin('disPostRead','PostReads');
$c->where(array(
    'PostReads.post' => null,
));
$c->sortby($sortBy,$sortDir);
$c->groupby('thread');
$c->limit($limit,$start);
$unreadPosts = $modx->getCollection('disPost',$c);
$posts = array();
$threads = array();
foreach ($unreadPosts as $post) {
    $pa = $post->toArray();
    $pa['class'] = $cssRowCls;
    $pa['title'] = $post->get('thread_title');

    $posts[] = $discuss->getChunk($rowTpl,$pa);
}
$placeholders['posts'] = implode("\n",$posts);

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $modx->makeUrl($modx->getOption('discuss.board_list_resource')),
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.unread_posts'),'active' => true);

$trail = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

return $placeholders;
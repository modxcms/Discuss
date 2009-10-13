<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('unread');
$properties = array();

/* setup default properties */
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$start = !empty($_REQUEST['start']) ? $_REQUEST['start'] : 0;


/* get unread posts */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Board.name AS board_name,
    Author.username AS author_username,
    Thread.title AS thread_title
');
$c->innerJoin('disPost','Thread');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->leftJoin('disPostRead','PostReads');
$c->where(array(
    'PostReads.post IS NULL',
));
$c->sortby('createdon','DESC');
$c->groupby('thread');
$c->limit($limit,$start);
$unreadPosts = $modx->getCollection('disPost',$c);
$posts = array();
$threads = array();
foreach ($unreadPosts as $post) {
    $pa = $post->toArray();
    $pa['class'] = 'dis-board-li';
    $pa['title'] = $post->get('thread_title');

    $tpl = $discuss->getChunk('disPostLI',$pa);
    $rps[] = $discuss->getChunk('disPostLI',$pa);
}
$properties['posts'] = implode("\n",$rps);

/* get board breadcrumb trail */
$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">[[++discuss.forum_title]]</a> / ';
$trail .= 'Unread Posts';
$properties['trail'] = $trail;

return $discuss->output('unread',$properties);
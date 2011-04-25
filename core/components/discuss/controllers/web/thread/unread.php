<?php
/**
 * Get all unread posts by user
 * 
 * @package discuss
 */
$discuss->setSessionPlace('unread');
$discuss->setPageTitle($modx->lexicon('discuss.unread_posts'));
$placeholders = array();

/* setup default properties */
$limit = !empty($scriptProperties['limit']) ? $scriptProperties['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$page = !empty($scriptProperties['page']) ? $scriptProperties['page'] : 1;
$page = $page <= 0 ? $page = 1 : $page;
$start = ($page-1) * $limit;

$sortBy = $modx->getOption('sortBy',$scriptProperties,'LastPost.createdon');
$sortDir = $modx->getOption('sortDir',$scriptProperties,'DESC');

/* handle marking all as read */
if (!empty($scriptProperties['read']) && $discuss->isLoggedIn) {
    /* TODO: Write code to mark every thread as read */

    $discuss->hooks->load('thread/read_all',array(
        'board' => &$board,
        'limit' => $limit,
        'start' => $start,
    ));
}


/* get unread threads */
$c = $modx->newQuery('disThread');
$c->innerJoin('disBoard','Board');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disUser','FirstAuthor');
$c->leftJoin('disThreadRead','Reads');
$c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
$groups = $discuss->user->getUserGroups();
if (!empty($groups) && !$discuss->user->isAdmin) {
    /* restrict boards by user group if applicable */
    $g = array(
        'UserGroups.usergroup:IN' => $groups,
    );
    $g['OR:UserGroups.usergroup:='] = null;
    $where[] = $g;
    $c->andCondition($where,null,2);
}
$c->where(array(
    'Reads.thread' => null,
));
if ($discuss->isLoggedIn) {
    $ignoreBoards = $discuss->user->get('ignore_boards');
    if (!empty($ignoreBoards)) {
        $c->where(array(
            'Board.id:NOT IN' => explode(',',$ignoreBoards),
        ));
    }
}
$total = $modx->getCount('disThread',$c);
$c->select($modx->getSelectColumns('disThread','disThread'));
$c->select(array(
    'board_name' => 'Board.name',

    'title' => 'FirstPost.title',
    'thread' => 'FirstPost.thread',
    'author_username' => 'FirstAuthor.username',

    'post_id' => 'LastPost.id',
    'createdon' => 'LastPost.createdon',
    'author' => 'LastPost.author',
));
$c->sortby($sortBy,$sortDir);
$c->limit($limit,$start);
$threads = $modx->getCollection('disThread',$c);
$posts = array();

$canViewProfiles = $modx->hasPermission('discuss.view_profiles');
$hotThreadThreshold = $modx->getOption('discuss.hot_thread_threshold',null,10);
$enableSticky = $modx->getOption('discuss.enable_sticky',null,true);
$enableHot = $modx->getOption('discuss.enable_hot',null,true);
$list = array();
foreach ($threads as $thread) {
    $threadArray = $thread->toArray();
    $threadArray['class'] = 'dis-board-li';
    $threadArray['createdon'] = strftime($discuss->dateFormat,strtotime($threadArray['createdon']));
    $threadArray['icons'] = '';
    
    /* set css class */
    $class = array('board-post');
    if ($enableHot) {
        $threshold = $hotThreadThreshold;
        if ($discuss->user->get('id') == $threadArray['author'] && $discuss->isLoggedIn) {
            $class[] = $threadArray['replies'] < $threshold ? 'dis-my-normal-thread' : 'dis-my-veryhot-thread';
        } else {
            $class[] = $threadArray['replies'] < $threshold ? '' : 'dis-veryhot-thread';
        }
    }
    $threadArray['class'] = implode(' ',$class);

    /* if sticky/locked */
    $icons = array();
    if ($threadArray['locked']) { $icons[] = '<div class="dis-thread-locked"></div>'; }
    if ($enableSticky && $threadArray['sticky']) {
        $icons[] = '<div class="dis-thread-sticky"></div>';
    }
    $threadArray['icons'] = implode("\n",$icons);

    $threadArray['views'] = number_format($threadArray['views']);
    $threadArray['replies'] = number_format($threadArray['replies']);

    /* unread class */
    $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
    $threadArray['author_link'] = $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->url.'user/?user='.$threadArray['author'].'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];

    $list[] = $discuss->getChunk('post/disPostLi',$threadArray);
}
$placeholders['threads'] = implode("\n",$list);

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.unread_posts').' ('.number_format($total).')','active' => true);

$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* action buttons */
$actionButtons = array();
if ($discuss->isLoggedIn) {
    $actionButtons[] = array('url' => $discuss->url.'thread/unread?read=1', 'text' => $modx->lexicon('discuss.mark_all_as_read'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* build pagination */
$discuss->hooks->load('pagination/build',array(
    'count' => $total,
    'id' => 0,
    'view' => 'thread/unread',
    'limit' => $limit,
));

return $placeholders;
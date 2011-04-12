<?php
/**
 * Displays the Board
 *
 * @package discuss
 */
$discuss->setSessionPlace('board:'.$_REQUEST['board']);

/* get board */
$board = $modx->getObject('disBoard',$scriptProperties['board']);
if ($board == null) $modx->sendErrorPage();

/* setup default properties */
$limit = $modx->getOption('limit',$_REQUEST,$modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.threads_per_page',null,20)));
$start = $modx->getOption('start',$_REQUEST,$modx->getOption('start',$scriptProperties,0));
$param = $modx->getOption('discuss.page_param',$scriptProperties,'page');

/* get default chunk properties */
$boardRowTpl = $modx->getOption('boardRowTpl',$scriptProperties,'board/disBoardLi');
$categoryRowTpl = $modx->getOption('categoryRowTpl',$scriptProperties,'category/disCategoryLi');
$lastPostByTpl = $modx->getOption('lastPostByTpl',$scriptProperties,'disLastPostBy');
$threadTpl = $modx->getOption('threadTpl',$scriptProperties,'disBoardPost');

/* get default css classes properties */
$cssLockedThreadCls = $modx->getOption('cssLockedThread',$scriptProperties,'dis-thread-locked');
$cssStickyThreadCls = $modx->getOption('cssStickyThread',$scriptProperties,'dis-thread-sticky');
$cssUnreadRowCls = $modx->getOption('cssUnreadRow',$scriptProperties,'dis-unread');
$cssBoardCls = $modx->getOption('cssBoard',$scriptProperties,'board-post');

/* grab all subboards */
$subboards = $modx->hooks->load('board/getList',array(
    'board' => &$board,
));

$category = array();
$category['category_name'] = $modx->lexicon('discuss.subboards');

$category['list'] = array();
foreach ($subboards as $subboard) {
    $subboard->getSubBoardList();

    if ($subboard->get('unread') > 0 && $modx->user->isAuthenticated()) {
        $subboard->set('unread-cls',$cssUnreadRowCls);
    }

    if ($subboard->get('last_post_author')) {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($subboard->get('last_post_createdon'))),
            'user' => $subboard->get('last_post_author'),
            'username' => $subboard->get('last_post_username'),
        );
        $lp = $discuss->getChunk($lastPostByTpl,$phs);
        $subboard->set('lastPost',$lp);
    }

    $ba = $subboard->toArray('',true);
    $category['list'][] = $discuss->getChunk($boardRowTpl,$ba);
}
if (!empty($category['list'])) {
    $category['list'] = implode("\n",$category['list']);
    $subboardOutput = $discuss->getChunk($categoryRowTpl,$category);
}
unset($ba,$subboard,$category);

/* get all threads in board */
$posts = $modx->hooks->load('board/post/getList',array(
    'board' => &$board,
));
/* iterate through threads */
$userUrl = $modx->makeUrl($modx->getOption('discuss.user_resource'));
$pa = array();
foreach ($posts as $post) {
    /* get latest post in thread
     * TODO: eventually move this to post/getList hook, where it can be one query
     * rather than in this foreach loop
     */
    $c = $modx->newQuery('disPost');
    $c->select($modx->getSelectColumns('disPost','disPost','',array('id','title','createdon','author')));
    $c->select($modx->getSelectColumns('modUser','Author','',array('username')));
    $c->innerJoin('disPostClosure','Descendants');
    $c->innerJoin('modUser','Author');
    $c->where(array(
        'Descendants.ancestor' => $post->get('id'),
    ));
    $c->sortby($modx->getSelectColumns('disPost','disPost','',array('createdon')),'DESC');
    $latestPost = $modx->getObject('disPost',$c);
    if ($latestPost != null) {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($latestPost->get('createdon'))),
            'user' => $latestPost->get('author'),
            'username' => $latestPost->get('username'),
        );
        $latestText = $discuss->getChunk($lastPostByTpl,$phs);

        $createdon = strftime($modx->getOption('discuss.date_format'),strtotime($latestPost->get('createdon')));
        $post->set('latest',$latestText);
        $post->set('latest.id',$latestPost->get('id'));
    } else {
        $post->set('latest',$modx->lexicon('discuss.no_replies_yet'));
    }

    /* set css class */
    $class = $cssBoardCls;
    if ($modx->getOption('discuss.enable_hot',null,true)) {
        $threshold = $modx->getOption('discuss.hot_thread_threshold',null,10);
        if ($modx->user->get('id') == $post->get('author') && $modx->user->isAuthenticated()) {
            $class .= $post->get('replies') < $threshold ? ' dis-my-normal-thread' : ' dis-my-veryhot-thread';
        } else {
            $class .= $post->get('replies') < $threshold ? '' : ' dis-veryhot-thread';
        }
    }
    $post->set('class',$class);

    /* if sticky/locked */
    $icons = '';
    if ($post->get('locked')) { $icons .= '<div class="'.$cssLockedThreadCls.'"></div>'; }
    if ($modx->getOption('discuss.enable_sticky',null,true) && $post->get('sticky')) {
        $icons .= '<div class="'.$cssStickyThreadCls.'"></div>';
    }
    $post->set('icons',$icons);

    /* unread class */
    $unread = '';
    if ($post->get('unread') > 0 && $modx->user->isAuthenticated()) {
        $unread = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
    }
    $post->set('unread',$unread);

    $pa[] = $post->toArray();
}
unset($unread,$class,$threshold,$latestText,$createdon,$c);

/* load theme options */
$discuss->config['pa'] = $pa;

/* parse threads */
$postsOutput = '';
if (count($pa) > 0) {
    foreach ($pa as $postArray) {
        $postsOutput .= $discuss->getChunk($threadTpl,$postArray);
    }
    $board->set('posts',$postsOutput);
}
unset($postsOutput,$pa,$posts,$post);

/* get board breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $board->get('id'),
    'Ancestors.ancestor:!=' => $board->get('id'),
));
$c->sortby($modx->getSelectColumns('disBoardClosure','Ancestors','',array('depth')),'DESC');
$ancestors = $modx->getCollection('disBoard',$c);

/* breadcrumbs */
$trail = array();
$trail[] = array(
    'url' => $modx->makeUrl($modx->getOption('discuss.board_list_resource')),
    'text' => $modx->getOption('discuss.forum_title'),
);
foreach ($ancestors as $ancestor) {
    $trail[] = array(
        'url' => $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$ancestor->get('id')),
        'text' => $ancestor->get('name'),
    );
}
$trail[] = array('text' => $board->get('name'), 'active' => true);
$trail = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$board->set('trail',$trail);

/* start placeholders */
$placeholders = $board->toArray();
$placeholders['subboards'] = $subboardOutput;
if (empty($subboardOutput)) $placeholders['subboards_toggle'] = 'display:none;';
unset($subboardOutput,$trail,$ancestors,$c);

/* get viewing users */
$placeholders['readers'] = $board->getViewing();

/* get pagination */
$count = count($pa);
$modx->hooks->load('pagination/build',array(
    'total' => $count,
    'id' => $board->get('id'),
    'view' => 'board',
    'limit' => $limit,
    'param' => $param,
));

unset($count,$start,$limit,$url);

/* action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) {
    $actionButtons[] = array('url' => '[[~[[*id]]? &action=`thread/new` &board=`'.$board->get('id').'`]]', 'text' => $modx->lexicon('discuss.thread_new'));
    $actionButtons[] = array('url' => '[[~[[*id]]]]board?board='.$board->get('id').'&read=1', 'text' => $modx->lexicon('discuss.mark_read'));
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.notify'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$modx->setPlaceholder('discuss.board',$board->get('name'));

return $placeholders;
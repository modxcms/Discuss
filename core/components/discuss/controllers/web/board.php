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

/* setup default properties */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.threads_per_page',null,20));
$start = $modx->getOption('start',$scriptProperties,0);
$param = $modx->getOption('discuss.page_param',$scriptProperties,'page');

$category = array();
$category['category_name'] = $modx->lexicon('discuss.subboards');

$category['list'] = array();

$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'));

/* grab all subboards */
$cacheKey = 'discuss/board/'.$board->get('id').'/subboards';
$subBoardsData = $modx->cacheManager->get($cacheKey);
if (empty($subBoardsData)) {
    $subBoards = $modx->hooks->load('board/getList',array(
        'board' => &$board,
    ));
    $subBoardsData = array();

    foreach ($subBoards as $subBoard) {
        $subBoard->getSubBoardList();

        if ($subBoard->get('unread') > 0 && $modx->user->isAuthenticated()) {
            $subBoard->set('unread-cls','dis-unread');
        }

        if ($subBoard->get('last_post_author')) {
            $phs = array(
                'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($subBoard->get('last_post_createdon'))),
                'user' => $subBoard->get('last_post_author'),
                'username' => $subBoard->get('last_post_username'),
            );
            $lp = $discuss->getChunk('disLastPostBy',$phs);
            $subBoard->set('lastPost',$lp);
        }

        $boardArray = $subBoard->toArray('',true);
        $subBoardsData[] = $boardArray;
    }
    $modx->cacheManager->set($cacheKey,$subBoardsData,3600);
}
$subBoardOutput = '';
if (!empty($subBoardsData)) {
    $subBoards = array();
    foreach ($subBoardsData as $subBoardData) {
        $subBoards[] = $discuss->getChunk('board/disBoardLi',$subBoardData);
    }
    $category['list'] = implode("\n",$subBoards);
    $subBoardOutput = $discuss->getChunk('category/disCategoryLi',$category);
}
unset($boardArray,$subBoard,$category);

/* get all threads in board */
$posts = $modx->hooks->load('board/post/getList',array(
    'board' => &$board,
));
/* iterate through threads */
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
        $latestText = $discuss->getChunk('disLastPostBy',$phs);

        $createdon = strftime($modx->getOption('discuss.date_format'),strtotime($latestPost->get('createdon')));
        $post->set('latest',$latestText);
        $post->set('latest.id',$latestPost->get('id'));
    } else {
        $post->set('latest',$modx->lexicon('discuss.no_replies_yet'));
    }

    /* set css class */
    $class = 'board-post';
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
    $icons = array();
    if ($post->get('locked')) { $icons[] = '<div class="dis-thread-locked"></div>'; }
    if ($modx->getOption('discuss.enable_sticky',null,true) && $post->get('sticky')) {
        $icons[] = '<div class="dis-thread-sticky"></div>';
    }
    $post->set('icons',implode("\n",$icons));

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
$postsOutput = array();
if (count($pa) > 0) {
    foreach ($pa as $postArray) {
        $postsOutput[] = $discuss->getChunk('disBoardPost',$postArray);
    }
    $board->set('posts',implode("\n",$postsOutput));
}
unset($postsOutput,$pa,$posts,$post);

/* get board breadcrumb trail */
$board->buildBreadcrumbs();

/* start placeholders */
$placeholders = $board->toArray();
$placeholders['subboards'] = $subBoardOutput;
if (empty($subBoardOutput)) $placeholders['subboards_toggle'] = 'display:none;';
unset($subBoardOutput,$trail,$ancestors,$c);

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
    $actionButtons[] = array('url' => $currentResourceUrl.'thread/new?board='.$board->get('id'), 'text' => $modx->lexicon('discuss.thread_new'));
    $actionButtons[] = array('url' => $currentResourceUrl.'board?board='.$board->get('id').'&read=1', 'text' => $modx->lexicon('discuss.mark_read'));
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.notify'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$modx->setPlaceholder('discuss.board',$board->get('name'));

return $placeholders;
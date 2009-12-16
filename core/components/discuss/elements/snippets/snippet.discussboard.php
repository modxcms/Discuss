<?php
/**
 * Displays the Board
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('board:'.$_REQUEST['board']);

/* get board */
$board = $modx->getObject('disBoard',$_REQUEST['board']);
if ($board == null) $modx->sendErrorPage();

/* setup default properties */
$limit = !empty($_REQUEST['limit']) ? $_REQUEST['limit'] : $modx->getOption('discuss.threads_per_page',null,20);
$start = !empty($_REQUEST['start']) ? $_REQUEST['start'] : 0;

$cssLockedThreadCls = $modx->getOption('cssLockedThreadCls',$scriptProperties,'dis-thread-locked');
$cssStickyThreadCls = $modx->getOption('cssStickyThreadCls',$scriptProperties,'dis-thread-sticky');
$cssUnreadRowCls = $modx->getOption('cssUnreadRowCls',$scriptProperties,'dis-unread');
$boardRowTpl = $modx->getOption('boardRowTpl',$scriptProperties,'disBoardLi');
$categoryRowTpl = $modx->getOption('categoryRowTpl',$scriptProperties,'disCategoryLi');
$lastPostByTpl = $modx->getOption('lastPostByTpl',$scriptProperties,'disLastPostBy');

/* grab all subboards */
$c = $modx->newQuery('disBoard');
$c->select('disBoard.*,
    (SELECT COUNT(*) FROM '.$modx->getTableName('disPost').' AS dp
        WHERE
            id NOT IN (
                SELECT post FROM '.$modx->getTableName('disPostRead').' AS dp2
                WHERE
                    user = '.$modx->user->get('id').'
                AND board = disBoard.id
            )
        AND board = disBoard.id
    ) AS unread,
    (SELECT GROUP_CONCAT(CONCAT_WS(":",subBoardClosureBoard.id,subBoardClosureBoard.name) SEPARATOR ",") AS name
        FROM '.$modx->getTableName('disBoard').' AS subBoard
            INNER JOIN '.$modx->getTableName('disBoardClosure').' AS subBoardClosure
            ON subBoardClosure.ancestor = subBoard.id
            INNER JOIN '.$modx->getTableName('disBoard').' AS subBoardClosureBoard
            ON subBoardClosureBoard.id = subBoardClosure.descendant
        WHERE
            subBoard.id = disBoard.id
        AND subBoardClosure.descendant != disBoard.id
        AND subBoardClosure.depth = 1
        GROUP BY subBoard.id
    ) AS subboards,
    LastPost.title AS last_post_title,
    LastPost.author AS last_post_author,
    LastPost.createdon AS last_post_createdon,
    LastPostAuthor.username AS last_post_username
');
$c->innerJoin('disCategory','Category');
$c->innerJoin('disBoardClosure','Descendants');
$c->leftJoin('disPost','LastPost');
$c->leftJoin('modUser','LastPostAuthor','LastPost.author = LastPostAuthor.id');
$c->where(array(
    'disBoard.parent' => $board->get('id'),
));
$c->sortby('Category.rank, disBoard.rank ASC','');
$subboards = $modx->getCollection('disBoard',$c);
unset($c);

$subboardOutput = '';
foreach ($subboards as $subboard) {
    $subboard->getSubBoardList();
    $subboard->set('category_name',$modx->lexicon('discuss.subboards'));

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

    if ($currentCategory != $subboard->get('category')) {
        $subboardOutput .= $discuss->getChunk($categoryRowTpl,$ba);
        $currentCategory = $subboard->get('category');
    }

    $subboardOutput .= $discuss->getChunk($boardRowTpl,$ba);
}
unset($currentCategory,$ba,$lp,$subboard);

/* get all threads in board */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Descendants.depth AS depth,
    Author.username AS username,
    AuthorProfile.fullname AS author_name,
    (SELECT COUNT(*) FROM '.$modx->getTableName('disPostClosure').'
     WHERE
        ancestor = disPost.id
    AND descendant != disPost.id) AS replies,
    (SELECT COUNT(*) FROM '.$modx->getTableName('disPost').' AS dp
        WHERE
            id NOT IN (
                SELECT post FROM '.$modx->getTableName('disPostRead').'
                WHERE
                    user = '.$modx->user->get('id').'
                AND board = disPost.board
            )
        AND id IN (
            SELECT descendant FROM '.$modx->getTableName('disPostClosure').'
            WHERE ancestor = disPost.id
        )
        AND board = disPost.board
    ) AS unread
');
$c->innerJoin('disPostClosure','Descendants');
$c->innerJoin('disPostClosure','Ancestors');
$c->innerJoin('modUser','Author');
$c->innerJoin('modUserProfile','AuthorProfile','Author.id = AuthorProfile.internalKey');
$c->where(array(
    'Descendants.ancestor' => 0,
    'Descendants.depth' => 0,
    'disPost.board' => $board->get('id'),
));
if ($modx->getOption('discuss.enable_sticky',null,true)) {
    $c->sortby('disPost.sticky','DESC');
}
$c->sortby('disPost.rank','DESC');
$c->limit($limit,$start);
$posts = $modx->getCollection('disPost',$c);

/* iterate through threads */
$userUrl = $modx->makeUrl($modx->getOption('discuss.user_resource'));
$pa = array();
foreach ($posts as $post) {
    /* get latest post in thread */
    $c = $modx->newQuery('disPost');
    $c->select('
        disPost.id,
        disPost.title,
        disPost.createdon,
        disPost.author,
        Author.username
    ');
    $c->innerJoin('disPostClosure','Descendants');
    $c->innerJoin('modUser','Author');
    $c->where(array(
        'Descendants.ancestor' => $post->get('id'),
    ));
    $c->sortby('disPost.createdon','DESC');
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

/* load thread count */
$modx->regClientStartupScript('<script type="text/javascript">
$(function() {
    DISBoard.threadCount = "'.count($pa).'";
});</script>');


/* parse threads with treeparser */
$discuss->loadTreeParser();
if (count($pa) > 0) {
    $postsOutput = $discuss->treeParser->parse($pa,'disBoardPost');
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
$c->sortby('Ancestors.depth','DESC');
$ancestors = $modx->getCollection('disBoard',$c);

$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">'
    .'[[++discuss.forum_title]]'
    .'</a> / ';
foreach ($ancestors as $ancestor) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_resource'),'','?board='.$ancestor->get('id'));
    $trail .= '<a href="'.$url.'">'.$ancestor->get('name').'</a>';
    $trail .= ' / ';
}
$trail .= $board->get('name');
$board->set('trail',$trail);

$properties = $board->toArray();
$properties['subboards'] = $subboardOutput;
if (empty($subboardOutput)) $properties['subboards_toggle'] = 'display:none;';
unset($subboardOutput,$trail,$ancestors,$c);

/* get viewing users */
$properties['readers'] = $board->getViewing();

/* get pagination */
$count = count($pa);
$url = $modx->makeUrl($modx->getOption('discuss.board_resource'));
$properties['pagination'] = $discuss->buildPagination($count,$limit,$start,$url);
unset($count,$start,$limit,$url);

/* action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) {
    $actionButtons[] = array('url' => '[[~[[++discuss.new_thread_resource]]]]?board=[[+id]]', 'text' => $modx->lexicon('discuss.thread_new'));
    $actionButtons[] = array('url' => '[[~[[++discuss.board_resource]]]]?board=[[+id]]&read=1', 'text' => $modx->lexicon('discuss.mark_read'));
    $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $modx->lexicon('discuss.notify'));
}
$properties['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($actionButtons);

/* output */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.board.js');
$modx->setPlaceholder('discuss.board',$board->get('name'));
return $discuss->output('board',$properties);


<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('home');

$modx->setLogTarget('ECHO');

$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.home.js');
$properties = array();

$_groups = implode(',',$modx->user->getUserGroups());
$c = $modx->newQuery('disBoard');
$c->select('disBoard.*,
    Category.name AS category_name,

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
            INNER JOIN '.$modx->getTableName('disBoardUserGroup').' AS subBoardUserGroups
            ON (subBoardUserGroups.usergroup IS NULL '.(empty($_groups) ? '' : '
                OR subBoardUserGroups.usergroup IN ('.$_groups.')
            ').')
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
$c->leftJoin('disBoardUserGroup','UserGroups');
$c->where(array(
    'disBoard.parent' => 0,
));
$c->orCondition(array(
    'UserGroups.usergroup IS NULL',
),null,1);

/* restrict boards by user group if applicable */
if (!empty($_groups)) {
    $c->orCondition(array(
        'UserGroups.usergroup IN ('.$_groups.')',
    ),null,1);
}

$c->sortby('Category.rank','ASC');
$c->sortby('disBoard.rank','ASC');
$boards = $modx->getCollection('disBoard',$c);
unset($c);

$boardOutput = '';
$currentCategory = 0;
foreach ($boards as $board) {
    $board->getSubBoardList();

    if ($board->get('unread') > 0 && $modx->user->isAuthenticated()) {
        $board->set('unread-cls','dis-unread');
    }

    if ($board->get('last_post_author')) {
        $lp = $modx->lexicon('discuss.last_post').' ';
        $lp .= strftime($modx->getOption('discuss.date_format'),strtotime($board->get('last_post_createdon')));
        $lp .= '<br />'.'by <a href="[[~[[++discuss.user_resource]]]]?user=';
        $lp .= $board->get('last_post_author').'">';
        $lp .= $board->get('last_post_username').'</a>';
        $board->set('lastPost',$lp);
    }



    $ba = $board->toArray('',true);

    if ($currentCategory != $board->get('category')) {
        $boardOutput .= $discuss->getChunk('disCategoryLI',$ba);
        $currentCategory = $board->get('category');
    }

    $boardOutput .= $discuss->getChunk('disBoardLI',$ba);
}
$properties['boards'] = $boardOutput;
unset($boardOutput,$currentCategory,$ba,$boards,$board);

/* recent posts */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Board.name AS board_name,
    Author.username AS author_username,
    (SELECT Post.id FROM '.$modx->getTableName('disPost').' AS Post
        INNER JOIN '.$modx->getTableName('disPostClosure').' AS Ancestors
        ON Ancestors.ancestor = Post.id
     WHERE
         Ancestors.descendant = disPost.id
     AND Ancestors.ancestor != disPost.id
     AND Post.parent = 0
    ) AS thread
');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->sortby('createdon','DESC');
$c->limit($modx->getOption('discuss.num_recent_posts',null,10));
$recentPosts = $modx->getCollection('disPost',$c);
$rps = array();
foreach ($recentPosts as $post) {
    $pa = $post->toArray('',true);
    $pa['class'] = 'dis-board-li';
    if (empty($pa['thread'])) { $pa['thread'] = $pa['id']; }

    $rps[] = $discuss->getChunk('disPostLI',$pa);
}
$properties['recentPosts'] = implode("\n",$rps);
unset($rps,$pa,$recentPosts,$post);

/* process logout */
if (isset($_REQUEST['logout']) && $_REQUEST['logout']) {
    $response = $modx->executeProcessor(array(
        'action' => 'logout',
        'location' => 'security'
    ));
    $url = $modx->makeUrl($modx->resource->get('id'));
    $modx->sendRedirect($url);
}

/* action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) { /* if logged in */
    $actionButtons[] = array('url' => '[[~[[++discuss.board_list_resource]]]]?read=1', 'text' => $modx->lexicon('discuss.mark_all_as_read'));

    $authLink = $modx->makeUrl($modx->getOption('discuss.board_list_resource')).'?logout=1';
    $authMsg = $modx->lexicon('discuss.logout');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');
    $actionButtons[] = array('url' => $authLink, 'text' => $authMsg);
} else { /* if logged out */
    $authLink = $modx->makeUrl($modx->getOption('discuss.login_resource'));
    $authMsg = $modx->lexicon('discuss.login');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');

    $modx->setPlaceholder('discuss.loginForm',$discuss->getChunk('disLogin'));
}
$properties['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($authLink,$authMsg,$actionButtons);

/* stats */
$properties['totalPosts'] = $modx->getCount('disPost');
$properties['totalTopics'] = $modx->getCount('disPost',array('parent' => 0));
$properties['totalMembers'] = $modx->getCount('disUserProfile');

/* active in last 40 */
if ($modx->getOption('discuss.show_whos_online',null,true)) {
    $threshold = $modx->getOption('discuss.user_active_threshold',null,40);
    $timeago = time() - (60*($threshold));
    $c = $modx->newQuery('modUser');
    $c->innerJoin('disSession','Session','Session.user = modUser.id');
    $c->where(array(
        'Session.access:>=' => $timeago,
    ));
    $activeUsers = $modx->getCollection('modUser',$c);
    $as = '';
    foreach ($activeUsers as $activeUser) {
        $as .= '<a href="[[~[[++discuss.user_resource]]]]?user='.$activeUser->get('id').'">'.$activeUser->get('username').'</a>,';
    }
    $properties['activeUsers'] = 'Users active in last '.$threshold.' minutes: '.trim($as,',');
    unset($as,$activeUsers,$activeUser,$timeago,$threshold);
}

/* total active */
$properties['totalMembersActive'] = $modx->getCount('disSession',array('user:!=' => 0));
$properties['totalVisitorsActive'] = $modx->getCount('disSession',array('user' => 0));

/* latest post */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.id,
    disPost.title,
    disPost.createdon,
    disPost.author,
    Author.username AS username,
    Board.name AS board,
    (SELECT Post.id FROM '.$modx->getTableName('disPost').' AS Post
        INNER JOIN '.$modx->getTableName('disPostClosure').' AS Ancestors
        ON Ancestors.ancestor = Post.id
     WHERE
         Ancestors.descendant = disPost.id
     AND Ancestors.ancestor != disPost.id
     AND Post.parent = 0
    ) AS thread
');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
$c->orCondition(array(
    'UserGroups.usergroup IS NULL',
),null,1);
if (!empty($_groups)) {
    $c->orCondition(array(
        'UserGroups.usergroup IN ('.$_groups.')',
    ),null,1);
}
$c->sortby('createdon','DESC');
$latestPost = $modx->getObject('disPost',$c);
$la = $latestPost->toArray('latestPost.',true);
$properties = array_merge($properties,$la);
unset($la,$latestPost,$c);

/* output */
$o = $discuss->getChunk('disHome',$properties);
return $discuss->output($o);


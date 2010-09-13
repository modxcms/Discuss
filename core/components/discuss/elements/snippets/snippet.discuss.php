<?php
/**
 * Main front page snippet
 * 
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('home');

/* get default properties */
$activeUserRowTpl = $modx->getOption('activeUserRowTpl',$scriptProperties,'disActiveUserRow');
$boardRowTpl = $modx->getOption('boardRowTpl',$scriptProperties,'disBoardLi');
$categoryRowTpl = $modx->getOption('categoryRowTpl',$scriptProperties,'disCategoryLi');
$cssBoardRowCls = $modx->getOption('cssBoardRowCls',$scriptProperties,'dis-board-li');
$cssUnreadCls = $modx->getOption('cssUnreadCls',$scriptProperties,'dis-unread');
$lastPostByTpl = $modx->getOption('lastPostByTpl',$scriptProperties,'disLastPostBy');
$postRowTpl = $modx->getOption('postRowTpl',$scriptProperties,'disPostLi');

$_groups = $modx->user->getUserGroups();

/* begin query build */

/* unread posts subquery */
$unreadSubCriteria = $modx->newQuery('disPostRead');
$unreadSubCriteria->setClassAlias('disPostRead');
$unreadSubCriteria->select($modx->getSelectColumns('disPostRead','disPostRead','',array('post')));
$unreadSubCriteria->where(array(
    'disPostRead.user' => $modx->user->get('id'),
    $modx->getSelectColumns('disPostRead','disPostRead','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
));
$unreadSubCriteria->prepare();
$unreadSubSql = $unreadSubCriteria->toSql();

$unreadCriteria = $modx->newQuery('disPost');
$unreadCriteria->setClassAlias('UnreadPosts');
$unreadCriteria->select('COUNT(`UnreadPosts`.`id`)');
$unreadCriteria->where(array(
    $modx->getSelectColumns('disPost','UnreadPosts','',array('id')).' NOT IN ('.$unreadSubSql.')',
    $modx->getSelectColumns('disPost','UnreadPosts','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
));
$unreadCriteria->prepare();
$unreadSql = $unreadCriteria->toSql();

/* subboards subquery */
$sbCriteria = $modx->newQuery('disBoard');
$sbCriteria->setClassAlias('subBoard');
$sbCriteria->select(array(
    'GROUP_CONCAT(CONCAT_WS(":",'.$modx->getSelectColumns('disBoard','subBoardClosureBoard','',array('id','name')).') SEPARATOR ",") AS `name`',
));
$sbCriteria->innerJoin('disBoardClosure','subBoardClosure','`subBoardClosure`.`ancestor` = `subBoard`.`id`');
$sbCriteria->innerJoin('disBoard','subBoardClosureBoard','`subBoardClosureBoard`.`id` = `subBoardClosure`.`descendant`');
$sbCriteria->innerJoin('disBoardUserGroup','subBoardUserGroups','(`subBoardUserGroups`.`usergroup` IS NULL '.(empty($_groups) ? '' : '
    OR `subBoardUserGroups`.`usergroup` IN ('.implode(',',$_groups).')
').')');
$sbCriteria->where(array(
    '`subBoard`.`id` = `disBoard`.`id`',
    '`subBoardClosure`.`descendant` != `disBoard`.`id`',
    'subBoardClosure.depth' => 1,
));
$sbCriteria->groupby('`subBoard`.`id`');
$sbCriteria->prepare();
$sbSql = $sbCriteria->toSql();

/* create main query */
$c = $modx->newQuery('disBoard');
$c->select(array(
    'disBoard.*',
));
$c->select('
    `Category`.`name` AS `category_name`,
    ('.$unreadSql.') AS `unread`,
    ('.$sbSql.') AS `subboards`,
    `LastPost`.`title` AS `last_post_title`,
    `LastPost`.`author` AS `last_post_author`,
    `LastPost`.`createdon` AS `last_post_createdon`,
    `LastPostAuthor`.`username` AS `last_post_username`
');
$c->innerJoin('disCategory','Category');
$c->innerJoin('disBoardClosure','Descendants');
$c->leftJoin('disPost','LastPost');
$c->leftJoin('modUser','LastPostAuthor','`LastPost`.`author` = `LastPostAuthor`.`id`');
$c->leftJoin('disBoardUserGroup','UserGroups');
$where = array(
    'disBoard.parent' => 0,
);
/* restrict boards by user group if applicable */
$g = array();
if (!empty($_groups)) {
    $g = array(
        'UserGroups.usergroup' => $_groups,
    );
}
$g['OR:UserGroups.usergroup:='] = null;
$where[] = $g;
$c->where($where);

$c->sortby('`Category`.`rank`','ASC');
$c->sortby('`disBoard`.`rank`','ASC');
$boards = $modx->getCollection('disBoard',$c);
unset($c);

/* now loop through boards */
$placeholders = array();
$placeholders['boards'] = '';
$currentCategory = 0;
foreach ($boards as $board) {
    $board->getSubBoardList();

    if ($board->get('unread') > 0 && $modx->user->isAuthenticated()) {
        $board->set('unread-cls',$cssUnreadCls);
    }

    if ($board->get('last_post_author')) {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($board->get('last_post_createdon'))),
            'user' => $board->get('last_post_author'),
            'username' => $board->get('last_post_username'),
        );
        $lp = $discuss->getChunk($lastPostByTpl,$phs);
        $board->set('lastPost',$lp);
    }

    $ba = $board->toArray('',true);

    if ($currentCategory != $board->get('category')) {
        $placeholders['boards'] .= $discuss->getChunk($categoryRowTpl,$ba);
        $currentCategory = $board->get('category');
    }

    $placeholders['boards'] .= $discuss->getChunk($boardRowTpl,$ba);
}
unset($currentCategory,$ba,$boards,$board,$lp);

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
    $actionButtons[] = array('url' => '[[~[[++discuss.board_list_resource]]? &read=`1`]]', 'text' => $modx->lexicon('discuss.mark_all_as_read'));

    $authLink = $modx->makeUrl($modx->getOption('discuss.board_list_resource'),'','logout=1');
    $authMsg = $modx->lexicon('discuss.logout');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');
    $actionButtons[] = array('url' => $authLink, 'text' => $authMsg);
} else { /* if logged out */
    $authLink = $modx->makeUrl($modx->getOption('discuss.login_resource'));
    $authMsg = $modx->lexicon('discuss.login');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');

    $modx->setPlaceholder('discuss.loginForm',$discuss->getChunk('disLogin'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($authLink,$authMsg,$actionButtons);

/* stats */
$placeholders['totalPosts'] = $modx->getCount('disPost');
$placeholders['totalTopics'] = $modx->getCount('disPost',array('parent' => 0));
$placeholders['totalMembers'] = $modx->getCount('disUserProfile');

/* active in last 40 */
if ($modx->getOption('discuss.show_whos_online',null,true)) {
    $threshold = $modx->getOption('discuss.user_active_threshold',null,40);
    $timeago = time() - (60*($threshold));
    $c = $modx->newQuery('modUser');
    $c->select(array(
        'modUser.*',
        'UserGroupProfile.color',
    ));
    $c->innerJoin('disSession','Session',$modx->getSelectColumns('disSession','Session','',array('user')).' = '.$modx->getSelectColumns('modUser','modUser','',array('id')));
    $c->leftJoin('modUserGroupMember','UserGroupMembers');
    $c->leftJoin('modUserGroup','UserGroup','UserGroup.id = UserGroupMembers.user_group');
    $c->leftJoin('disUserGroupProfile','UserGroupProfile','UserGroupProfile.usergroup = UserGroup.id AND UserGroupProfile.color != ""');
    $c->where(array(
        'Session.access:>=' => $timeago,
    ));
    $c->sortby('`UserGroupProfile`.`color`,`Session`.`access`','ASC');
    $activeUsers = $modx->getCollection('modUser',$c);
    $as = '';
    foreach ($activeUsers as $activeUser) {
        $as .= $discuss->getChunk($activeUserRowTpl,$activeUser->toArray());
    }
    $placeholders['activeUsers'] = $modx->lexicon('discuss.users_active_in_last',array(
        'users' => trim($as,','),
        'threshold' => $threshold,
    ));
    unset($as,$activeUsers,$activeUser,$timeago,$threshold);
}

/* total active */
$placeholders['totalMembersActive'] = $modx->getCount('disSession',array('user:!=' => 0));
$placeholders['totalVisitorsActive'] = $modx->getCount('disSession',array('user' => 0));

/* latest post */
$c = $modx->newQuery('disPost');
$c->select(array(
    'disPost.id',
    'disPost.title',
    'disPost.createdon',
    'disPost.author',
    'Author.username',
    '`Thread`.`id` AS `thread`',
));
$c->select($modx->getSelectColumns('disBoard','Board','',array('name')).' AS `board`');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->innerJoin('disPost','Thread');
$c->leftJoin('disBoardUserGroup','UserGroups',$modx->getSelectColumns('disBoard','Board','',array('id')).' = '.$modx->getSelectColumns('disBoardUserGroup','UserGroups','',array('board')));
$c->orCondition(array(
    'UserGroups.usergroup' => null,
),null,1);
if (!empty($_groups)) {
    $c->orCondition(array(
        'UserGroups.usergroup:IN' => $_groups,
    ),null,1);
}
$c->sortby($modx->getSelectColumns('disPost','disPost','',array('createdon')),'DESC');
$latestPost = $modx->getObject('disPost',$c);

if ($latestPost) {
    $la = $latestPost->toArray('latestPost.',true);
    $placeholders = array_merge($placeholders,$la);
}
unset($la,$latestPost,$c);

/* output */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.home.js');
return $discuss->output('home',$placeholders);

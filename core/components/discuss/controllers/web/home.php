<?php
/**
 * Handle the home page
 */
$discuss->setSessionPlace('home');

/* get default chunk properties */
$boardRowTpl = $modx->getOption('boardRowTpl',$scriptProperties,'board/disBoardLi');
$categoryRowTpl = $modx->getOption('categoryRowTpl',$scriptProperties,'category/disCategoryLi');
$subForumLinkTpl = $modx->getOption('subForumsLinkTpl',$scriptProperties,'board/disSubForumLink');
$cssUnreadCls = $modx->getOption('cssUnreadCls',$scriptProperties,'dis-unread');
$lastPostByTpl = $modx->getOption('lastPostByTpl',$scriptProperties,'disLastPostBy');
$postRowTpl = $modx->getOption('postRowTpl',$scriptProperties,'disPostLi');

/* get default css classes properties */
$cssBoardRow = $modx->getOption('cssBoardRow',$scriptProperties,'dis-board-li');
$cssUnread = $modx->getOption('cssUnread',$scriptProperties,'dis-unread');
$cssRowAlt = $modx->getOption('cssRowAlt',$scriptProperties,'alt');
$cssRowEven = $modx->getOption('cssRowEven',$scriptProperties,'even');

$_groups = $modx->user->getUserGroups();

$placeholders = array();

/* get boards */
$placeholders['boards'] = $modx->hooks->load('board/getlist',array(
    'board' => 0,
    'groups' => $_groups,
));

/* process logout */
if (isset($scriptProperties['logout']) && $scriptProperties['logout']) {
    $response = $modx->runProcessor('security/logout');
    $url = $modx->makeUrl($modx->resource->get('id'));
    $modx->sendRedirect($url);
}

/* action buttons */
$actionButtons = array();
if ($modx->user->isAuthenticated()) { /* if logged in */
    $actionButtons[] = array('url' => '[[~[[*id]]]]?read=1', 'text' => $modx->lexicon('discuss.mark_all_as_read'));

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
    $placeholders['activeUsers'] = $modx->hooks->load('user/active_in_last');
}

/* total active */
$placeholders['totalMembersActive'] = $modx->getCount('disSession',array('user:!=' => 0));
$placeholders['totalVisitorsActive'] = $modx->getCount('disSession',array('user' => 0));

/* latest post */
$latestPost = $modx->hooks->load('post/latest',array(
    'groups' => $_groups,
));
if ($latestPost) {
    $la = $latestPost->toArray('latestPost.',true);
    $placeholders = array_merge($placeholders,$la);
    unset($la,$latestPost);
}

$placeholders['recent_posts'] = $modx->hooks->load('post/recent');

/* breadcrumbs */
$trail = array(array('text' => $modx->getOption('discuss.forum_title'),'active' => true));
$placeholders['trail'] = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
unset($trail);

return $placeholders;
<?php
/**
 * Handle the home page
 */
$discuss->setSessionPlace('home');
$discuss->setPageTitle($modx->getOption('discuss.forum_title'));

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
$c = array(
    'board' => 0,
    'groups' => $_groups,
);
if (!empty($scriptProperties['category'])) $c['category'] = (int)$scriptProperties['category'];
$cacheKey = 'discuss/user/'.$discuss->user->get('id').'/index-'.md5(serialize($c));
$boardIndex = $modx->cacheManager->get($cacheKey);
if (empty($boardIndex) || true) {
    $boardIndex = $discuss->hooks->load('board/getlist',$c);
    $modx->cacheManager->set($cacheKey,$boardIndex,3600);
}
$placeholders['boards'] = $boardIndex;
unset($boardIndex);

/* process logout */
if (isset($scriptProperties['logout']) && $scriptProperties['logout']) {
    $response = $modx->runProcessor('security/logout');
    $url = $modx->makeUrl($modx->resource->get('id'));
    $modx->sendRedirect($url);
}

/* action buttons */
$actionButtons = array();
if ($discuss->isLoggedIn) { /* if logged in */
    $actionButtons[] = array('url' => $discuss->url.'?read=1', 'text' => $modx->lexicon('discuss.mark_all_as_read'));

    $authLink = $discuss->url.'logout';
    $authMsg = $modx->lexicon('discuss.logout');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');
    $actionButtons[] = array('url' => $authLink, 'text' => $authMsg);
} else { /* if logged out */
    $authLink = $discuss->url.'login';
    $authMsg = $modx->lexicon('discuss.login');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');

    $modx->setPlaceholder('discuss.loginForm',$discuss->getChunk('disLogin'));
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($authLink,$authMsg,$actionButtons);

/* stats */
$placeholders['totalPosts'] = number_format((int)$modx->getCount('disPost'));
$placeholders['totalTopics'] = number_format((int)$modx->getCount('disPost',array('parent' => 0)));
$placeholders['totalMembers'] = number_format((int)$modx->getCount('disUser'));

/* active in last 40 */
if ($modx->getOption('discuss.show_whos_online',null,true) && $modx->hasPermission('discuss.view_online')) {
    $placeholders['activeUsers'] = $discuss->hooks->load('user/active_in_last');
} else {
    $placeholders['activeUsers'] = '';
}

/* total active */
$placeholders['totalMembersActive'] = number_format((int)$modx->getCount('disSession',array('user:!=' => 0)));
$placeholders['totalVisitorsActive'] = number_format((int)$modx->getCount('disSession',array('user' => 0)));

/* recent posts */
$recent = $discuss->hooks->load('post/recent');
$placeholders['recent_posts'] = $recent['results'];
unset($recent);

/* breadcrumbs */
$trail = array();
if (!empty($scriptProperties['category'])) {
    $category = $modx->getObject('disCategory',$scriptProperties['category']);
}
if (!empty($category)) {
    $trail[] = array(
        'text' => $modx->getOption('discuss.forum_title'),
        'url' => $discuss->url
    );
    $trail[] = array(
        'text' => $category->get('name'),
        'active' => true
    );
} else {
    $trail[] = array('text' => $modx->getOption('discuss.forum_title'),'active' => true);
}
$placeholders['trail'] = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
unset($trail);

return $placeholders;
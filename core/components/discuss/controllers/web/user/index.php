<?php
/**
 *
 * @package discuss
 */
$discuss->setSessionPlace('user:'.$scriptProperties['user']);
$modx->lexicon->load('discuss:user');

/* allow external profile page */
$profileResourceId = $modx->getOption('discuss.profile_resource_id',null,0);
if (!empty($profileResourceId) && $discuss->ssoMode) {
    $url = $modx->makeUrl($profileResourceId,'',array('discuss' => 1,'user' => $scriptProperties['user']));
    $modx->sendRedirect($url);
}


/* get default properties */
$cssPostRowCls = $modx->getOption('cssPostRowCls',$scriptProperties,'dis-board-li');
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'');
$numRecentPosts = $modx->getOption('numRecentPosts',$scriptProperties,10);
$postRowTpl = $modx->getOption('postRowTpl',$scriptProperties,'disPostLi');

/* get user */
if (empty($scriptProperties['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$scriptProperties['user']);
if ($user == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($user->get('username'));

$placeholders = $user->toArray();

/* format age */
$age = strtotime($user->get('birthdate'));
$age = round((time() - $age) / 60 / 60 / 24 / 365);
$placeholders['age'] = $age;

/* format gender */
switch ($user->get('gender')) {
    case 'm': $placeholders['gender'] = $modx->lexicon('discuss.male'); break;
    case 'f': $placeholders['gender'] = $modx->lexicon('discuss.female'); break;
    default: $placeholders['gender'] = ''; break;
}

/* get last visited thread */
$placeholders['last_reading'] = '';
$lastThread = $user->getLastVisitedThread();
if ($lastThread) {
    $firstPost = $modx->getObject('disPost',$lastThread->get('post_first'));
    $placeholders = array_merge($placeholders,$lastThread->toArray('lastThread.'));
    if ($firstPost) {
        $placeholders = array_merge($placeholders,$firstPost->toArray('lastThread.'));
    }
}

/* recent posts */
$recent = $discuss->hooks->load('post/recent',array(
    'user' => $user->get('id'),
));
$placeholders['recent_posts'] = $recent['results'];
unset($recent);

if (!$user->get('show_email') && !$discuss->user->isAdmin()) {
    $placeholders['email'] = '';
}
if (!$user->get('show_online') && !$discuss->user->isAdmin()) {
    $placeholders['last_active'] = '';
} elseif (!empty($placeholders['last_active']) && $placeholders['last_active'] != '-001-11-30 00:00:00') {
    $placeholders['last_active'] = strftime($discuss->dateFormat,strtotime($placeholders['last_active']));
} else {
    $placeholders['last_active'] = '';
}
if ($modx->hasPermission('discuss.track_ip')) {
    $placeholders['ip'] = '';
}

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;
<?php
/**
 *
 * @package discuss
 */
$discuss->setSessionPlace('user:'.$scriptProperties['user']);
$modx->lexicon->load('discuss:user');

/* get default properties */
$cssPostRowCls = $modx->getOption('cssPostRowCls',$scriptProperties,'dis-board-li');
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');
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
$lastThread = $user->getOne('ThreadLastVisited');
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


/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;
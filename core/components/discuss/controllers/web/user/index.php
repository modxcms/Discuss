<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
/**
 *
 * @package discuss
 */
if (empty($scriptProperties['user'])) $scriptProperties['user'] = $discuss->user->get('id');
$discuss->setSessionPlace('user:'.$scriptProperties['user']);
$modx->lexicon->load('discuss:user');

if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}

/* allow external profile page */
$profileResourceId = $modx->getOption('discuss.profile_resource_id',null,0);
if (!empty($profileResourceId) && $discuss->ssoMode) {
    $url = $modx->makeUrl($profileResourceId,'',array('discuss' => 1,'user' => $scriptProperties['user']));
    $modx->sendRedirect($url);
}

/* get user */
if (empty($scriptProperties['user'])) { $discuss->sendErrorPage(); }
$user = trim($scriptProperties['user'],' /');
$key = intval($user) <= 0 ? 'username' : 'id';
$c = array();
$c[!empty($scriptProperties['i']) ? 'integrated_id' : $key] = $user;
$user = $modx->getObject('disUser',$c);
if ($user == null) { $discuss->sendErrorPage(); }
$discuss->setPageTitle($user->get('username'));
$isSelf = $modx->user->get('id') == $user->get('user');

$placeholders = $user->toArray();
$placeholders['avatarUrl'] = $user->getAvatarUrl();
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
        $placeholders['last_post_url'] = $firstPost->getUrl();
    }
}

/* recent posts */
if (!empty($options['showRecentPosts'])) {
    $recent = $discuss->hooks->load('post/recent',array(
        'user' => $user->get('id'),
    ));
    $placeholders['recent_posts'] = $recent['results'];
    unset($recent);
}

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

$user->getOne('User');
$placeholders['groups'] = implode(', ',$user->User->getUserGroupNames());

/* do output */
$placeholders['canEdit'] = $isSelf;
$placeholders['canAccount'] = $isSelf;
$placeholders['canMerge'] = $isSelf;
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;
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

$placeholders = $user->toArray();

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

$placeholders['groups'] = implode(', ',$user->getUserGroupNames());

/* do output */
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;
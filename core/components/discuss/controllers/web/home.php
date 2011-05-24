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
 * Handle the home page
 */
$discuss->setSessionPlace('home');
$discuss->setPageTitle($modx->getOption('discuss.forum_title'));

$placeholders = array();

/* get boards */
if (!empty($options['showBoards'])) {
    $c = array(
        'board' => 0,
    );
    if (!empty($scriptProperties['category'])) $c['category'] = (int)$scriptProperties['category'];
    $placeholders['boards'] = $discuss->hooks->load('board/getlist',$c);
}

/* process logout */
if (isset($scriptProperties['logout']) && $scriptProperties['logout']) {
    $response = $modx->runProcessor('security/logout');
    $url = $discuss->request->makeUrl();
    $modx->sendRedirect($url);
}
if (isset($scriptProperties['read']) && !empty($scriptProperties['read'])) {
    $discuss->hooks->load('thread/read_all',$c);
}

/* action buttons */
$actionButtons = array();
if ($discuss->user->isLoggedIn) { /* if logged in */
    $actionButtons[] = array('url' => $discuss->request->makeUrl('',array('read' => 1)), 'text' => $modx->lexicon('discuss.mark_all_as_read'));

    $authLink = $discuss->request->makeUrl('logout');
    $authMsg = $modx->lexicon('discuss.logout');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');
    $actionButtons[] = array('url' => $authLink, 'text' => $authMsg);
} else { /* if logged out */
    $authLink = $discuss->request->makeUrl('login');
    $authMsg = $modx->lexicon('discuss.login');
    $modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');

    if (!empty($options['showLoginForm'])) {
        $modx->setPlaceholder('discuss.loginForm',$discuss->getChunk('disLogin'));
    }
}
$placeholders['actionbuttons'] = $discuss->buildActionButtons($actionButtons,'dis-action-btns right');
unset($authLink,$authMsg,$actionButtons);

/* stats */
if (!empty($options['showStatistics'])) {
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

    /* forum activity */
    $activity = $modx->getObject('disForumActivity',array(
        'day' => date('Y-m-d'),
    ));
    if (!$activity) {
        $activity = $modx->newObject('disForumActivity');
        $activity->set('day',date('Y-m-d'));
        $activity->save();
    }
    $placeholders = array_merge($placeholders,$activity->toArray('activity.'));
}

/* recent posts */
if (!empty($options['showRecentPosts'])) {
    $cacheKey = 'discuss/board/recent/'.$discuss->user->get('id');
    $recent = $modx->cacheManager->get($cacheKey);
    if (empty($recent)) {
        $recent = $discuss->hooks->load('post/recent');
        $modx->cacheManager->set($cacheKey,$recent,$modx->getOption('discuss.cache_time',null,3600));
    }
    $placeholders['recent_posts'] = $recent['results'];
    unset($recent);
} else {
    $placeholders['recent_posts'] = '';
}


/* breadcrumbs */
if (!empty($options['showBreadcrumbs'])) {
    $trail = array();
    if (!empty($scriptProperties['category'])) {
        $category = $modx->getObject('disCategory',$scriptProperties['category']);
    }
    if (!empty($category)) {
        $trail[] = array(
            'text' => $modx->getOption('discuss.forum_title'),
            'url' => $discuss->request->makeUrl(),
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
}

return $placeholders;
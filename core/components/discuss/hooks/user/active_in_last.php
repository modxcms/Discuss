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
 * Get users active in last X time
 */
/* setup defaults/permissions */
$threshold = $modx->getOption('discuss.user_active_threshold',null,40);
$timeAgo = time() - (60*($threshold));
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

/* build query */
$activeUsers = $modx->call('disUser','fetchActive',array(&$modx,$timeAgo));

/* iterate */
$as = array();
foreach ($activeUsers['results'] as $activeUser) {
    $activeUser->getUrl();
    $activeUserArray = $activeUser->toArray();
    $activeUserArray['style'] = ' style="';
    if (!empty($activeUserArray['color'])) {
        $activeUserArray['style'] .= 'color: '.$activeUserArray['color'];
    }
    $activeUserArray['style'] .= '"';
    if ($canViewProfiles) {
        $as[] = $discuss->getChunk('user/disActiveUserRow',$activeUserArray);
    } else {
        $as[] = $activeUser->get('username');
    }
}

/* parse into lexicon */
$list = $modx->lexicon('discuss.users_active_in_last',array(
    'users' => implode(', ',$as),
    'total' => $activeUsers['total'],
    'threshold' => $threshold,
));
return $list;
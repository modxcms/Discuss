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
 * Get a list of Categories
 *
 * @package discuss
 * @subpackage processors
 */
$isLimit = !empty($scriptProperties['limit']);
$isCombo = !empty($scriptProperties['combo']);
$sort = $modx->getOption('sort',$scriptProperties,'username');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('disUser');
$c->innerJoin('modUser','User');
if (!empty($query)) {
    $c->where(array(
        'disUser.username:LIKE' => '%'.$query.'%',
    ));
}
$count = $modx->getCount('disUser',$c);
if ($isLimit || $isCombo) {
    $c->limit($limit,$start);
}
$users = $modx->getCollection('disUser', $c);

/* iterate */
$list = array();
foreach ($users as $user) {
    $userArray = $user->toArray();
    if (!empty($userArray['last_active']) && $userArray['last_active'] != '0000-00-00 00:00:00' && $userArray['last_active'] != '-001-11-30 00:00:00') {
        $userArray['last_active'] = strftime('%b %d, %Y %I:%M %p',strtotime($userArray['last_active']));
    } else {
        $userArray['last_active'] = '';
    }
    $userArray['posts'] = number_format($userArray['posts']);
    $list[]= $userArray;
}
return $this->outputArray($list,$count);
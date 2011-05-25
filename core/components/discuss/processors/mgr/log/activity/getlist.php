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
$sort = $modx->getOption('sort',$scriptProperties,'createdon');
$dir = $modx->getOption('dir',$scriptProperties,'DESC');
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('disLogActivity');
$c->innerJoin('disUser','User');
if (!empty($query)) {
    $c->where(array(
        'User.username:LIKE' => '%'.$query.'%',
        'OR:disLogActivity.action:LIKE' => '%'.$query.'%',
        'OR:disLogActivity.ip:LIKE' => '%'.$query.'%',
    ));
}
$count = $modx->getCount('disLogActivity',$c);
$c->select($modx->getSelectColumns('disLogActivity','disLogActivity'));
$c->select($modx->getSelectColumns('disUser','User','',array('username')));
if ($isLimit || $isCombo) {
    $c->limit($limit,$start);
}
$activity = $modx->getCollection('disLogActivity', $c);

/* iterate */
$list = array();
foreach ($activity as $activityItem) {
    $logArray = $activityItem->toArray();
    if (!empty($logArray['createdon']) && $logArray['createdon'] != '0000-00-00 00:00:00' && $logArray['createdon'] != '-001-11-30 00:00:00') {
        $logArray['createdon'] = strftime('%b %d, %Y %I:%M %p',strtotime($logArray['createdon']));
    } else {
        $logArray['createdon'] = '';
    }
    $list[]= $logArray;
}
return $this->outputArray($list,$count);
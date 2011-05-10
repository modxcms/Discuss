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
 * Get a list of Posts for a User
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
$user = $modx->getOption('user',$scriptProperties,0);
if (empty($user)) return $modx->error->failure($modx->lexicon('discuss.user_err_ns'));

$c = $modx->newQuery('disPost');
$c->where(array(
    'author' => $user,
));
$count = $modx->getCount('disPost',$c);
if ($isCombo || $isLimit) {
    $c->limit($limit,$start);
}
$c->sortby($sort,$dir);
$posts = $modx->getCollection('disPost', $c);

$list = array();
foreach ($posts as $post) {
    $postArray = $post->toArray();
    $list[]= $postArray;
}
return $this->outputArray($list,$count);
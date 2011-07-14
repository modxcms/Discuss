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
 * Handle updating of profile
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('discuss:user');

$profile = $modx->user->getOne('Profile');
if (empty($profile)) return '';

$fields = $profile->toArray();

$useExtended = $modx->getOption('useExtended',$scriptProperties,true);
if ($useExtended) {
    $extended = $fields['extended'];
    if (!empty($extended)) {
        $fields = array_merge($extended,$fields);
    }
}
$disUser = $modx->getObject('disUser',array(
   'user' => $modx->user->get('id'),
));

if ($disUser) {
    $fields = array_merge($disUser->toArray(),$fields);
    $fields['show_email'] = !empty($fields['show_email']) ? 1 : 0;
    $fields['show_online'] = !empty($fields['show_online']) ? 1 : 0;
    $fields['post_sort_dir'] = $disUser->getSetting('discuss.post_sort_dir','ASC');
}

$forumsResourceId = $modx->getOption('discuss.forums_resource_id',null,0);
if (!empty($_REQUEST['discuss']) && !empty($forumsResourceId)) {
    $url = $modx->makeUrl($forumsResourceId,'','','full');
    $fields['forums_url'] = $url;
}

$placeholderPrefix = $modx->getOption('placeholderPrefix',$scriptProperties,'up');
$modx->toPlaceholders($fields,$placeholderPrefix);
return '';
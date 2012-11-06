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
 *
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $fields
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('discuss:user');

/** @var disUser $disUser */
$disUser = $modx->getObject('disUser',array(
    'user' => $modx->user->get('id'),
));
if (!$disUser) return true;

unset($fields['id']);
unset($fields['user']);

$fields['show_email'] = !empty($fields['show_email']) ? 1 : 0;
$fields['show_online'] = !empty($fields['show_online']) ? 1 : 0;

if (isset($fields['fullname']) && empty($fields['name_first'])) {
    $name = explode(' ',$fields['fullname']);
    $fields['name_first'] = $name[0];
    $fields['name_last'] = !empty($name[1]) ? $name[1] : '';
}

$disUser->fromArray($fields);
if (!empty($fields['signature'])) {
    $fields['signature'] = str_replace(array('&#91;','&#93;'),array('[',']'),$fields['signature']);
    $disUser->set('signature',$fields['signature']);
}

if (!empty($fields['birthdate'])) {
    $unixBirthdate = strtotime($fields['birthdate']);
    $disUser->set('birthdate',($unixBirthdate !== false) ? $unixBirthdate : '');
}

if (!$disUser->save()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not sync profile information during UpdateProfile snippet posthook: '.print_r($fields,true));
}

/* handle post_sort_dir setting */
if (!empty($fields['post_sort_dir'])) {
    switch (strtoupper($fields['post_sort_dir'])) {
        case 'DESC': $fields['post_sort_dir'] = 'DESC'; break;
        default: $fields['post_sort_dir'] = 'ASC'; break;
    }
    $disUser->setSetting('discuss.post_sort_dir',$fields['post_sort_dir'],'ASC');
}

/* clear cache */
$modx->getCacheManager();
$modx->cacheManager->delete('discuss/board/user/');
$modx->cacheManager->delete('discuss/board/index/');

$forumsResourceId = $modx->getOption('discuss.forums_resource_id',null,0);

if (!empty($_REQUEST['discuss']) && !empty($forumsResourceId)) {
    $url = $modx->makeUrl($forumsResourceId,'','','full').'user/?user='.$disUser->get('id');
    $modx->sendRedirect($url);
}
return true;

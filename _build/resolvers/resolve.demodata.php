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
 * Resolve creating db tables
 *
 * @package discuss
 * @subpackage build
 */
if ($object->xpdo && !empty($options['install_demodata'])) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:

$modx =& $object->xpdo;
$modelPath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/';
$modx->addPackage('discuss',$modelPath);

$modx->log(modX::LOG_LEVEL_INFO,'Installing demo data...');

$category = $modx->newObject('disCategory');
$category->fromArray(array(
    'name' => 'Welcome',
    'description' => 'The welcome section.',
    'collapsible' => true,
));
$category->save();

$board = $modx->newObject('disBoard');
$board->fromArray(array(
    'name' => 'Discuss 101',
    'category' => $category->get('id'),
    'description' => 'Introduce yourself to the community here.',
    'ignoreable' => true,
));
$board->save();

if ($modx->user && $modx->user instanceof modUser) {
    $modx->user->profile = $modx->user->getOne('Profile');
    if ($modx->user->profile && $modx->user->profile instanceof modUserProfile) {
        $disUser = $modx->newObject('disUser');
        $disUser->fromArray($modx->user->profile->toArray());
        $name = $modx->user->profile = $modx->user->profile->get('fullname');
        $name = explode(' ',$name);
        $disUser->fromArray(array(
            'user' => $modx->user->get('id'),
            'username' => $modx->user->get('username'),
            'createdon' => strftime('%Y-%m-%d %H:%M:%S'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'synced' => true,
            'syncedat' => strftime('%Y-%m-%d %H:%M:%S'),
            'source' => 'internal',
            'confirmed' => true,
            'confirmedon' => strftime('%Y-%m-%d %H:%M:%S'),
            'status' => disUser::ACTIVE,
            'name_first' => $name[0],
            'name_last' => !empty($name[1]) ? $name[1] : '',
            'salt' => $modx->user->get('salt'),
        ));
        $disUser->save();
    }
}
            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;
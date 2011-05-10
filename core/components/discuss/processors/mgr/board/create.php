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
 * Create a Board.
 * 
 * @package discuss
 * @subpackage processors
 */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.board_err_ns_name'));
if (empty($scriptProperties['category'])) $modx->error->addField('category',$modx->lexicon('discuss.board_err_ns_category'));

$scriptProperties['locked'] = !empty($scriptProperties['locked']) ? true : false;
$scriptProperties['ignoreable'] = !empty($scriptProperties['ignoreable']) ? true : false;


$category = $modx->getObject('disCategory',$scriptProperties['category']);
if (empty($category)) $modx->error->addField('category',$modx->lexicon('discuss.board_err_ns_category'));

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$board = $modx->newObject('disBoard');
$board->fromArray($scriptProperties);

/* add default user groups */
$defaultUserGroups = $category->get('default_usergroups');
if (empty($defaultUserGroups)) {
    $defaultUserGroups = $modx->getOption('discuss.default_board_usergroups',null,false);
}
if (!empty($defaultUserGroups)) {
    $defaultUserGroups = explode(',',$defaultUserGroups);
    $ugs = array();
    foreach ($defaultUserGroups as $userGroupName) {
        $usergroup = $modx->getObject('modUserGroup',array(
            'name' => trim($userGroupName),
        ));
        if ($usergroup) {
            $ug = $modx->newObject('disBoardUserGroup');
            $ug->set('usergroup',$usergroup->get('id'));
            $ugs[] = $ug;
        }
    }
    $board->addMany($ugs,'UserGroups');
}

/* add default moderators */
$defaultModerators = $category->get('default_moderators');
if (empty($defaultUserGroups)) {
    $defaultUserGroups = $modx->getOption('discuss.default_board_moderators',null,false);
}
if (!empty($defaultModerators)) {
    $defaultModerators = explode(',',$defaultModerators);
    $mods = array();
    foreach ($defaultModerators as $username) {
        $c = $modx->newQuery('disUser');
        $c->innerJoin('modUser','User');
        $c->where(array(
            'User.username' => trim($username),
        ));
        $user = $modx->getObject('disUser',$c);
        if ($user) {
            $mod = $modx->newObject('disModerator');
            $mod->set('user',$user->get('id'));
            $mods[] = $mod;
        }
    }
    $board->addMany($mods,'Moderators');
}

if ($board->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_save'));
}

return $modx->error->success('',$board);
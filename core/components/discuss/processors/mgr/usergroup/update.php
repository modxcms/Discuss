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
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $scriptProperties
 *
 * @package discuss
 * @subpackage processors
 */
/* get usergroup */
/** @var disUserGroupProfile $profile */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_ns'));
$profile = $modx->getObject('disUserGroupProfile',array('usergroup' => $scriptProperties['id']));
if ($profile == null) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf'));

/** @var modUserGroup $usergroup */
$usergroup = $profile->getOne('UserGroup');
if (!$usergroup) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf',array('id' => $scriptProperties['id'])));

/* do validation */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.usergroup_err_ns_name'));

if ($modx->error->hasError()) {
    $modx->error->failure();
}

/* set fields */
$scriptProperties['post_based'] = !empty($scriptProperties['post_based']) ? true : false;
$profile->fromArray($scriptProperties);
$usergroup->fromArray($scriptProperties);

/* save board */
if ($profile->save() == false || $usergroup->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_save'));
}

/* upload badge */
if (!empty($_FILES['image'])) {
    $profile->uploadBadge($_FILES['image'],true);
    $profile->save();
}


/* set members */
if (isset($scriptProperties['members'])) {

    /** @var modUserGroupMember $membership */
    $members = $modx->getCollection('modUserGroupMember',array('user_group' => $usergroup->get('id')));
    foreach ($members as $membership) { $membership->remove(); }
    unset($members,$member);

    $members = $modx->fromJSON($scriptProperties['members']);
    foreach ($members as $member) {
        $user = $modx->getObject('modUser',$member['id']);
        if ($user) {
            $membership = $modx->newObject('modUserGroupMember');
            $membership->set('user_group',$usergroup->get('id'));
            $membership->set('member',$user->get('id'));
            $membership->set('role',empty($member['role']) ? 1 : $member['role']);
            $membership->save();
        }
    }
}

/* set board access */
if (isset($scriptProperties['boards'])) {
    /** @var disBoardUserGroup $bug */
    $bugs = $modx->getCollection('disBoardUserGroup',array('usergroup' => $usergroup->get('id')));
    foreach ($bugs as $bug) { $bug->remove(); }
    unset($bugs,$bug);

    $boards = $modx->fromJSON($scriptProperties['boards']);
    foreach ($boards as $board) {
        if (!$board['access']) continue;

        $bug = $modx->newObject('disBoardUserGroup');
        $bug->set('usergroup',$usergroup->get('id'));
        $bug->set('board',$board['id']);
        $bug->save();
    }
}

return $modx->error->success();
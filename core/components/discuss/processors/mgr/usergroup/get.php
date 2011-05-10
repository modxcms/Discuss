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
 * Get a User Group
 * 
 * @package discuss
 * @subpackage processors
 */
/* get user group */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_ns'));
$c = $modx->newQuery('modUserGroup');
$c->select(array(
    'modUserGroup.*',
    'Profile.post_based',
    'Profile.min_posts',
    'Profile.color',
    'Profile.image',
));
$c->leftJoin('disUserGroupProfile','Profile','modUserGroup.id = Profile.usergroup');
$c->where(array(
    'modUserGroup.id' => $scriptProperties['id'],
));
$usergroup = $modx->getObject('modUserGroup',$c);
if ($usergroup == null) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf'));

$usergroupArray = $usergroup->toArray();

/* get members */
$c = $modx->newQuery('modUserGroupMember');
$c->innerJoin('modUser','User');
$c->leftJoin('disUser','disUser','disUser.user = User.id');
$c->innerJoin('modUserGroupRole','UserGroupRole');
$c->where(array(
    'user_group' => $usergroup->get('id'),
));

$c->select($modx->getSelectColumns('modUserGroupMember','modUserGroupMember'));
$c->select(array(
    'User.username',
    'User.id AS user',
    'disUser.email',
    'disUser.id AS disuser_id',
    'UserGroupRole.name AS role_name',
));
$c->sortby($modx->getSelectColumns('disUser','User','',array('username')),'ASC');
$members = $modx->getCollection('modUserGroupMember',$c);
$list = array();
foreach ($members as $member) {
    $list[] = array(
        $member->get('user'),
        $member->get('username'),
        $member->get('role'),
        $member->get('role_name'),
    );
}
$usergroup->set('members','(' . $modx->toJSON($list) . ')');
unset($members,$member,$list,$c);


/* get boards */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Descendants');
$c->leftJoin('disBoardUserGroup','UserGroups');
$c->innerJoin('disCategory','Category');
$c->select($modx->getSelectColumns('disBoard','disBoard'));
$c->select(array(
    'IF(UserGroups.usergroup = "'.$usergroup->get('id').'",1,0) AS access',
    'MAX(Descendants.depth) AS depth',
    'Category.name AS category_name',
));
$c->sortby('disBoard.category','ASC');
$c->sortby('disBoard.map','ASC');
$c->groupby('disBoard.id');
$boards = $modx->getCollection('disBoard',$c);
$list = array();
foreach ($boards as $board) {
    $list[] = array(
        $board->get('id'),
        str_repeat('--',$board->get('depth')-1).$board->get('name'),
        $board->get('access') ? true : false,
        $board->get('category'),
        $board->get('category_name'),
    );
}
$usergroup->set('boards','(' . $modx->toJSON($list) . ')');
unset($boards,$board,$list,$c);

/* get badge */
$disUserGroup = $modx->getObject('disUserGroupProfile',array(
    'usergroup' => $usergroup->get('id'),
));
if ($disUserGroup) {
    $usergroup->set('badge_full',$disUserGroup->getBadge());
}

/* output */
return $modx->error->success('',$usergroup);

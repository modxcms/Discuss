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
 * Get the user groups in tree node format
 *
 * @param string $id The parent ID
 *
 * @package discuss
 * @subpackage processors
 */
if (!$modx->hasPermission('access_permissions')) return $modx->error->failure($modx->lexicon('permission_denied'));
$modx->lexicon->load('user');

$scriptProperties['id'] = !isset($scriptProperties['id']) ? 0 : str_replace('n_ug_','',$scriptProperties['id']);

$g = $modx->getObject('modUserGroup',$scriptProperties['id']);

$c = $modx->newQuery('modUserGroupMember');
$c->select(array(
    'COUNT(`member`)',
));
$c->where(array(
    'modUserGroupMember.user_group = modUserGroup.id',
));
$c->prepare();
$memberCtSql = $c->toSql();

$c = $modx->newQuery('modUserGroup');
$c->select($modx->getSelectColumns('modUserGroup','modUserGroup'));
$c->select(array(
    '('.$memberCtSql.') AS members',
));
$c->where(array(
    'parent' => $scriptProperties['id'],
    'AND:modUserGroup.name:!=' => 'Administrator',
));
$c->sortby('name','ASC');
$groups = $modx->getCollection('modUserGroup',$c);

$list = array();
foreach ($groups as $group) {
    $list[] = array(
        'text' => $group->get('name').' ('.$group->get('id').') - <i>'.$modx->lexicon('discuss.members_ct',array('members' => $group->get('members'))).'</i>',
        'id' => 'n_ug_'.$group->get('id'),
        'leaf' => 0,
        'type' => 'usergroup',
        'cls' => 'icon-group',
    );
}

if (!empty($scriptProperties['id'])) {
    $c = $modx->newQuery('modUser');
    $c->innerJoin('modUserGroupMember','UserGroupMembers');
    $c->where(array(
        'UserGroupMembers.user_group' => $scriptProperties['id'],
    ));
    $c->sortby('username','ASC');
    $users = $modx->getCollection('modUser',$c);
    foreach ($users as $user) {
        $list[] = array(
            'text' => $user->get('username'),
            'id' => 'n_user_'.$user->get('id'),
            'leaf' => 1,
            'user' => $user->get('id'),
            'usergroup' => $scriptProperties['id'],
            'type' => 'user',
            'cls' => 'icon-user',
        );
    }

}

return $this->toJSON($list);
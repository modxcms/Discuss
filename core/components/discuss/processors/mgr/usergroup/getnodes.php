<?php
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


$c = $modx->newQuery('modUserGroup');
$c->where(array(
    'parent' => $scriptProperties['id'],
    'AND:modUserGroup.name:!=' => 'Administrator',
));
$groups = $modx->getCollection('modUserGroup',$c);

$list = array();
foreach ($groups as $group) {
    $list[] = array(
        'text' => $group->get('name'),
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
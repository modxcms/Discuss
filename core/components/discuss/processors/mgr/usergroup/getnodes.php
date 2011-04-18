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

$da = array();
foreach ($groups as $group) {
    $da[] = array(
        'text' => $group->get('name'),
        'id' => 'n_ug_'.$group->get('id'),
        'leaf' => 0,
        'type' => 'usergroup',
        'cls' => 'icon-group',
    );
}

return $this->toJSON($da);
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

$_REQUEST['id'] = !isset($_REQUEST['id']) ? 0 : str_replace('n_ug_','',$_REQUEST['id']);

$g = $modx->getObject('modUserGroup',$_REQUEST['id']);
$groups = $modx->getCollection('modUserGroup',array('parent' => $_REQUEST['id']));

$da = array();
foreach ($groups as $group) {
    $da[] = array(
        'text' => $group->get('name'),
        'id' => 'n_ug_'.$group->get('id'),
        'leaf' => 0,
        'type' => 'usergroup',
        'cls' => 'icon-group',
        'menu' => array(
            'items' => array(
                array(
                    'text' => $modx->lexicon('user_group_create'),
                    'handler' => 'function(itm,e) {
                        this.createUserGroup(itm,e);
                    }',
                ),
                array(
                    'text' => $modx->lexicon('user_group_update'),
                    'handler' => 'function(itm,e) {
                        this.updateUserGroup(itm,e);
                    }',
                ),
                '-',
                array(
                    'text' => $modx->lexicon('user_group_remove'),
                    'handler' => 'function(itm,e) {
                        this.removeUserGroup(itm,e);
                    }',
                ),
            ),
        ),
    );
}

return $this->toJSON($da);
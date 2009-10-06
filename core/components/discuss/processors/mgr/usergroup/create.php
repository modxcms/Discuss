<?php
/**
 * Create a User Group
 *
 * @package discuss
 * @subpackage processors
 */

/* validate form */
if (empty($_POST['name'])) $modx->error->addField('name','Please specify a name.');
$_POST['post_based'] = !empty($_POST['post_based']) ? $_POST['post_based'] : 0;

$usergroup = $modx->newObject('modUserGroup');
$usergroup->set('parent',$_POST['parent']);
$usergroup->set('name',$_POST['name']);
if ($usergroup->save() === false) {
    return $modx->error->failure('discuss.usergroup_err_save');
}

$profile = $modx->newObject('disUserGroupProfile');
$profile->fromArray($_POST);
$profile->set('usergroup',$usergroup->get('id'));
$profile->save();

return $modx->error->success('',$usergroup);
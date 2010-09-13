<?php
/**
 * Create a User Group
 *
 * @package discuss
 * @subpackage processors
 */
/* validate form */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.usergroup_err_ns_name'));

/* check for existing */
$alreadyExists = $modx->getObject('modUserGroup',array('name' => $scriptProperties['name']));
if ($alreadyExists) $modx->error->addField('name',$modx->lexicon('discuss.usergroup_err_ae'));

/* if any errors, return */
if ($modx->error->hasError()) {
    return $modx->error->failure();
}

/* create usergroup */
$usergroup = $modx->newObject('modUserGroup');
$usergroup->fromArray($scriptProperties);

/* save usergroup */
if ($usergroup->save() === false) {
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_save'));
}

/* create discuss user group */
$profile = $modx->newObject('disUserGroupProfile');
$scriptProperties['post_based'] = !empty($scriptProperties['post_based']) ? $scriptProperties['post_based'] : 0;
$profile->fromArray($scriptProperties);
$profile->set('usergroup',$usergroup->get('id'));
if (!$profile->save()) {
    $usergroup->remove();
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_save'));
}

return $modx->error->success('',$usergroup);
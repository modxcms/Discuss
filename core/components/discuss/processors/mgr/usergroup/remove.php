<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get usergroup */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_ns'));
$profile = $modx->getObject('disUserGroupProfile',array('usergroup' => $scriptProperties['id']));
if ($profile == null) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf'));

$usergroup = $profile->getOne('UserGroup');
if (!$usergroup) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf',array('id' => $scriptProperties['id'])));

/* remove usergroup */
$profile->remove();
if ($usergroup->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_remove'));
}

return $modx->error->success('',$usergroup);
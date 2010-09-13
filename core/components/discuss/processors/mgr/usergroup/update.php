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

/* set moderators */
if (isset($scriptProperties['members'])) {
    $members = $modx->getCollection('modUserGroupMember',array('user_group' => $usergroup->get('id')));
    foreach ($members as $member) { $member->remove(); }
    unset($members,$member);

    $members = $modx->fromJSON($scriptProperties['members']);
    foreach ($members as $user) {
        $member = $modx->newObject('modUserGroupMember');
        $member->set('user_group',$usergroup->get('id'));
        $member->set('member',$user['id']);
        $member->set('role',$user['role']);
        $member->save();
    }
}

/* set board access */
if (isset($scriptProperties['boards'])) {
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
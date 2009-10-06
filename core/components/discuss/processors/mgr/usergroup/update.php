<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get usergroup */
if (empty($_POST['id'])) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_ns'));
$usergroup = $modx->getObject('modUserGroup',$_POST['id']);
if ($usergroup == null) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf'));

/* do validation */
if (empty($_POST['name'])) $modx->error->addField('name','Please enter a valid name.');

if ($modx->error->hasError()) {
    $modx->error->failure();
}

/* set fields */
$usergroup->fromArray($_POST);

/* save board */
if ($usergroup->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_save'));
}

/* set moderators */
if (isset($_POST['members'])) {
    $members = $modx->getCollection('modUserGroupMember',array('user_group' => $usergroup->get('id')));
    foreach ($members as $member) { $member->remove(); }
    unset($members,$member);

    $members = $modx->fromJSON($_POST['members']);
    foreach ($members as $user) {
        $member = $modx->newObject('modUserGroupMember');
        $member->set('user_group',$usergroup->get('id'));
        $member->set('member',$user['id']);
        $member->set('role',$user['role']);
        $member->save();
    }
}

/* set board access */
if (isset($_POST['boards'])) {
    $bugs = $modx->getCollection('disBoardUserGroup',array('usergroup' => $usergroup->get('id')));
    foreach ($bugs as $bug) { $bug->remove(); }
    unset($bugs,$bug);

    $boards = $modx->fromJSON($_POST['boards']);
    foreach ($boards as $board) {
        if (!$board['access']) continue;

        $bug = $modx->newObject('disBoardUserGroup');
        $bug->set('usergroup',$usergroup->get('id'));
        $bug->set('board',$board['id']);
        $bug->save();
    }
}

return $modx->error->success();
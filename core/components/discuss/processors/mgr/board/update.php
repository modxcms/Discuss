<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get board */
if (empty($_POST['id'])) return $modx->error->failure($modx->lexicon('discuss.board_err_ns'));
$board = $modx->getObject('disBoard',$_POST['id']);
if ($board == null) return $modx->error->failure($modx->lexicon('discuss.board_err_nf'));

/* do validation */
if (empty($_POST['name'])) $modx->error->addField('name','Please enter a valid name.');
if (empty($_POST['category'])) $modx->error->addField('category','Please select a Category for this Board to belong in.');

if ($modx->error->hasError()) {
    $modx->error->failure();
}

/* set fields */
$board->fromArray($_POST);

/* save board */
if ($board->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_save'));
}

/* set moderators */
if (isset($_POST['moderators'])) {
    $mods = $modx->getCollection('disModerator',array('board' => $board->get('id')));
    foreach ($mods as $mod) { $mod->remove(); }
    unset($mods,$mod);

    $moderators = $modx->fromJSON($_POST['moderators']);
    foreach ($moderators as $user) {
        $moderator = $modx->newObject('disModerator');
        $moderator->set('board',$board->get('id'));
        $moderator->set('user',$user['user']);
        $moderator->save();
    }
}


/* set user groups */
if (isset($_POST['usergroups'])) {
    $usergroups = $modx->getCollection('disBoardUserGroup',array('board' => $board->get('id')));
    foreach ($usergroups as $usergroup) { $usergroup->remove(); }
    unset($usergroups,$usergroup);

    $usergroups = $modx->fromJSON($_POST['usergroups']);
    foreach ($usergroups as $usergroup) {
        $access = $modx->newObject('disBoardUserGroup');
        $access->set('board',$board->get('id'));
        $access->set('usergroup',$usergroup['id']);
        $access->save();
    }
}

return $modx->error->success('',$board);
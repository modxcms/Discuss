<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.board_err_ns'));
$board = $modx->getObject('disBoard',$scriptProperties['id']);
if (!$board) return $modx->error->failure($modx->lexicon('discuss.board_err_nf'));

/* do validation */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.board_err_ns_name'));
if (empty($scriptProperties['category'])) $modx->error->addField('category',$modx->lexicon('discuss.board_err_ns_category'));

if ($modx->error->hasError()) {
    $modx->error->failure();
}

/* set fields */
$board->fromArray($scriptProperties);

/* save board */
if ($board->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_save'));
}

/* set moderators */
if (isset($scriptProperties['moderators'])) {
    $mods = $modx->getCollection('disModerator',array('board' => $board->get('id')));
    foreach ($mods as $mod) { $mod->remove(); }
    unset($mods,$mod);

    $moderators = $modx->fromJSON($scriptProperties['moderators']);
    foreach ($moderators as $user) {
        $moderator = $modx->newObject('disModerator');
        $moderator->set('board',$board->get('id'));
        $moderator->set('user',$user['user']);
        $moderator->save();
    }
}


/* set user groups */
if (isset($scriptProperties['usergroups'])) {
    $usergroups = $modx->getCollection('disBoardUserGroup',array('board' => $board->get('id')));
    foreach ($usergroups as $usergroup) { $usergroup->remove(); }
    unset($usergroups,$usergroup);

    $usergroups = $modx->fromJSON($scriptProperties['usergroups']);
    foreach ($usergroups as $usergroup) {
        $access = $modx->newObject('disBoardUserGroup');
        $access->set('board',$board->get('id'));
        $access->set('usergroup',$usergroup['id']);
        $access->save();
    }
}

return $modx->error->success('',$board);
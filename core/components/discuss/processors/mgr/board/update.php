<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
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

$scriptProperties['locked'] = !empty($scriptProperties['locked']) ? true : false;
$scriptProperties['ignoreable'] = !empty($scriptProperties['ignoreable']) ? true : false;

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
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
 * Grab a Board.
 * 
 * @package discuss
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.board_err_ns'));
$board = $modx->getObject('disBoard',$scriptProperties['id']);
if (!$board) return $modx->error->failure($modx->lexicon('discuss.board_err_nf',array('id' => $scriptProperties['id'])));

/* get moderators */
$c = $modx->newQuery('disModerator');
$c->select($modx->getSelectColumns('disModerator','disModerator'));
$c->select(array(
    'User.username',
));
$c->innerJoin('disUser','User');
$c->where(array(
    'board' => $board->get('id'),
));
$c->sortby($modx->getSelectColumns('disUser','User','',array('username')),'ASC');
$moderators = $board->getMany('Moderators',$c);
$mods = array();
foreach ($moderators as $moderator) {
    $mods[] = array(
        $moderator->get('user'),
        $moderator->get('username'),
    );
}
$board->set('moderators','('.$modx->toJSON($mods).')');

/* get user groups */
$c = $modx->newQuery('disBoardUserGroup');
$c->select(array(
    'disBoardUserGroup.*',
    'UserGroup.name',
));
$c->innerJoin('modUserGroup','UserGroup');
$c->where(array(
    'board' => $board->get('id'),
));
$c->sortby($modx->getSelectColumns('modUserGroup','UserGroup','',array('name')),'ASC');
$usergroups = $board->getMany('UserGroups',$c);
$list = array();
foreach ($usergroups as $usergroup) {
    $list[] = array(
        $usergroup->get('usergroup'),
        $usergroup->get('name'),
    );
}
$board->set('usergroups','('.$modx->toJSON($list).')');

/* output */
$boardArray = $board->toArray('',true);
return $modx->error->success('',$boardArray);
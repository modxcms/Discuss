<?php
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
$c->select(array(
    'disModerator.*',
    'User.username',
));
$c->innerJoin('modUser','User');
$c->where(array(
    'board' => $board->get('id'),
));
$c->sortby($modx->getSelectColumns('modUser','User','',array('username')),'ASC');
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
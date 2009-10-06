<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get board */
if (empty($_REQUEST['id'])) return $modx->error->failure($modx->lexicon('discuss.board_err_ns'));
$board = $modx->getObject('disBoard',$_REQUEST['id']);
if ($board == null) return $modx->error->failure($modx->lexicon('discuss.board_err_nf'));

/* get moderators */
$c = $modx->newQuery('disModerator');
$c->select('disModerator.*,User.username AS username');
$c->innerJoin('modUser','User');
$c->where(array(
    'board' => $board->get('id'),
));
$c->sortby('User.username','ASC');
$moderators = $board->getMany('Moderators',$c);
$mods = array();
foreach ($moderators as $moderator) {
    $mods[] = array(
        $moderator->get('user'),
        $moderator->get('username'),
    );
}
$board->set('moderators',$mods);

/* get user groups */
$c = $modx->newQuery('disBoardUserGroup');
$c->select('
    disBoardUserGroup.*,
    UserGroup.name AS name
');
$c->innerJoin('modUserGroup','UserGroup');
$c->where(array(
    'board' => $board->get('id'),
));
$c->sortby('UserGroup.name','ASC');
$usergroups = $board->getMany('UserGroups',$c);
$list = array();
foreach ($usergroups as $usergroup) {
    $list[] = array(
        $usergroup->get('usergroup'),
        $usergroup->get('name'),
    );
}
$board->set('usergroups',$list);

/* output */
$boardArray = $board->toArray('',true);
return $modx->error->success('',$boardArray);
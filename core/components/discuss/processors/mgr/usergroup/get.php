<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get user group */
if (empty($_REQUEST['id'])) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_ns'));
$c = $modx->newQuery('modUserGroup');
$c->select('
    modUserGroup.*,
    Profile.post_based AS post_based,
    Profile.min_posts AS min_posts,
    Profile.color AS color,
    Profile.image AS image
');
$c->leftJoin('disUserGroupProfile','Profile','modUserGroup.id = Profile.usergroup');
$c->where(array(
    'modUserGroup.id' => $_REQUEST['id'],
));
$usergroup = $modx->getObject('modUserGroup',$c);
if ($usergroup == null) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf'));

/* get members */
$c = $modx->newQuery('modUserGroupMember');
$c->select('modUserGroupMember.*,User.username AS username');
$c->leftJoin('modUser','User');
$c->where(array(
    'user_group' => $usergroup->get('id'),
));
$c->sortby('User.username','ASC');
$members = $modx->getCollection('modUserGroupMember',$c);
$list = array();
foreach ($members as $member) {
    $list[] = array(
        $member->get('member'),
        $member->get('username'),
        $member->get('role'),
    );
}
$usergroup->set('members',$list);
unset($members,$member,$list,$c);

/* get boards */
$c = $modx->newQuery('disBoard');
$c->select('
    disBoard.*,
    IF(UserGroups.usergroup = "'.$usergroup->get('id').'",1,0) AS access,
    MAX(Descendants.depth) AS depth
');
$c->innerJoin('disBoardClosure','Descendants');
$c->leftJoin('disBoardUserGroup','UserGroups');
$c->sortby('disBoard.category','ASC');
$c->sortby('disBoard.map','ASC');
$c->groupby('disBoard.id');
$boards = $modx->getCollection('disBoard',$c);
$list = array();
foreach ($boards as $board) {
    $pad =
    $list[] = array(
        $board->get('id'),
        str_repeat('--',$board->get('depth')).$board->get('name'),
        $board->get('access') ? true : false,
        $board->get('category'),
    );
}
$usergroup->set('boards',$list);
unset($boards,$board,$list,$c);


/* output */
$usergroupArray = $usergroup->toArray('',true);
return $modx->error->success('',$usergroupArray);
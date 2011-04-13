<?php
/**
 * Get users active in last X time
 */
$threshold = $modx->getOption('discuss.user_active_threshold',null,40);
$timeAgo = time() - (60*($threshold));
$c = $modx->newQuery('modUser');
$c->select(array(
    'modUser.*',
    'UserGroupProfile.color',
));
$c->innerJoin('disSession','Session',$modx->getSelectColumns('disSession','Session','',array('user')).' = '.$modx->getSelectColumns('modUser','modUser','',array('id')));
$c->leftJoin('modUserGroupMember','UserGroupMembers');
$c->leftJoin('modUserGroup','UserGroup','UserGroup.id = UserGroupMembers.user_group');
$c->leftJoin('disUserGroupProfile','UserGroupProfile','UserGroupProfile.usergroup = UserGroup.id AND UserGroupProfile.color != ""');
$c->where(array(
    'Session.access:>=' => $timeAgo,
));
$c->sortby('UserGroupProfile.color','ASC');
$c->sortby('Session.access','ASC');
$activeUsers = $modx->getCollection('modUser',$c);
$as = '';
foreach ($activeUsers as $activeUser) {
    $as .= $discuss->getChunk('disActiveUserRow',$activeUser->toArray());
}
$list = $modx->lexicon('discuss.users_active_in_last',array(
    'users' => trim($as,','),
    'threshold' => $threshold,
));
unset($as,$activeUsers,$activeUser,$timeago,$threshold);

return $list;
<?php
/**
 * Get users active in last X time
 */
/* setup defaults/permissions */
$threshold = $modx->getOption('discuss.user_active_threshold',null,40);
$timeAgo = time() - (60*($threshold));
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

/* build query */
$c = $modx->newQuery('disUser');
$c->innerJoin('disSession','Session',$modx->getSelectColumns('disSession','Session','',array('user')).' = '.$modx->getSelectColumns('disUser','disUser','',array('id')));
$c->innerJoin('modUser','User');
$c->leftJoin('modUserGroupMember','UserGroupMembers','User.id = UserGroupMembers.member');
$c->leftJoin('modUserGroup','UserGroup','UserGroup.id = UserGroupMembers.user_group');
$c->leftJoin('disUserGroupProfile','UserGroupProfile','UserGroupProfile.usergroup = UserGroup.id AND UserGroupProfile.color != ""');
$c->where(array(
    'Session.access:>=' => $timeAgo,
));
$c->sortby('UserGroupProfile.color','ASC');
$c->sortby('Session.access','ASC');
$activeUsers = $modx->getCollection('disUser',$c);

/* iterate */
$as = array();
foreach ($activeUsers as $activeUser) {
    if ($canViewProfiles) {
        $as[] = $discuss->getChunk('user/disActiveUserRow',$activeUser->toArray());
    } else {
        $as[] = $activeUser->get('username');
    }
}

/* parse into lexicon */
$list = $modx->lexicon('discuss.users_active_in_last',array(
    'users' => implode(',',$as),
    'threshold' => $threshold,
));
return $list;
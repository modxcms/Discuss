<?php
/**
 * Get users active in last X time
 */
/* setup defaults/permissions */
$threshold = $modx->getOption('discuss.user_active_threshold',null,40);
$timeAgo = time() - (60*($threshold));
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

/* build query */
$activeUsers = $modx->call('disUser','fetchActive',array(&$modx,$timeAgo));

/* iterate */
$as = array();
foreach ($activeUsers['results'] as $activeUser) {
    $activeUserArray = $activeUser->toArray();
    $activeUserArray['style'] = '';
    if (!empty($activeUserArray['color'])) {
        $activeUserArray['style'] .= 'color: '.$activeUserArray['color'].';';
    }
    if ($canViewProfiles) {
        $as[] = $discuss->getChunk('user/disActiveUserRow',$activeUserArray);
    } else {
        $as[] = $activeUser->get('username');
    }
}

/* parse into lexicon */
$list = $modx->lexicon('discuss.users_active_in_last',array(
    'users' => implode(',',$as),
    'total' => $activeUsers['total'],
    'threshold' => $threshold,
));
return $list;
<?php
/**
 * Handle post-login data manipulation
 */
$discuss =& $modx->discuss;
$modx->lexicon->load('discuss:user');

/* get modUser object */
$user = $modx->getObject('modUser',array(
    'username' => $fields['username'],
));
if (empty($user)) {
    $hook->addError('username',$modx->lexicon('discuss.user_err_nf'));
    return false;
}

/* attempt to get disUser related object */
$c = $modx->newQuery('disUser');
$c->where(array(
    'user' => $user->get('id'),
));
$disUser = $modx->getObject('disUser',$c);
if (empty($disUser)) {
    /* ok, we need to create a parallel disUser obj since none exists */
    $profile = $user->getOne('Profile');

    $disUser = $modx->newObject('disUser');
    $disUser->fromArray(array(
        'user' => $user->get('id'),
        'username' => $user->get('username'),
        'password' => $user->get('password'),
        'salt' => $user->get('salt'),
        'synced' => true,
        'syncedat' => date('Y-m-d H:I:S'),
        'confirmed' => true,
        'confirmedon' => date('Y-m-d H:I:S'),
        'source' => 'internal',
    ));
    if ($profile) {
        $disUser->fromArray($profile->toArray());
        $name = $profile->get('fullname');
        $name = explode(' ',$name);
        $disUser->fromArray(array(
            'name_first' => $name[0],
            'name_last' => isset($name[1]) ? $name[1] : '',
        ));
    }
    $disUser->save();
}

/* remove old session to prevent duplicates */
$oldSessionId = session_id();
$session = $modx->removeObject('disSession',array('id' => $oldSessionId));


/* update profile with activity/last login dates and IP */
$disUser->set('last_login',strftime('%Y-%m-%d %H:%M:%S'));
$disUser->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
$disUser->set('ip',$discuss->getIp());
$disUser->save();

return true;
<?php

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
$profile = $modx->getObject('disUser',$c);
if (empty($profile)) {
    /* if no disUser, redirect, we'll add it post-login */
    return true;
}

/* ok, we have a disUser, now check for banned/deactive status */
$ok = false;
$errorOutput = '';
$status = $profile->get('status');
switch ($status) {
    case disUser::ACTIVE: $ok = true; break;
    case disUser::BANNED:
        $errorOutput = $modx->lexicon('discuss.account_banned');
        break;
    case disUser::INACTIVE:
        $errorOutput = $modx->lexicon('discuss.account_deactivated');
        break;
    case disUser::UNCONFIRMED:
        $errorOutput = $modx->lexicon('discuss.account_unconfirmed');
        break;
    case disUser::AWAITING_MODERATION:
        $errorOutput = $modx->lexicon('discuss.account_awaiting_moderation');
        break;
    default:
        $errorOutput = $modx->lexicon('discuss.account_nonexistent');
        break;
}
if (!$ok) {
    $hook->addError($errorOutput);
    return false;
}
return true;
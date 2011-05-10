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
 * - Check to see if disUser obj exists for username
 * - If so, check to see if related MODX user exists
 * --- If so, check source of disUser
 * --- If source == smf, try to auth with password
 * ---- If auth is success, update modUser obj with proper password in MODX hash
 * ---- If auth is fail, return fail
 * --- If source == internal, ignore this and move on
 * - If no disUser, return true and we'll add post-login
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('core:login','discuss:web');

/* only run on login */
if ($scriptProperties['mode'] != 'login') {
    return true;
}

/* get discuss User */
$c = $modx->newQuery('disUser');
$c->where(array(
    'username' => $fields['username'],
));
$disUser = $modx->getObject('disUser',$c);

if ($disUser) {
    $user = $disUser->getOne('User');
    if (!$user) {
        /* no related disUser, add it post-login, is import err */
        return true;
    }

    if (!$disUser->get('synced') && $user->get('password') != '') {
        /* sync user passwords for imported users */
        switch ($disUser->get('source')) {
            case 'smf':
                $smfHashedPassword = @sha1(strtolower($fields['username']) . $fields['password']);
                if ($disUser->get('password') == $smfHashedPassword) {
                    $user->set('password',$fields['password']);
                    $user->save();
                    if ($user instanceof modPtaUser) {
                        $user->syncPassword($fields['password']);
                    }
                    $disUser->set('synced',true);
                    $disUser->set('syncedat',$discuss->now());
                    $disUser->save();
                } else {
                    $hook->addError('password',$modx->lexicon('discuss.login_err'));
                    return false;
                }
                break;
            default:

                break;
        }
    }
}

/* get modUser object */
if (empty($user)) {
    $user = $modx->getObject('modUser',array(
        'username' => $fields['username'],
    ));
}
if (empty($user)) {
    $hook->addError('username',$modx->lexicon('login_username_password_incorrect'));
    return false;
}
if (empty($disUser)) {
    /* if no disUser, redirect, we'll add it post-login */
    return true;
}
/* ok, we have a disUser, now check for banned/deactive status */
$ok = false;
$errorOutput = '';
$status = $disUser->get('status');
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
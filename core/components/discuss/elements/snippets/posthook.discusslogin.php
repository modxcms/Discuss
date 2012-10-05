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
 * Handle post-login data manipulation
 *
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $scriptProperties
 * @var fiHooks $hook
 * 
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('discuss:user','core:login');

if (empty($fields['username'])) {
    $hook->addError('username',$modx->lexicon('login_username_password_incorrect'));
    return false;
}

/* @var modUser $user get modUser object */
$user = $modx->getObject('modUser',array(
    'username' => $fields['username'],
));
if (empty($user)) {
    $hook->addError('username',$modx->lexicon('login_username_password_incorrect'));
    return false;
}

/* attempt to get disUser related object */
$c = $modx->newQuery('disUser');
$c->where(array(
    'user' => $user->get('id'),
));
/** @var disUser $disUser */
$disUser = $modx->getObject('disUser',$c);
if (empty($disUser)) {
    /* ok, we need to create a parallel disUser obj since none exists */
    /** @var modUserProfile $profile */
    $profile = $user->getOne('Profile');

    $disUser = $modx->newObject('disUser');
    $disUser->fromArray(array(
        'user' => $user->get('id'),
        'username' => $user->get('username'),
        'password' => $user->get('password'),
        'salt' => $user->get('salt'),
        'confirmed' => true,
        'confirmedon' => date('Y-m-d H:I:S'),
        'createdon' => date('Y-m-d H:I:S'),
        'source' => 'internal',
        'status' => disUser::ACTIVE,
    ));
    if ($profile) {
        $disUser->fromArray($profile->toArray());
        $disUser->set('birthdate',strftime($discuss->dateFormat,$profile->get('dob')));
        $disUser->set('gender',$profile->get('gender') == 2 ? 'f' : 'm');
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
/** @var disSession $session */
$session = $modx->getObject('disSession',array('id' => $oldSessionId));
if ($session) {
    $session->remove();
}


/* update profile with activity/last login dates and IP */
$disUser->set('last_login',strftime('%Y-%m-%d %H:%M:%S'));
$disUser->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
$disUser->set('ip',$discuss->getIp());
$disUser->save();

/* Parse the discussPlace value (if set) to redirect user back to Discuss. */
$discussPlace = $hook->getValue('discussPlace');
if (!empty($discussPlace)) {
    $discussPlace = explode(':', $discussPlace);
    $params = array();
    if (isset($discussPlace[1])) {
        if (strpos($discussPlace[1],'=') !== false) {
            $urlparam = explode('=', $discussPlace[1]);
            $params = array($urlparam[0] => $urlparam[1]);
        } else {
            $params = array($discussPlace[0] => $discussPlace[1]);
        }
    }
    if (isset($discussPlace[2]) && ($discussPlace[2] > 1)) {
        $params['page'] = $discussPlace[2];
    }

    /* Load the request handler, set the URL (cause we're out of Discuss here) and redirect back. */
    $discuss->loadRequest();
    $discuss->url = $modx->makeUrl($modx->getOption('discuss.forums_resource_id'));
    $url = $discuss->request->makeUrl($discussPlace[0],$params); /* TODO - TO FIX TO THE NEW MAKEURL SYNTAX! */
    if (!empty($url)) {
        $modx->sendRedirect($url);
    }
}

return true;

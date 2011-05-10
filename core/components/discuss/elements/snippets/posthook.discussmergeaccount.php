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
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;
$modx->lexicon->load('discuss:user');

if (empty($fields['password'])) {
    $hook->addError('password',$modx->lexicon('discuss.user_err_password'));
}
if (empty($fields['username'])) {
    $hook->addError('username',$modx->lexicon('discuss.user_err_username'));
} else {

    if ($fields['username'] == $modx->user->get('username')) {
        $hook->addError('username',$modx->lexicon('discuss.user_err_merge_same'));
    } else {
        $user = $modx->getObject('disUser',array(
            'username' => $fields['username'],
        ));
        if (empty($user)) {
            $hook->addError('username',$modx->lexicon('discuss.user_err_nf'));
        } else {
            $pw = $user->get('password');
            switch ($user->get('source')) {
                case 'smf':
                    $userPassword = @sha1(strtolower($fields['username']) . $fields['password']);
                    if ($userPassword != $pw) {
                        $hook->addError('password',$modx->lexicon('discuss.user_err_password_incorrect'));
                    }
                    break;
                break;
            }
        }
    }
}

if (!empty($hook->errors)) {
    return false;
}

if (!$discuss->user->merge($user)) {
    $hook->addError('username','An error occurred while trying to merge the user.');
}

return true;
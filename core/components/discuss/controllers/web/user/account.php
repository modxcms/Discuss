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
 *
 * @deprecated true
 * @package discuss
 */
if (empty($scriptProperties['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$scriptProperties['user']);
if ($user == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.user_account_header',array('user' => $user->get('username'))));


if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}

$modx->lexicon->load('discuss:user');

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');

/* save form fields */
if (!empty($_POST)) {
    if (empty($_POST['password']) || $user->get('password') !== md5($_POST['password'])) {
        $errors['password'] = $modx->lexicon('discuss.user_err_password_incorrect');
    }

    if (empty($_POST['username'])) $errors['username'] = $modx->lexicon('discuss.user_err_username');
    if (!empty($_POST['password_new'])) {
        if (empty($_POST['password_new'])) $errors['password_new'] = $modx->lexicon('discuss.user_err_password');
        if (empty($_POST['password_confirm'])) $errors['password_confirm'] = $modx->lexicon('discuss.user_err_password_confirm');
        if ($_POST['password_new'] != $_POST['password_confirm']) $errors['password_confirm'] = $modx->lexicon('discuss.user_err_password_match');
    }

    if (empty($_POST['show_email'])) { $_POST['show_email'] = 0; }
    if (empty($_POST['show_online'])) { $_POST['show_online'] = 0; }

    if (empty($errors)) {
        $user->set('username',$_POST['username']);
        if (!empty($_POST['password'])) {
            $user->changePassword($_POST['password_current'],$_POST['password']);
        }
        $user->fromArray($_POST);
        $user->save();
    }
    $modx->toPlaceholders($errors,'error');
}

$placeholders = $user->toArray();

/* setup genders */
$genders = array('' => '','m' => $modx->lexicon('discuss.male'),'f' => $modx->lexicon('discuss.female'));
$gs = '';
foreach ($genders as $v => $d) {
    $gs .= '<option value="'.$v.'"'
        .($user->get('gender') == $v ? ' selected="selected"' : '')
        .'>'.$d.'</option>';
}
$placeholders['genders'] = $gs;
unset($genders,$gs,$v,$d);

/* format checkbox settings */
if (!empty($placeholders['show_email'])) { $placeholders['show_email'] = ' checked="checked"'; }
if (!empty($placeholders['show_online'])) { $placeholders['show_online'] = ' checked="checked"'; }

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $placeholders;
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
 * The edit user page
 *
 * @package discuss
 */
$modx->lexicon->load('discuss:user');

if (!$discuss->user->isLoggedIn || !$discuss->user->isAdmin()) {
    $discuss->sendUnauthorizedPage();
}

/* get user */
if (empty($scriptProperties['id'])) { $modx->sendErrorPage(); }
$c = array();
$c[!empty($scriptProperties['i']) ? 'integrated_id' : 'id'] = $scriptProperties['id'];
$user = $modx->getObject('disUser',$c);
if ($user == null) { $modx->sendErrorPage(); }

$modxUser = $user->getOne('User');

/* get user */
$discuss->setPageTitle($modx->lexicon('discuss.ban_user_header',array('username' => $user->get('username'))));

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');

$placeholders = $user->toArray('fi.');

if (!empty($scriptProperties['success'])) {
    $placeholders['fi.successMessage'] = $modx->lexicon('discuss.ban_added_msg');
}

if (empty($_POST)) {
    $placeholders['fi.expireson'] = 30;
    $placeholders['fi.ip_range'] = $placeholders['fi.ip'];
    $placeholders['fi.hostname'] = gethostbyaddr($placeholders['fi.ip']);
}
$placeholders['other_fields'] = '';

/* fire OnDiscussBanUser */
$modx->invokeEvent('OnDiscussBeforeBanUser',array(
    'user' => &$user,
    'modUser' => &$modxUser,
    'placeholders' => &$placeholders,
));

/* do output */
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,array('id' => $placeholders['fi.id'],'username' => $placeholders['fi.username']));
$modx->setPlaceholder('discuss.user',$discuss->user->get('username'));

return $placeholders;
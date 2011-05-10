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
 * User statistics page
 *
 * @package discuss
 */
$modx->lexicon->load('discuss:user');

if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}

/* get user */
if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.user_statistics_header',array('user' => $user->get('username'))));

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');

$placeholders = $user->toArray();

/* # of topics started */
$placeholders['topics'] = $modx->getCount('disThread',array(
    'author_first' => $user->get('id'),
));
$placeholders['topics'] = number_format($placeholders['topics']);

/* # of replies to topics */
$placeholders['replies'] = $modx->getCount('disPost',array(
    'author' => $user->get('id'),
    'parent:!=' => 0,
));
$placeholders['replies'] = number_format($placeholders['replies']);

/* # of total posts */
$placeholders['posts'] = number_format($placeholders['posts']);

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $placeholders;
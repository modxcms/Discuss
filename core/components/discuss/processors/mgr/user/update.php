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
 * Update a User
 * 
 * @package discuss
 */
/* get user */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.user_err_ns'));
$c = $modx->newQuery('disUser');
$c->innerJoin('modUser','User');
$c->where(array(
    'id' => $scriptProperties['id'],
));
$user = $modx->getObject('disUser',$c);
if (!$user) return $modx->error->failure($modx->lexicon('discuss.user_err_nf',array('id' => $scriptProperties['id'])));

/* set fields */
unset($scriptProperties['id']);
$scriptProperties['show_email'] = !empty($scriptProperties['show_email']) ? true : false;
$scriptProperties['show_online'] = !empty($scriptProperties['show_online']) ? true : false;
$user->fromArray($scriptProperties);

/* save user */
if (!$user->save()) {
    return $modx->error->failure($modx->lexicon('discuss.user_err_save'));
}

/* save username if changed */
$modxUser = $user->getOne('User');
if ($modxUser) {
    $modxUser->fromArray($scriptProperties);
    $modxUser->save();
}

$ra = $user->toArray();
unset($ra['password'],$ra['cachepwd']);
return $modx->error->success('',$ra);
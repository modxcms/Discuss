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
 * @package discuss
 * @subpackage processors
 */
/* get user */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.user_err_ns'));
$c = $modx->newQuery('disUser');
$c->innerJoin('modUser','User');
$c->select($modx->getSelectColumns('disUser','disUser'));
$c->select(array(
    'User.username',
));
$c->where(array(
    'id' => $scriptProperties['id'],
));
$user = $modx->getObject('disUser',$c);
if (!$user) return $modx->error->failure($modx->lexicon('discuss.user_err_nf'));

$userArray = $user->toArray('',true);
unset($userArray['password'],$userArray['cachepwd']);

$userArray['createdon'] = $modx->discuss->formatDate($userArray['createdon']);
$userArray['last_login'] = $modx->discuss->formatDate($userArray['last_login']);
$userArray['last_active'] = $modx->discuss->formatDate($userArray['last_active']);
$userArray['syncedat'] = $modx->discuss->formatDate($userArray['syncedat']);

return $modx->error->success('',$userArray);
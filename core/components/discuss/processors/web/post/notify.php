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
$thread = $modx->getObject('disPost',$scriptProperties['id']);
if ($thread == null) return $modx->error->failure();

$userId = $modx->user->get('id');
if (empty($userId)) return $modx->error->failure();

$notify = $modx->newObject('disUserNotification');
$notify->set('user',$modx->user->get('id'));
$notify->set('post',$thread->get('id'));
if (!$notify->save()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not create notification: '.print_r($notify->toArray(),true));
}


return $modx->error->success();
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
 * The default Permission scheme for the DiscussTemplate ACL Policy Template.
 *
 * @package discuss
 * @subpackage build
 */
$permissions = array();
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.pm_remove',
    'description' => 'discuss.perm.pm_remove',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.pm_send',
    'description' => 'discuss.perm.pm_send',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.pm_view',
    'description' => 'discuss.perm.pm_view',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.search',
    'description' => 'discuss.perm.search',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_attach',
    'description' => 'discuss.perm.thread_attach',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_create',
    'description' => 'discuss.perm.thread_create',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_lock',
    'description' => 'discuss.perm.thread_lock',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_merge',
    'description' => 'discuss.perm.thread_merge',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_modify',
    'description' => 'discuss.perm.thread_modify',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_move',
    'description' => 'discuss.perm.thread_move',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_print',
    'description' => 'discuss.perm.thread_print',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_remove',
    'description' => 'discuss.perm.thread_remove',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_reply',
    'description' => 'discuss.perm.thread_reply',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_report',
    'description' => 'discuss.perm.thread_report',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_send',
    'description' => 'discuss.perm.thread_send',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_split',
    'description' => 'discuss.perm.thread_split',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_stick',
    'description' => 'discuss.perm.thread_stick',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_subscribe',
    'description' => 'discuss.perm.thread_subscribe',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_unlock',
    'description' => 'discuss.perm.thread_unlock',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.thread_unstick',
    'description' => 'discuss.perm.thread_unstick',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.track_ip',
    'description' => 'discuss.perm.track_ip',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.view_attachments',
    'description' => 'discuss.perm.view_attachments',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.view_emails',
    'description' => 'discuss.perm.view_emails',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.view_memberlist',
    'description' => 'discuss.perm.view_memberlist',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.view_online',
    'description' => 'discuss.perm.view_online',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.view_profiles',
    'description' => 'discuss.perm.view_profiles',
    'value' => true,
));
$permissions[] = $modx->newObject('modAccessPermission',array(
    'name' => 'discuss.view_statistics',
    'description' => 'discuss.perm.view_statistics',
    'value' => true,
));

return $permissions;
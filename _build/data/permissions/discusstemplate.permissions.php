<?php
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
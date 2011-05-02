<?php
/**
 * Loads system settings
 *
 * @package discuss
 * @subpackage build
 */
$settings = array();

$settings['discuss.admin_email']= $modx->newObject('modSystemSetting');
$settings['discuss.admin_email']->fromArray(array(
    'key' => 'discuss.admin_email',
    'value' => 'forums@mydomain.com',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Administration',
),'',true,true);

$settings['discuss.admin_groups']= $modx->newObject('modSystemSetting');
$settings['discuss.admin_groups']->fromArray(array(
    'key' => 'discuss.admin_groups',
    'value' => 'Administrator,Forum Administrator',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Administration',
),'',true,true);

$settings['discuss.allow_custom_titles']= $modx->newObject('modSystemSetting');
$settings['discuss.allow_custom_titles']->fromArray(array(
    'key' => 'discuss.allow_custom_titles',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'User Profiles',
),'',true,true);

$settings['discuss.allow_guests']= $modx->newObject('modSystemSetting');
$settings['discuss.allow_guests']->fromArray(array(
    'key' => 'discuss.allow_guests',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.attachments_allowed_filetypes']= $modx->newObject('modSystemSetting');
$settings['discuss.attachments_allowed_filetypes']->fromArray(array(
    'key' => 'discuss.attachments_allowed_filetypes',
    'value' => 'doc,gif,jpg,pdf,png,txt,zip,gz,bz2,xls,psd,css,tgz,odt,sql,tpl,rtf,xml',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Attachments',
),'',true,true);

$settings['discuss.attachments_max_filesize']= $modx->newObject('modSystemSetting');
$settings['discuss.attachments_max_filesize']->fromArray(array(
    'key' => 'discuss.attachments_max_filesize',
    'value' => '11509760',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Attachments',
),'',true,true);

$settings['discuss.attachments_max_per_post']= $modx->newObject('modSystemSetting');
$settings['discuss.attachments_max_per_post']->fromArray(array(
    'key' => 'discuss.attachments_max_per_post',
    'value' => '5',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Attachments',
),'',true,true);

$settings['discuss.attachments_path']= $modx->newObject('modSystemSetting');
$settings['discuss.attachments_path']->fromArray(array(
    'key' => 'discuss.attachments_path',
    'value' => '{assets_path}components/discuss/attachments/',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Attachments',
),'',true,true);

$settings['discuss.attachments_url']= $modx->newObject('modSystemSetting');
$settings['discuss.attachments_url']->fromArray(array(
    'key' => 'discuss.attachments_url',
    'value' => '{assets_url}components/discuss/attachments/',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Attachments',
),'',true,true);

$settings['discuss.bbcode_enabled']= $modx->newObject('modSystemSetting');
$settings['discuss.bbcode_enabled']->fromArray(array(
    'key' => 'discuss.bbcode_enabled',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.email_reported_post_subject']= $modx->newObject('modSystemSetting');
$settings['discuss.email_reported_post_subject']->fromArray(array(
    'key' => 'discuss.email_reported_post_subject',
    'value' => 'Reported Post: [[+title]]',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.email_reported_post_chunk']= $modx->newObject('modSystemSetting');
$settings['discuss.email_reported_post_chunk']->fromArray(array(
    'key' => 'discuss.email_reported_post_chunk',
    'value' => 'emails/disReportedEmail',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.theme']= $modx->newObject('modSystemSetting');
$settings['discuss.theme']->fromArray(array(
    'key' => 'discuss.theme',
    'value' => 'default',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Administration',
),'',true,true);

$settings['discuss.post_per_page']= $modx->newObject('modSystemSetting');
$settings['discuss.post_per_page']->fromArray(array(
    'key' => 'discuss.post_per_page',
    'value' => 10,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.use_custom_post_parser']= $modx->newObject('modSystemSetting');
$settings['discuss.use_custom_post_parser']->fromArray(array(
    'key' => 'discuss.use_custom_post_parser',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.courtesy_edit_wait']= $modx->newObject('modSystemSetting');
$settings['discuss.courtesy_edit_wait']->fromArray(array(
    'key' => 'discuss.courtesy_edit_wait',
    'value' => '60',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.date_format']= $modx->newObject('modSystemSetting');
$settings['discuss.date_format']->fromArray(array(
    'key' => 'discuss.date_format',
    'value' => '%b %d, %Y, %I:%M %p',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Administration',
),'',true,true);

$settings['discuss.debug']= $modx->newObject('modSystemSetting');
$settings['discuss.debug']->fromArray(array(
    'key' => 'discuss.debug',
    'value' => '0',
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => '',
),'',true,true);

$settings['discuss.enable_hot']= $modx->newObject('modSystemSetting');
$settings['discuss.enable_hot']->fromArray(array(
    'key' => 'discuss.enable_hot',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.enable_sticky']= $modx->newObject('modSystemSetting');
$settings['discuss.enable_sticky']->fromArray(array(
    'key' => 'discuss.enable_sticky',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.forum_title']= $modx->newObject('modSystemSetting');
$settings['discuss.forum_title']->fromArray(array(
    'key' => 'discuss.forum_title',
    'value' => 'My Forums',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.global_moderators']= $modx->newObject('modSystemSetting');
$settings['discuss.global_moderators']->fromArray(array(
    'key' => 'discuss.global_moderators',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.hot_thread_threshold']= $modx->newObject('modSystemSetting');
$settings['discuss.hot_thread_threshold']->fromArray(array(
    'key' => 'discuss.hot_thread_threshold',
    'value' => 10,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.max_post_depth']= $modx->newObject('modSystemSetting');
$settings['discuss.max_post_depth']->fromArray(array(
    'key' => 'discuss.max_post_depth',
    'value' => '3',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.max_signature_length']= $modx->newObject('modSystemSetting');
$settings['discuss.max_signature_length']->fromArray(array(
    'key' => 'discuss.max_signature_length',
    'value' => '2000',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'User Profiles',
),'',true,true);

$settings['discuss.maximum_post_size']= $modx->newObject('modSystemSetting');
$settings['discuss.maximum_post_size']->fromArray(array(
    'key' => 'discuss.maximum_post_size',
    'value' => '30000',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.notification_new_post_subject']= $modx->newObject('modSystemSetting');
$settings['discuss.notification_new_post_subject']->fromArray(array(
    'key' => 'discuss.notification_new_post_subject',
    'value' => '[Discuss] [[%discuss.new_post_made]]',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Notifications',
),'',true,true);

$settings['discuss.notification_new_post_chunk']= $modx->newObject('modSystemSetting');
$settings['discuss.notification_new_post_chunk']->fromArray(array(
    'key' => 'discuss.notification_new_post_chunk',
    'value' => 'disNotificationEmail',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Notifications',
),'',true,true);

$settings['discuss.num_recent_posts']= $modx->newObject('modSystemSetting');
$settings['discuss.num_recent_posts']->fromArray(array(
    'key' => 'discuss.num_recent_posts',
    'value' => 10,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.page_param']= $modx->newObject('modSystemSetting');
$settings['discuss.page_param']->fromArray(array(
    'key' => 'discuss.page_param',
    'value' => 'page',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Security',
),'',true,true);

$settings['discuss.recycle_bin_board']= $modx->newObject('modSystemSetting');
$settings['discuss.recycle_bin_board']->fromArray(array(
    'key' => 'discuss.recycle_bin_board',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.reserved_usernames']= $modx->newObject('modSystemSetting');
$settings['discuss.reserved_usernames']->fromArray(array(
    'key' => 'discuss.reserved_usernames',
    'value' => 'admin,abc,administrator,superuser,root',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Security',
),'',true,true);

$settings['discuss.show_whos_online']= $modx->newObject('modSystemSetting');
$settings['discuss.show_whos_online']->fromArray(array(
    'key' => 'discuss.show_whos_online',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.spam_bucket_board']= $modx->newObject('modSystemSetting');
$settings['discuss.spam_bucket_board']->fromArray(array(
    'key' => 'discuss.spam_bucket_board',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.stats_enabled']= $modx->newObject('modSystemSetting');
$settings['discuss.stats_enabled']->fromArray(array(
    'key' => 'discuss.stats_enabled',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Administration',
),'',true,true);

$settings['discuss.threads_per_page']= $modx->newObject('modSystemSetting');
$settings['discuss.threads_per_page']->fromArray(array(
    'key' => 'discuss.threads_per_page',
    'value' => 20,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.use_css']= $modx->newObject('modSystemSetting');
$settings['discuss.use_css']->fromArray(array(
    'key' => 'discuss.use_css',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.use_stopforumspam']= $modx->newObject('modSystemSetting');
$settings['discuss.use_stopforumspam']->fromArray(array(
    'key' => 'discuss.use_stopforumspam',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

$settings['discuss.user_active_threshold']= $modx->newObject('modSystemSetting');
$settings['discuss.user_active_threshold']->fromArray(array(
    'key' => 'discuss.user_active_threshold',
    'value' => 40,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'General',
),'',true,true);

/* SSO Settings */
$settings['discuss.login_resource_id']= $modx->newObject('modSystemSetting');
$settings['discuss.login_resource_id']->fromArray(array(
    'key' => 'discuss.login_resource_id',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'SSO',
),'',true,true);

$settings['discuss.register_resource_id']= $modx->newObject('modSystemSetting');
$settings['discuss.register_resource_id']->fromArray(array(
    'key' => 'discuss.register_resource_id',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'SSO',
),'',true,true);

$settings['discuss.update_profile_resource_id']= $modx->newObject('modSystemSetting');
$settings['discuss.update_profile_resource_id']->fromArray(array(
    'key' => 'discuss.update_profile_resource_id',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'SSO',
),'',true,true);

$settings['discuss.forums_resource_id']= $modx->newObject('modSystemSetting');
$settings['discuss.forums_resource_id']->fromArray(array(
    'key' => 'discuss.forums_resource_id',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'SSO',
),'',true,true);

$settings['discuss.sso_mode']= $modx->newObject('modSystemSetting');
$settings['discuss.sso_mode']->fromArray(array(
    'key' => 'discuss.sso_mode',
    'value' => 1,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'SSO',
),'',true,true);

/*
$settings['discuss.']= $modx->newObject('modSystemSetting');
$settings['discuss.']->fromArray(array(
    'key' => 'discuss.',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => '',
),'',true,true);
*/

return $settings;
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

$settings['discuss.archive_threads_after']= $modx->newObject('modSystemSetting');
$settings['discuss.archive_threads_after']->fromArray(array(
    'key' => 'discuss.archive_threads_after',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Threads',
),'',true,true);

$settings['discuss.attachments_allowed_filetypes']= $modx->newObject('modSystemSetting');
$settings['discuss.attachments_allowed_filetypes']->fromArray(array(
    'key' => 'discuss.attachments_allowed_filetypes',
    'value' => 'doc,gif,jpg,pdf,png,txt,zip,gz,bz2,xls,psd,css,tgz,odt,sql,tpl,rtf,xml,docx',
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

$settings['discuss.bad_words']= $modx->newObject('modSystemSetting');
$settings['discuss.bad_words']->fromArray(array(
    'key' => 'discuss.bad_words',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.bad_words_replace']= $modx->newObject('modSystemSetting');
$settings['discuss.bad_words_replace']->fromArray(array(
    'key' => 'discuss.bad_words_replace',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.bad_words_replace_string']= $modx->newObject('modSystemSetting');
$settings['discuss.bad_words_replace_string']->fromArray(array(
    'key' => 'discuss.bad_words_replace_string',
    'value' => '****',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.bbcode_enabled']= $modx->newObject('modSystemSetting');
$settings['discuss.bbcode_enabled']->fromArray(array(
    'key' => 'discuss.bbcode_enabled',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Threads',
),'',true,true);

$settings['discuss.default_board_moderators']= $modx->newObject('modSystemSetting');
$settings['discuss.default_board_moderators']->fromArray(array(
    'key' => 'discuss.default_board_moderators',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
),'',true,true);

$settings['discuss.default_board_usergroups']= $modx->newObject('modSystemSetting');
$settings['discuss.default_board_usergroups']->fromArray(array(
    'key' => 'discuss.default_board_usergroups',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Security',
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
    'area' => 'General',
),'',true,true);

$settings['discuss.use_custom_post_parser']= $modx->newObject('modSystemSetting');
$settings['discuss.use_custom_post_parser']->fromArray(array(
    'key' => 'discuss.use_custom_post_parser',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Threads',
),'',true,true);

$settings['discuss.courtesy_edit_wait']= $modx->newObject('modSystemSetting');
$settings['discuss.courtesy_edit_wait']->fromArray(array(
    'key' => 'discuss.courtesy_edit_wait',
    'value' => '60',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posting',
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
$settings['discuss.debug_templates']= $modx->newObject('modSystemSetting');
$settings['discuss.debug_templates']->fromArray(array(
    'key' => 'discuss.debug_templates',
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
    'area' => 'Threads',
),'',true,true);

$settings['discuss.enable_notifications']= $modx->newObject('modSystemSetting');
$settings['discuss.enable_notifications']->fromArray(array(
    'key' => 'discuss.enable_notifications',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Notifications',
),'',true,true);

$settings['discuss.enable_sticky']= $modx->newObject('modSystemSetting');
$settings['discuss.enable_sticky']->fromArray(array(
    'key' => 'discuss.enable_sticky',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Threads',
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
    'area' => 'Threads',
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
    'area' => 'Threads',
),'',true,true);

$settings['discuss.new_replies_threshold']= $modx->newObject('modSystemSetting');
$settings['discuss.new_replies_threshold']->fromArray(array(
    'key' => 'discuss.new_replies_threshold',
    'value' => 14,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Threads',
),'',true,true);
$settings['discuss.unanswered_questions_threshold']= $modx->newObject('modSystemSetting');
$settings['discuss.unanswered_questions_threshold']->fromArray(array(
    'key' => 'discuss.unanswered_questions_threshold',
    'value' => 90,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Threads',
),'',true,true);
$settings['discuss.no_replies_threshold']= $modx->newObject('modSystemSetting');
$settings['discuss.no_replies_threshold']->fromArray(array(
    'key' => 'discuss.no_replies_threshold',
    'value' => 90,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Threads',
),'',true,true);
$settings['discuss.recent_threshold_days']= $modx->newObject('modSystemSetting');
$settings['discuss.recent_threshold_days']->fromArray(array(
    'key' => 'discuss.recent_threshold_days',
    'value' => 42,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Threads',
),'',true,true);

$settings['discuss.notification_new_post_subject']= $modx->newObject('modSystemSetting');
$settings['discuss.notification_new_post_subject']->fromArray(array(
    'key' => 'discuss.notification_new_post_subject',
    'value' => '[Discuss] New Reply: [[+title]]',
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
    'area' => 'General',
),'',true,true);

$settings['discuss.parser_class']= $modx->newObject('modSystemSetting');
$settings['discuss.parser_class']->fromArray(array(
    'key' => 'discuss.parser_class',
    'value' => 'disBBCodeParser',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.parser_class_path']= $modx->newObject('modSystemSetting');
$settings['discuss.parser_class_path']->fromArray(array(
    'key' => 'discuss.parser_class_path',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.post_sort_dir']= $modx->newObject('modSystemSetting');
$settings['discuss.post_sort_dir']->fromArray(array(
    'key' => 'discuss.post_sort_dir',
    'value' => 'ASC',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);

$settings['discuss.recycle_bin_board']= $modx->newObject('modSystemSetting');
$settings['discuss.recycle_bin_board']->fromArray(array(
    'key' => 'discuss.recycle_bin_board',
    'value' => 0,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Moderation',
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

/* Gravatar Settings */
$settings['discuss.gravatar_url']= $modx->newObject('modSystemSetting');
$settings['discuss.gravatar_url']->fromArray(array(
    'key' => 'discuss.gravatar_url',
    'value' => 'http://www.gravatar.com/avatar/',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Gravatar',
),'',true,true);

$settings['discuss.gravatar_default']= $modx->newObject('modSystemSetting');
$settings['discuss.gravatar_default']->fromArray(array(
    'key' => 'discuss.gravatar_default',
    'value' => 'mm',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Gravatar',
),'',true,true);

$settings['discuss.gravatar_rating']= $modx->newObject('modSystemSetting');
$settings['discuss.gravatar_rating']->fromArray(array(
    'key' => 'discuss.gravatar_rating',
    'value' => 'g',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Gravatar',
),'',true,true);

/* Search Settings */
$settings['discuss.search_class']= $modx->newObject('modSystemSetting');
$settings['discuss.search_class']->fromArray(array(
    'key' => 'discuss.search_class',
    'value' => 'disSearch',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Search',
),'',true,true);

$settings['discuss.search_class_path']= $modx->newObject('modSystemSetting');
$settings['discuss.search_class_path']->fromArray(array(
    'key' => 'discuss.search_class_path',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Search',
),'',true,true);


/* SOLR Settings */
$settings['discuss.solr.hostname']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.hostname']->fromArray(array(
    'key' => 'discuss.solr.hostname',
    'value' => '127.0.0.1',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.port']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.port']->fromArray(array(
    'key' => 'discuss.solr.port',
    'value' => '8080',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.path']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.path']->fromArray(array(
    'key' => 'discuss.solr.path',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.username']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.username']->fromArray(array(
    'key' => 'discuss.solr.username',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.password']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.password']->fromArray(array(
    'key' => 'discuss.solr.password',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.timeout']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.timeout']->fromArray(array(
    'key' => 'discuss.solr.',
    'value' => 30,
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.ssl']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.ssl']->fromArray(array(
    'key' => 'discuss.solr.ssl',
    'value' => false,
    'xtype' => 'combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.ssl_cert']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.ssl_cert']->fromArray(array(
    'key' => 'discuss.solr.ssl_cert',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.ssl_key']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.ssl_key']->fromArray(array(
    'key' => 'discuss.solr.ssl_key',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.ssl_keypassword']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.ssl_keypassword']->fromArray(array(
    'key' => 'discuss.solr.ssl_keypassword',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.ssl_cainfo']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.ssl_cainfo']->fromArray(array(
    'key' => 'discuss.solr.ssl_cainfo',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.ssl_capath']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.ssl_capath']->fromArray(array(
    'key' => 'discuss.solr.ssl_capath',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.proxy_host']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.proxy_host']->fromArray(array(
    'key' => 'discuss.solr.proxy_host',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.proxy_port']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.proxy_port']->fromArray(array(
    'key' => 'discuss.solr.proxy_port',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.proxy_username']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.proxy_username']->fromArray(array(
    'key' => 'discuss.solr.proxy_username',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);

$settings['discuss.solr.proxy_password']= $modx->newObject('modSystemSetting');
$settings['discuss.solr.proxy_password']->fromArray(array(
    'key' => 'discuss.solr.proxy_password',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Solr',
),'',true,true);
$settings['discuss.post_excerpt_length']= $modx->newObject('modSystemSetting');
$settings['discuss.post_excerpt_length']->fromArray(array(
    'key' => 'discuss.post_excerpt_length',
    'value' => '',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Posts',
),'',true,true);
$settings['discuss.session_ttl']= $modx->newObject('modSystemSetting');
$settings['discuss.session_ttl']->fromArray(array(
    'key' => 'discuss.session_ttl',
    'value' => '3600',
    'xtype' => 'numberfield',
    'namespace' => 'discuss',
    'area' => 'Sessions',
),'',true,true);
$settings['discuss.strip_remaining_bbcode']= $modx->newObject('modSystemSetting');
$settings['discuss.strip_remaining_bbcode']->fromArray(array(
    'key' => 'discuss.strip_remaining_bbcode',
    'value' => '0',
    'xtype' => 'modx-combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Sessions',
),'',true,true);

$settings['discuss.group_by_thread']= $modx->newObject('modSystemSetting');
$settings['discuss.group_by_thread']->fromArray(array(
    'key' => 'discuss.group_by_thread',
    'value' => '1',
    'xtype' => 'modx-combo-boolean',
    'namespace' => 'discuss',
    'area' => 'Search',
),'',true,true);

$settings['discuss.max_search_results']= $modx->newObject('modSystemSetting');
$settings['discuss.max_search_results']->fromArray(array(
    'key' => 'discuss.max_search_results',
    'value' => '500',
    'xtype' => 'numberfield',
    'namespace' => 'discuss',
    'area' => 'Search',
),'',true,true);

$settings['discuss.search_results_buffer']= $modx->newObject('modSystemSetting');
$settings['discuss.search_results_buffer']->fromArray(array(
    'key' => 'discuss.search_results_buffer',
    'value' => '200',
    'xtype' => 'numberfield',
    'namespace' => 'discuss',
    'area' => 'Search',
),'',true,true);

// Sphinx
$settings['discuss.sphinx.host_name']= $modx->newObject('modSystemSetting');
$settings['discuss.sphinx.host_name']->fromArray(array(
    'key' => 'discuss.sphinx.host_name',
    'value' => 'localhost',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Sphinx',
),'',true,true);

$settings['discuss.sphinx.port']= $modx->newObject('modSystemSetting');
$settings['discuss.sphinx.port']->fromArray(array(
    'key' => 'discuss.sphinx.port',
    'value' => '9312',
    'xtype' => 'numberfield',
    'namespace' => 'discuss',
    'area' => 'Sphinx',
),'',true,true);

$settings['discuss.sphinx.connection_timeout']= $modx->newObject('modSystemSetting');
$settings['discuss.sphinx.connection_timeout']->fromArray(array(
    'key' => 'discuss.sphinx.connection_timeout',
    'value' => '30',
    'xtype' => 'numberfield',
    'namespace' => 'discuss',
    'area' => 'Sphinx',
),'',true,true);

$settings['discuss.sphinx.searchd_retries']= $modx->newObject('modSystemSetting');
$settings['discuss.sphinx.searchd_retries']->fromArray(array(
    'key' => 'discuss.sphinx.searchd_retries',
    'value' => '3',
    'xtype' => 'numberfield',
    'namespace' => 'discuss',
    'area' => 'Sphinx',
),'',true,true);

$settings['discuss.sphinx.searchd_retry_delay']= $modx->newObject('modSystemSetting');
$settings['discuss.sphinx.searchd_retry_delay']->fromArray(array(
    'key' => 'discuss.sphinx.searchd_retry_delay',
    'value' => '5000',
    'xtype' => 'numberfield',
    'namespace' => 'discuss',
    'area' => 'Sphinx',
),'',true,true);

$settings['discuss.sphinx.indexes']= $modx->newObject('modSystemSetting');
$settings['discuss.sphinx.indexes']->fromArray(array(
    'key' => 'discuss.sphinx.indexes',
    'value' => 'discuss_posts',
    'xtype' => 'textfield',
    'namespace' => 'discuss',
    'area' => 'Sphinx',
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

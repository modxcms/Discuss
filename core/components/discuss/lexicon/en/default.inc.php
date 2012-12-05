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
 * Default English Lexicon Entries for Discuss
 *
 * @package discuss
 * @subpackage lexicon
 */
$_lang['discuss'] = 'Discuss';
$_lang['discuss.menu_desc'] = 'A dynamic, threaded native forum.';

$_lang['discuss.access'] = 'Access';
$_lang['discuss.action'] = 'Action';
$_lang['discuss.active'] = 'Active';
$_lang['discuss.activity_log'] = 'Activity Log';
$_lang['discuss.activity_log.intro_msg'] = 'A log of all moderator/administrator actions performed on the forums.';
$_lang['discuss.activity_log_remove'] = 'Remove Log Entry';
$_lang['discuss.activity_log_remove_confirm'] = 'Are you sure you want to permanently remove this log entry?';
$_lang['discuss.admin'] = 'Administrator';
$_lang['discuss.archived'] = 'Archived';
$_lang['discuss.board'] = 'Board';
$_lang['discuss.board_category_desc'] = 'The Category this Board resides in.';
$_lang['discuss.board_collapsible'] = 'Collapsible';
$_lang['discuss.board_collapsible_desc'] = 'If true, users can collapse the board to hide its contents.';
$_lang['discuss.board_create'] = 'Create Board';
$_lang['discuss.board_create_here'] = 'Create Board Here';
$_lang['discuss.board_description_desc'] = 'A short description of the board, placed under the title when listing boards.';
$_lang['discuss.board_edit'] = 'Edit Board';
$_lang['discuss.board_err_nf'] = 'Board not found with id: [[+id]]';
$_lang['discuss.board_err_ns'] = 'Board not specified.';
$_lang['discuss.board_err_ns_category'] = 'Please select a Category for this Board to belong in.';
$_lang['discuss.board_err_ns_name'] = 'Please enter a valid name for this Board.';
$_lang['discuss.board_err_remove'] = 'An error occurred while trying to remove the Board.';
$_lang['discuss.board_err_save'] = 'An error occurred while trying to save the Board.';
$_lang['discuss.board_ignoreable'] = 'Ignoreable';
$_lang['discuss.board_ignoreable_desc'] = 'If true, users can select to hide this board from their views.';
$_lang['discuss.board_locked'] = 'Locked';
$_lang['discuss.board_locked_desc'] = 'If true, only Administrators can post on this board.';
$_lang['discuss.board_moderators_msg'] = 'This is a list of all the Moderators for this current board.';
$_lang['discuss.board_name_desc'] = 'A name for the board.';
$_lang['discuss.board_merge'] = 'Merge Board';
$_lang['discuss.board_merge.boardaction'] = 'Board Action';
$_lang['discuss.board_merge.merge_into'] = 'Merge Board Into';
$_lang['discuss.board_merge.name'] = 'Board to Merge';
$_lang['discuss.board_merge.start'] = 'Starting Board Merge process...';
$_lang['discuss.board_merge.remove'] = 'Remove Board';
$_lang['discuss.board_merge.deactivate'] = 'Mark Board as Inactive';
$_lang['discuss.board_merge.archive'] = 'Mark Board as Archived';
$_lang['discuss.board_merge.nothing'] = 'Do Nothing';
$_lang['discuss.board_new'] = 'New Board';
$_lang['discuss.board_remove'] = 'Remove Board';
$_lang['discuss.board_remove_confirm'] = 'Are you sure you want to remove this board and all its subboards entirely?';
$_lang['discuss.board_status'] = 'Status';
$_lang['discuss.board_status_desc'] = 'The status of the board. Active boards behave normally. Inactive boards do not show. Archived boards show, but do not allow posting.';
$_lang['discuss.board_usergroup_add'] = 'Add User Group Access to Board';
$_lang['discuss.board_usergroup_remove'] = 'Remove User Group';
$_lang['discuss.board_usergroup_remove_confirm'] = 'Are you sure you want to remove this User Group from accessing this Board?';
$_lang['discuss.board_usergroup_remove_title'] = 'Remove User Group Access?';
$_lang['discuss.board_usergroups_msg'] = 'This is a list of all the User Groups that can access this current board. If none are specified, the Board will be globally accessible.';
$_lang['discuss.boards'] = 'Boards';
$_lang['discuss.boards.intro_msg'] = 'Manage all the boards for your forum. First, create a Category for each board; then you can create boards (and sub-boards) within the Category. You can also drag and drop boards to re-arrange them.';
$_lang['discuss.by'] = 'By';
$_lang['discuss.categories'] = 'Categories';
$_lang['discuss.category'] = 'Category';
$_lang['discuss.category_create'] = 'Create Category';
$_lang['discuss.category_default_moderators'] = 'Default Board Moderators';
$_lang['discuss.category_default_moderators_desc'] = 'A comma-separated list of Moderator usernames that will automatically be added to new Boards in this Category. Leave blank to ignore.';
$_lang['discuss.category_default_usergroups'] = 'Default Board User Groups';
$_lang['discuss.category_default_usergroups_desc'] = 'A comma-separated list of User Group names that will automatically be added to new Boards in this Category, restricting them to those User Groups. Leave blank to ignore.';
$_lang['discuss.category_description_desc'] = 'A short description of the Category.';
$_lang['discuss.category_edit'] = 'Edit Category';
$_lang['discuss.category_err_ae'] = 'A Category already exists with that name.';
$_lang['discuss.category_err_nf'] = 'Category not found with ID [[+id]]';
$_lang['discuss.category_err_ns'] = 'Please specify a Category.';
$_lang['discuss.category_err_ns_name'] = 'Please enter a valid name.';
$_lang['discuss.category_err_remove'] = 'An error occurred while trying to remove the Category.';
$_lang['discuss.category_err_save'] = 'An error occurred while trying to save the Category.';
$_lang['discuss.category_name_desc'] = 'A name for the Category';
$_lang['discuss.category_remove'] = 'Remove Category';
$_lang['discuss.category_remove_confirm'] = 'Are you sure you want to remove this category and all its boards entirely?';
$_lang['discuss.category_update'] = 'Update Category';
$_lang['discuss.custom_title'] = 'Custom Title';
$_lang['discuss.editedon_post'] = '[ed. note: [[+user]] last edited this post [[+on]].]';
$_lang['discuss.error_no_term_passed'] = 'Error: no valid term was passed.';
$_lang['discuss.inactive'] = 'Inactive';
$_lang['discuss.ip'] = 'IP';
$_lang['discuss.information'] = 'Information';
$_lang['discuss.lang_direction'] = 'Lang Direction';
$_lang['discuss.lang_direction_desc'] = 'The direction of the language of this board.';
$_lang['discuss.last_active'] = 'Last Active';
$_lang['discuss.last_login'] = 'Last Login';
$_lang['discuss.last_post_on'] = 'Last Post On';
$_lang['discuss.login_to_post'] = 'Login to Post';
$_lang['discuss.ltr'] = 'Left-to-Right';
$_lang['discuss.member'] = 'Member';
$_lang['discuss.member_add'] = 'Add Member';
$_lang['discuss.member_remove'] = 'Remove Member';
$_lang['discuss.member_remove_confirm'] = 'Are you sure you want to remove this User from this User Group?';
$_lang['discuss.members'] = 'Members';
$_lang['discuss.members_ct'] = '[[+members]] Members';
$_lang['discuss.minimum_post_level'] = 'Minimum Post Level';
$_lang['discuss.minimum_post_level_desc'] = 'The user must be this level of access to post new threads or replies on this board.';
$_lang['discuss.moderator'] = 'Moderator';
$_lang['discuss.moderator_add'] = 'Add Moderator';
$_lang['discuss.moderator_remove'] = 'Remove Moderator';
$_lang['discuss.moderator_remove_confirm'] = 'Are you sure you want to remove this User as a Moderator?';
$_lang['discuss.moderators'] = 'Moderators';
$_lang['discuss.occurredon'] = 'Occurred On';
$_lang['discuss.post_allow_replies'] = 'Allow Replies';
$_lang['discuss.post_allow_replies_desc'] = 'If false, no replies can be posted to this post.';
$_lang['discuss.post_author'] = 'Author';
$_lang['discuss.post_date'] = 'Date';
$_lang['discuss.post_err_nf'] = 'Post not found with ID [[+id]]';
$_lang['discuss.post_err_ns'] = 'Please specify a Post.';
$_lang['discuss.post_err_remove'] = 'An error occurred while trying to remove the Post.';
$_lang['discuss.post_err_save'] = 'An error occurred while trying to save the Post.';
$_lang['discuss.post_ip'] = 'IP';
$_lang['discuss.post_locked'] = 'Locked';
$_lang['discuss.post_locked_desc'] = 'If true, this post cannot be replied to or edited.';
$_lang['discuss.post_message'] = 'Message';
$_lang['discuss.post_modify'] = 'Modify Post';
$_lang['discuss.post_remove'] = 'Remove Post';
$_lang['discuss.post_remove_confirm'] = 'Are you sure you want to remove this post and all its replies entirely?';
$_lang['discuss.post_sticky'] = 'Sticky';
$_lang['discuss.post_sticky_desc'] = 'If true, the post will appear at the beginning of a board.';
$_lang['discuss.post_title'] = 'Title';
$_lang['discuss.post_update'] = 'Update Post';
$_lang['discuss.post_views'] = 'Views';
$_lang['discuss.posts'] = 'Posts';
$_lang['discuss.primary_group'] = 'Primary Group';
$_lang['discuss.primary_group_desc'] = 'The primary User Group this User is a part of. This is used for username coloring and forum badges.';
$_lang['discuss.refresh'] = 'Refresh';
$_lang['discuss.registered_on'] = 'Registered On';
$_lang['discuss.role'] = 'Role';
$_lang['discuss.rtl'] = 'Right-to-Left';
$_lang['discuss.source'] = 'Source';
$_lang['discuss.synced'] = 'Synced';
$_lang['discuss.synced_at'] = 'Synced At';
$_lang['discuss.thread_author'] = 'Thread Owner';
$_lang['discuss.threads'] = 'Threads';
$_lang['discuss.threads_no_replies'] = 'Threads Without Replies';
$_lang['discuss.threads.intro_msg'] = 'Search Threads and manage posts here.';
$_lang['discuss.title'] = 'Title';
$_lang['discuss.unanswered_questions'] = 'Unanswered Questions';
$_lang['discuss.user'] = 'User';
$_lang['discuss.user.trail'] = 'User: [[+user]]';
$_lang['discuss.user_activity_msg'] = 'General information about the activity and source of the User.';
$_lang['discuss.user_birthdate'] = 'Birthdate';
$_lang['discuss.user_boards.intro_msg'] = 'Here you can manage the Boards this User Group can view.';
$_lang['discuss.user_create'] = 'Create User';
$_lang['discuss.user_display_name'] = 'Display Name';
$_lang['discuss.user_email'] = 'Email';
$_lang['discuss.user_err_nf'] = 'User not found with ID [[+id]]';
$_lang['discuss.user_err_ns'] = 'Please select a user.';
$_lang['discuss.user_location'] = 'Location';
$_lang['discuss.user_members.intro_msg'] = 'View all the members of this User Group.';
$_lang['discuss.user_name_first'] = 'First Name';
$_lang['discuss.user_name_last'] = 'Last Name';
$_lang['discuss.user_new'] = 'New User';
$_lang['discuss.user_remove'] = 'Remove User';
$_lang['discuss.user_remove_confirm'] = 'Are you sure you want to remove this user? This will remove them from the MODx installation as well.';
$_lang['discuss.user_perms.intro_msg'] = 'Here you can set permissions for this user.';
$_lang['discuss.user_posts'] = '[[+user]]\'s Posts ([[+count]])';
$_lang['discuss.user_posts.intro_msg'] = 'These are all the Posts made by this user.';
$_lang['discuss.user_show_email'] = 'Show Email';
$_lang['discuss.user_show_online'] = 'Show Online';
$_lang['discuss.user_signature'] = 'Signature';
$_lang['discuss.user_update'] = 'Update User';
$_lang['discuss.user_use_display_name'] = 'Use Display Name';
$_lang['discuss.user_website'] = 'Website';

/* commenting out in prep for moving to proper lexicon file
$_lang['discuss.user.no_new_posts'] = 'no new posts';
$_lang['discuss.user.one_new_post'] = '1 new post';
$_lang['discuss.user.new_posts'] = '[[+total]] new posts';
$_lang['discuss.user.no_new_messages'] = 'no private messages';
$_lang['discuss.user.one_new_message'] = '1 private message';
$_lang['discuss.user.new_messages'] = '[[+total]] private messages';
$_lang['discuss.user.no_new_replies'] = 'no replies';
$_lang['discuss.user.one_new_reply'] = '1 reply';
$_lang['discuss.user.new_replies'] = '[[+total]] unread replies';
$_lang['discuss.user.no_unanswered_questions'] = 'no unanswered questions';
$_lang['discuss.user.one_unanswered_question'] = '1 unanswered question';
$_lang['discuss.user.new_unanswered_questions'] = '[[+total]] unanswered questions';
$_lang['discuss.user.no_no_replies'] = 'no discussions';
$_lang['discuss.user.one_no_reply'] = '1 discussion';
$_lang['discuss.user.no_replies'] = '[[+total]] discussions';
*/

$_lang['discuss.usergroup'] = 'User Group';
$_lang['discuss.usergroup_access'] = 'User Group Access';
$_lang['discuss.usergroup_add'] = 'Add User Group';
$_lang['discuss.usergroup_err_ae'] = 'A User Group already exists with this name!';
$_lang['discuss.usergroup_err_nf'] = 'User Group not found with id [[+id]]';
$_lang['discuss.usergroup_err_ns'] = 'Please select a User Group.';
$_lang['discuss.usergroup_err_ns_name'] = 'Please specify a name for the User Group.';
$_lang['discuss.usergroup_image'] = 'Badge Image';
$_lang['discuss.usergroup_image_desc'] = 'The badge Users get when they are a part of this User Group.';
$_lang['discuss.usergroup_min_posts'] = 'Minimum Posts';
$_lang['discuss.usergroup_min_posts_desc'] = 'If this User Group is a Post-Based Group, then this is the minimum amount of Posts required for a User to enter this Group.';
$_lang['discuss.usergroup_name_desc'] = 'The name for the User Group.';
$_lang['discuss.usergroup_name_color'] = 'Name Color';
$_lang['discuss.usergroup_name_color_desc'] = 'The color a User in this User Group will have in the Online section.';
$_lang['discuss.usergroup_new'] = 'New User Group';
$_lang['discuss.usergroup_post_based'] = 'Post-Based';
$_lang['discuss.usergroup_post_based_desc'] = 'If true, this User Group will be based on Post counts. Once a User reaches the specified count, they will become a part of this User Group.';
$_lang['discuss.usergroup_remove'] = 'Remove User Group';
$_lang['discuss.usergroup_remove_confirm'] = 'Are you sure you want to permanently remove this User Group?';
$_lang['discuss.usergroup_update'] = 'Update User Group';
$_lang['discuss.usergroup_user_remove'] = 'Remove User from User Group';
$_lang['discuss.usergroup_user_remove_confirm'] = 'Are you sure you want to remove this User from the User Group?';
$_lang['discuss.usergroups.intro_msg'] = 'Manage your User Groups here.';
$_lang['discuss.username'] = 'Username';
$_lang['discuss.users'] = 'Users';
$_lang['discuss.users.intro_msg'] = 'Manage your Users across your Discuss installation.';


/* System Settings */
$_lang['setting_discuss.reserved_usernames'] = 'Reserved Usernames';
$_lang['setting_discuss.reserved_usernames_desc'] = 'A comma-separated list of reserved usernames.';

$_lang['setting_discuss.admin_email'] = 'Administrator Email';
$_lang['setting_discuss.admin_email_desc'] = 'An email to use when sending forum notifications.';

$_lang['setting_discuss.admin_groups'] = 'Administrator User Groups';
$_lang['setting_discuss.admin_groups_desc'] = 'A comma-separated list of User Group names that are given Admin access to the forums.';

$_lang['setting_discuss.allow_custom_titles'] = 'Allow Custom Titles';
$_lang['setting_discuss.allow_custom_titles_desc'] = 'Whether or not to allow users to have custom titles.';

$_lang['setting_discuss.allow_guests'] = 'Allow Guests';
$_lang['setting_discuss.allow_guests_desc'] = 'Whether or not to allow anonymous users to browse the forums.';

$_lang['setting_discuss.archive_threads_after'] = 'Archive Threads After';
$_lang['setting_discuss.archive_threads_after_desc'] = 'After X days since the first post on the thread, disallow replies. Set to 0 to always allow replies.';

$_lang['setting_discuss.attachments_allowed_filetypes'] = 'Allowed Filetypes for Attachments';
$_lang['setting_discuss.attachments_allowed_filetypes_desc'] = 'A comma-separated list of filetypes to allow in attachments.';

$_lang['setting_discuss.attachments_max_filesize'] = 'Max File Size of Attachments';
$_lang['setting_discuss.attachments_max_filesize_desc'] = 'In bytes, how large attachments may be in posts.';

$_lang['setting_discuss.attachments_max_per_post'] = 'Maximum Number of Attachments Per Post';
$_lang['setting_discuss.attachments_max_per_post_desc'] = 'The total number of attachments that may be attached to any given post.';

$_lang['setting_discuss.attachments_path'] = 'Attachments Absolute Path';
$_lang['setting_discuss.attachments_path_desc'] = 'The absolute path to the directory where attachments are stored.';

$_lang['setting_discuss.attachments_url'] = 'Attachments URL';
$_lang['setting_discuss.attachments_url_desc'] = 'The URL by which attachments can be accessed.';

$_lang['setting_discuss.bad_words'] = 'Bad Words';
$_lang['setting_discuss.bad_words_desc'] = 'A comma-separated list of words to strip out of posts.';

$_lang['setting_discuss.bad_words_replace'] = 'Replace Bad Words With Censor';
$_lang['setting_discuss.bad_words_replace_desc'] = 'If set to Yes, will replace bad words with the value found in the discuss.bad_words_replace_string setting.';

$_lang['setting_discuss.bad_words_replace_string'] = 'Bad Words Replace String';
$_lang['setting_discuss.bad_words_replace_string_desc'] = 'The censor string to replace bad words with.';

$_lang['setting_discuss.bbcode_enabled'] = 'Enable BBCode';
$_lang['setting_discuss.bbcode_enabled_desc'] = 'Whether or not to enable BBCode on posts.';

$_lang['setting_discuss.courtesy_edit_wait'] = 'Courtesy Edit Wait';
$_lang['setting_discuss.courtesy_edit_wait_desc'] = 'The amount of time, in seconds, in which a user may edit their post without "Edited On" showing up in the post.';

$_lang['setting_discuss.date_format'] = 'Date Format';
$_lang['setting_discuss.date_format_desc'] = 'The date format, in strftime syntax, by which to format all dates in Discuss.';

$_lang['setting_discuss.debug'] = 'Debug Mode';
$_lang['setting_discuss.debug_desc'] = 'If true, will turn on error reporting and show execution times at the end of each page.';

$_lang['setting_discuss.default_board_moderators'] = 'Default Board Moderators';
$_lang['setting_discuss.default_board_moderators_desc'] = 'A comma-separated list of Moderator usernames that will automatically be added to new Boards created in the manager. Leave blank to ignore. Category defaults will override this setting.';;

$_lang['setting_discuss.default_board_usergroups'] = 'Default Board User Groups';
$_lang['setting_discuss.default_board_usergroups_desc'] = 'A comma-separated list of User Group names that will automatically be added to new Boards created in the manager. Adding Groups to this will automatically restrict all new boards to just those User Groups. Leave blank to ignore. Category defaults will override this setting.';

$_lang['setting_discuss.email_reported_post_chunk'] = 'Reported Post Email Chunk';
$_lang['setting_discuss.email_reported_post_chunk_desc'] = 'The Chunk to use for emails sent when someone reports a post.';

$_lang['setting_discuss.email_reported_post_subject'] = 'Reported Post Email Subject';
$_lang['setting_discuss.email_reported_post_subject_desc'] = 'The Subject line to use for emails sent when someone reports a post.';

$_lang['setting_discuss.enable_hot'] = 'Enable Hot Threads';
$_lang['setting_discuss.enable_hot_desc'] = 'If yes, will enable flagging of threads as Hot, where their icon will be differently colored.';

$_lang['setting_discuss.enable_notifications'] = 'Enable Notifications';
$_lang['setting_discuss.enable_notifications_desc'] = 'If no, all email notifications will be turned off.';

$_lang['setting_discuss.enable_sticky'] = 'Enable Sticky Threads';
$_lang['setting_discuss.enable_sticky_desc'] = 'Whether or not to allow sticking of threads to the top of board post lists.';

$_lang['setting_discuss.forum_title'] = 'Forum Title';
$_lang['setting_discuss.forum_title_desc'] = 'The title of your Discuss forum.';

$_lang['setting_discuss.global_moderators'] = 'Global Moderators List';
$_lang['setting_discuss.global_moderators_desc'] = 'A comma-separated list of usernames who are Global Moderators, or moderators for all boards on the forums.';

$_lang['setting_discuss.hot_thread_threshold'] = 'Hot Thread Threshold';
$_lang['setting_discuss.hot_thread_threshold_desc'] = 'The number of posts a thread must have to achieve Hot status.';

$_lang['setting_discuss.max_post_depth'] = 'Maximum Post Depth';
$_lang['setting_discuss.max_post_depth_desc'] = 'If threading is enabled, the maximum depth of posts a thread may go.';

$_lang['setting_discuss.max_signature_length'] = 'Maximum Signature Length';
$_lang['setting_discuss.max_signature_length_desc'] = 'The maximum number of characters a signature may be for any user.';

$_lang['setting_discuss.maximum_post_size'] = 'Maximum Post Size';
$_lang['setting_discuss.maximum_post_size_desc'] = 'The maximum number of characters a post may be in a thread.';

$_lang['setting_discuss.new_replies_threshold'] = 'New Replies Threshold';
$_lang['setting_discuss.new_replies_threshold_desc'] = 'The number of days to show from when viewing new replies to posts.';

$_lang['setting_discuss.notification_new_post_chunk'] = 'New Post Notification Email Chunk';
$_lang['setting_discuss.notification_new_post_chunk_desc'] = 'The Chunk used for the email sent to subscribers of a thread when a new post is made.';

$_lang['setting_discuss.notification_new_post_subject'] = 'New Post Notification Email Subject';
$_lang['setting_discuss.notification_new_post_subject_desc'] = 'The subject line for the email sent to subscribers of a thread when a new post is made.';

$_lang['setting_discuss.num_recent_posts'] = 'Number of Recent Posts';
$_lang['setting_discuss.num_recent_posts_desc'] = 'The number of recent posts to show on the main board index.';

$_lang['setting_discuss.parser_class'] = 'Parser Class';
$_lang['setting_discuss.parser_class_desc'] = 'The name of the class to use for parsing posts.';

$_lang['setting_discuss.parser_class_path'] = 'Parser Class Path';
$_lang['setting_discuss.parser_class_path_desc'] = 'The path to the directory of the parser class. Leave blank to use the default directory.';

$_lang['setting_discuss.post_per_page'] = 'Posts Per Page';
$_lang['setting_discuss.post_per_page_desc'] = 'The default number of posts to show per page on the thread view.';

$_lang['setting_discuss.post_sort_dir'] = 'Default Post Sort Direction';
$_lang['setting_discuss.post_sort_dir_desc'] = 'The direction to sort posts in a Thread, with ASC being most recent at end, or DESC being most recent at beginning of the thread.';

$_lang['setting_discuss.use_custom_post_parser'] = 'Use Custom Post Parser';
$_lang['setting_discuss.use_custom_post_parser_desc'] = 'If set, will use a custom post parser for thread posts instead of BBCode.';

$_lang['setting_discuss.recycle_bin_board'] = 'Recycle Bin Board';
$_lang['setting_discuss.recycle_bin_board_desc'] = 'If set to non-zero, will move any posts marked to be removed by Moderators to this board instead. If set to 0, they will be removed instead.';

$_lang['setting_discuss.search_class'] = 'Search Class';
$_lang['setting_discuss.search_class_desc'] = 'The PHP class to use for Search. Can be overridden to provide custom search implementations.';

$_lang['setting_discuss.search_class_path'] = 'Search Class Path';
$_lang['setting_discuss.search_class_path_desc'] = 'The absolute path to the Search Class, as set in the discuss.search_class setting. Leave blank to use the default path.';

$_lang['setting_discuss.show_whos_online'] = 'Show Whos Online';
$_lang['setting_discuss.show_whos_online_desc'] = 'If set to yes, will display the users currently online.';

$_lang['setting_discuss.solr.hostname'] = 'Solr Hostname';
$_lang['setting_discuss.solr.hostname_desc'] = 'The hostname for the Solr server.';

$_lang['setting_discuss.solr.port'] = 'Solr Port';
$_lang['setting_discuss.solr.port_desc'] = 'The port number for the Solr server.';

$_lang['setting_discuss.solr.path'] = 'Solr Path';
$_lang['setting_discuss.solr.path_desc'] = 'The absolute path to Solr.';

$_lang['setting_discuss.solr.username'] = 'Solr Username';
$_lang['setting_discuss.solr.username_desc'] = 'The username used for HTTP Authentication, if any.';

$_lang['setting_discuss.solr.password'] = 'Solr Password';
$_lang['setting_discuss.solr.password_desc'] = 'The HTTP Authentication password, if any.';

$_lang['setting_discuss.solr.proxy_host'] = 'Solr Proxy Hostname';
$_lang['setting_discuss.solr.proxy_host_desc'] = 'The hostname for the proxy server to Solr, if any.';

$_lang['setting_discuss.solr.proxy_port'] = 'Solr Proxy Port';
$_lang['setting_discuss.solr.proxy_port_desc'] = 'The port number for the proxy server to Solr, if any.';

$_lang['setting_discuss.solr.proxy_username'] = 'Solr Proxy Username';
$_lang['setting_discuss.solr.proxy_username_desc'] = 'The username for the proxy server to Solr, if any.';

$_lang['setting_discuss.solr.proxy_password'] = 'Solr Proxy Password';
$_lang['setting_discuss.solr.proxy_password_desc'] = 'The password for the proxy server to Solr, if any.';

$_lang['setting_discuss.solr.timeout'] = 'Solr Request Timeout';
$_lang['setting_discuss.solr.timeout_desc'] = 'This is maximum time in seconds allowed for the http data transfer operation to Solr.';

$_lang['setting_discuss.solr.ssl'] = 'Solr Use SSL';
$_lang['setting_discuss.solr.ssl_desc'] = 'If Yes, will connect to Solr via SSL.';

$_lang['setting_discuss.solr.ssl_cert'] = 'Solr SSL Cert';
$_lang['setting_discuss.solr.ssl_cert_desc'] = 'File name to a PEM-formatted file containing the private key + private certificate (concatenated in that order)';

$_lang['setting_discuss.solr.ssl_key'] = 'Solr SSL Key';
$_lang['setting_discuss.solr.ssl_key_desc'] = 'File name to a PEM-formatted private key file only.';

$_lang['setting_discuss.solr.ssl_keypassword'] = 'Solr SSL Key Password';
$_lang['setting_discuss.solr.ssl_keypassword_desc'] = 'Password for private key for SSL key.';

$_lang['setting_discuss.solr.ssl_cainfo'] = 'Solr SSL CA Certificates';
$_lang['setting_discuss.solr.ssl_cainfo_desc'] = 'Name of file holding one or more CA certificates to verify peer with';

$_lang['setting_discuss.solr.ssl_capath'] = 'Solr SSL CA Certificate Path';
$_lang['setting_discuss.solr.ssl_capath_desc'] = 'Name of directory holding multiple CA certificates to verify peer with.';

$_lang['setting_discuss.spam_bucket_board'] = 'Spam Box Board';
$_lang['setting_discuss.spam_bucket_board_desc'] = 'If set to non-zero, will move any posts marked as spam by Moderators to this board instead. If set to 0, they will be deleted instead.';

$_lang['setting_discuss.stats_enabled'] = 'Enable Statistics';
$_lang['setting_discuss.stats_enabled_desc'] = 'If set to yes, enables forum-wide statistcs.';

$_lang['setting_discuss.theme'] = 'Theme';
$_lang['setting_discuss.theme_desc'] = 'The theme to use for the board. Must be lowercase and the name of the theme directory.';

$_lang['setting_discuss.threads_per_page'] = 'Threads Per Page';
$_lang['setting_discuss.threads_per_page_desc'] = 'The default number of threads to show per page on the board view.';

$_lang['setting_discuss.user_active_threshold'] = 'Active User Threshold';
$_lang['setting_discuss.user_active_threshold_desc'] = 'The number of minutes a user must be active in to stay within the Active User threshold.';


/* SSO Mode */
$_lang['setting_discuss.sso_mode'] = 'SSO Mode';
$_lang['setting_discuss.sso_mode_desc'] = 'If set to yes, Discuss will attempt to redirect Profile, Login, Logout and other SSO functionality to external MODX pages to allow you to integrate into the MODX user system. This is recommended.';

$_lang['setting_discuss.forums_resource_id'] = 'Forums Resource ID';
$_lang['setting_discuss.forums_resource_id_desc'] = 'The ID of the Resource your Discuss call is on.';

$_lang['setting_discuss.login_resource_id'] = 'Login Resource ID';
$_lang['setting_discuss.login_resource_id_desc'] = 'The ID of the Resource your Login call is on.';

$_lang['setting_discuss.register_resource_id'] = 'Register Resource ID';
$_lang['setting_discuss.register_resource_id_desc'] = 'The ID of the Resource your Register call is on.';

$_lang['setting_discuss.update_profile_resource_id'] = 'Update Profile Resource ID';
$_lang['setting_discuss.update_profile_resource_id_desc'] = 'The ID of the Resource your UpdateProfile call is on.';

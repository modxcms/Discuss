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
 * @subpackage lexicon
 */
$_lang['discuss.attachment_err_upload'] = 'An error occurred while trying to upload the attachment: [[+error]]';
$_lang['discuss.attachment_add'] = 'Add Attachment';
$_lang['discuss.attachments'] = 'Attachments';
$_lang['discuss.attachments_max'] = 'max of [[+max]]';
$_lang['discuss.attachment_bad_type'] = 'Attachment [[+idx]] is not an allowed file type.';
$_lang['discuss.attachment_err_upload'] = 'An error occurred while trying to upload attachment [[+idx]].';
$_lang['discuss.attachment_too_large'] = 'Attachment [[+idx]] cannot be larger than [[+maxSize]] bytes; this one is [[+size]] bytes. Please specify a smaller attachment.';

$_lang['discuss.message'] = 'Message';
$_lang['discuss.new_post_type_instructions'] = '&larr; Is this a general discussion or are you looking for a specific answer?';
$_lang['discuss.question_instructions'] = '<b>Help us help you</b>. If you’re having technical problems, please supply adequate details: web server type and version; PHP version, configuration and how it runs; MySQL details and <em>especially</em> the MODX version and list of Add-ons installed.';
$_lang['discuss.question_links'] = '<a href="[[++discuss.questions_link]]" title="See our handy post on the best ways and types of things to consider when requesting help">Learn how to get help</a> <a href="#" onclick="surroundText(\'[[%discuss.question_template]]\',\'\');return false;" title="A super-handy template so you won’t forget important bits">Insert suggested tech question template</a>';
$_lang['discuss.question_template'] = '[b]Description of Problem:[/b] \n\n[b]Steps to Reproduce:[/b] \n\n[b]Expected Outcome:[/b] \n\n[ul]\n[*][b]MODX Version:[/b] \n[*][b]PHP Version:[/b] \n[*][b]Database (MySQL, SQL Server, etc) Version:[/b] \n[*][b]Additional Server Info:[/b] \n[*][b]Installed MODX Add-ons:[/b] \n[*][b]Error Log Contents:[/b] [i](attach as file if it’s too large)[/i] \n[/ul]';
$_lang['discuss.discussion_instructions'] = '<b>Be clear, concise, and stay on topic</b>. Use a discussion title that gives insight into your topic without having to read the entire message. Also, limit your topics to a single one per thread if possible.';
$_lang['discuss.discussion_links'] = '<a href="[[++discuss.guidelines_link]]" title="Learn what we allow at MODX for appropriate topics">See our Forums policy…</a>';
$_lang['discuss.new_post_made'] = 'A New Post Has Been Made';
$_lang['discuss.notify_of_replies'] = 'Notify of Replies';

$_lang['discuss.correct_errors'] = 'Please correct the errors in your form.';
$_lang['discuss.post_err_create'] = 'An error occurred while trying to save the new thread.';
$_lang['discuss.post_err_nf'] = 'Post not found!';
$_lang['discuss.post_err_ns'] = 'Post not specified!';
$_lang['discuss.post_err_ns_message'] = 'Please enter a message.';
$_lang['discuss.post_err_ns_title'] = 'Please enter a valid post title.';
$_lang['discuss.post_err_remove'] = 'An error occurred while trying to remove the post.';
$_lang['discuss.post_err_reply'] = 'An error occurred while trying to post a reply.';
$_lang['discuss.post_err_save'] = 'An error occurred while trying to save the post.';
$_lang['discuss.thread_err_nf'] = 'Thread not found.';

$_lang['discuss.post_modify'] = 'Modify Post';
$_lang['discuss.post_new'] = 'New Post';
$_lang['discuss.post_reply'] = 'Reply to Post';
$_lang['discuss.preview'] = 'Preview';
$_lang['discuss.save_changes'] = 'Save Changes';

$_lang['discuss.solve'] = 'Answer';
$_lang['discuss.solved'] = 'Answered';
$_lang['discuss.unsolve'] = 'Remove Answer';
$_lang['discuss.unsolved'] = 'No Answers';

$_lang['discuss.thread'] = 'Thread';
$_lang['discuss.new_thread'] = 'New Post';
$_lang['discuss.thread_remove'] = 'Remove Thread';
$_lang['discuss.thread_remove_confirm'] = 'Are you sure you want to permanently remove the thread "[[+thread]]"?';
$_lang['discuss.thread_summary'] = 'Thread Summary';
$_lang['discuss.title'] = 'Title';
$_lang['discuss.views'] = 'Views';

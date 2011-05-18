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
 *
 * @package discuss
 */
if (empty($scriptProperties['post'])) { $discuss->sendErrorPage(); }
$post = $modx->getObject('disPost',$scriptProperties['post']);
if ($post == null) { $discuss->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.modify_post_header',array('title' => $post->get('title'))));
$modx->lexicon->load('discuss:post');

/* setup defaults */
$placeholders = $post->toArray();
$placeholders['url'] = $post->getUrl();
$placeholders['post'] = $post->get('id');
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));
$placeholders['message'] = $post->br2nl($placeholders['message']);
$placeholders['message'] = str_replace(array('[',']'),array('&#91;','&#93;'),$placeholders['message']);

/* get thread root */
$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread')));
if ($thread == null) $discuss->sendErrorPage();
$placeholders['thread'] = $thread->get('id');
$placeholders['locked'] = $thread->get('locked');
$placeholders['sticky'] = $thread->get('sticky');

/* ensure user can modify this post */
$isModerator = $discuss->user->isGlobalModerator() || $thread->isModerator($discuss->user->get('id')) || $discuss->user->isAdmin();
$canModifyPost = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.thread_modify');
$canModify = $discuss->user->get('id') == $post->get('author') || ($isModerator && $canModifyPost);
if (!$canModify) {
    $modx->sendRedirect($thread->getUrl());
}

/* get attachments for post */
$attachments = $post->getMany('Attachments');
$idx = 1;
$atts = array();
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostEditAttachment');
foreach ($attachments as $attachment) {
    $attachmentArray = $attachment->toArray();
    $attachmentArray['filesize'] = $attachment->convert();
    $attachmentArray['url'] = $attachment->getUrl();
    $attachmentArray['idx'] = $idx;
    $atts[] = $discuss->getChunk($postAttachmentRowTpl,$attachmentArray);
    $idx++;
}

/* attachments */
$placeholders['attachment_fields'] = '';
$placeholders['attachments'] = implode("\n",$atts);
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$placeholders['attachmentCurIdx'] = count($attachments)+1;
if ($thread->canPostAttachments()) {
    $placeholders['attachment_fields'] = $discuss->getChunk('post/disAttachmentFields',$placeholders);
}

/* get board breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $post->get('board'),
));
$c->sortby('Ancestors.depth','ASC');
$ancestors = $modx->getCollection('disBoard',$c);

/* build breadcrumbs */
$board = $thread->getOne('Board');
if ($board) {
    $board->buildBreadcrumbs(array(array(
        'text' => $modx->lexicon('discuss.modify_post_header',array(
            'post' => $post->get('title'),
        )),
        'active' => true,
    )),true);
}
$placeholders['trail'] = $board->get('trail');

/* perms */
if ($thread->canLock()) {
    $checked = !empty($_POST) ? !empty($_POST['locked']) : $thread->get('locked');
    $placeholders['locked'] = $checked ? ' checked="checked"' : '';
    $placeholders['locked_cb'] = '<label class="dis-cb"><input type="checkbox" name="locked" value="1" '.$placeholders['locked'].' />'.$modx->lexicon('discuss.thread_lock').'</label>';
    $placeholders['can_lock'] = true;
}
if ($thread->canStick()) {
    $checked = !empty($_POST) ? !empty($_POST['sticky']) : $thread->get('sticky');
    $placeholders['sticky'] = $checked ? ' checked="checked"' : '';
    $placeholders['sticky_cb'] = '<label class="dis-cb"><input type="checkbox" name="sticky" value="1" '.$placeholders['sticky'].' />'.$modx->lexicon('discuss.thread_stick').'</label>';
    $placeholders['can_stick'] = true;
}

/* get thread */
$threadData = $discuss->hooks->load('post/getthread',array(
    'post' => &$post,
    'thread' => $post->get('thread'),
    'limit' => 5,
));
$placeholders['thread_posts'] = $threadData['results'];

/* output form to browser */
$modx->regClientHTMLBlock('<script type="text/javascript">
var DISModifyPost = $(function() {
    DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].';
    DIS.DISModifyPost.init({
        attachments: '.(count($attachments)+1).'
    });
});</script>');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholders($placeholders,'fi.');

return $placeholders;
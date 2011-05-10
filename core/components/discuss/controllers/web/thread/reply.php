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
 * Reply to a current post
 *
 * @package discuss
 */
/* get post */
if (empty($scriptProperties['thread'])) {
    if (empty($scriptProperties['post'])) { $modx->sendErrorPage(); }
    $post = $modx->getObject('disPost',$scriptProperties['post']);
    if (empty($post)) $modx->sendErrorPage();

    /* get thread root */
    $thread = $post->getOne('Thread');
    if (empty($thread)) $modx->sendErrorPage();
} else {
    $thread = $modx->getObject('disThread',$scriptProperties['thread']);
    if (empty($thread)) $modx->sendErrorPage();
    $post = $thread->getOne('FirstPost');
    if (empty($post)) $modx->sendErrorPage();
}
$discuss->setPageTitle($modx->lexicon('discuss.reply_to_post',array('title' => $post->get('title'))));
$modx->lexicon->load('discuss:post');

/* ensure user can actually reply */
if (!$post->canReply()) $modx->sendErrorPage();

$author = $post->getOne('Author');

/* setup default snippet properties */
$replyPrefix = $modx->getOption('replyPrefix',$scriptProperties,'Re: ');

/* setup placeholders */
$placeholders = $post->toArray();
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));

$placeholders['post'] = $placeholders['id'];
$placeholders['thread'] = $thread->get('id');
$placeholders['title'] = $replyPrefix.str_replace($replyPrefix,'',$post->get('title'));

/* build breadcrumbs */
$board = $thread->getOne('Board');
if ($board) {
    $board->buildBreadcrumbs(array(array(
        'text' => $modx->lexicon('discuss.reply_to_post',array(
            'post' => '<a class="active" href="'.$discuss->url.'thread?thread='.$thread->get('id').'">'.$post->get('title').'</a>',
        )),
        'active' => true,
    )),true);
    $placeholders['trail'] = $board->get('trail');
} else {
    $placeholders['trail'] = '';
}

/* get thread */
$threadData = $discuss->hooks->load('post/getthread',array(
    'post' => &$post,
    'thread' => $post->get('thread'),
    'limit' => 5,
));
$placeholders['thread_posts'] = $threadData['results'];

/* perms */
if ($thread->canLock()) {
    $placeholders['locked'] = !empty($_POST['locked']) ? ' checked="checked"' : '';
    $placeholders['locked_cb'] = '<label class="dis-cb"><input type="checkbox" name="locked" value="1" '.$placeholders['locked'].' />'.$modx->lexicon('discuss.thread_lock').'</label>';
    $placeholders['can_lock'] = true;
}
if ($thread->canStick()) {
    $placeholders['sticky'] = !empty($_POST['sticky']) ? ' checked="checked"' : '';
    $placeholders['sticky_cb'] = '<label class="dis-cb"><input type="checkbox" name="sticky" value="1" '.$placeholders['sticky'].' />'.$modx->lexicon('discuss.thread_stick').'</label>';
    $placeholders['can_stick'] = true;
}

/* attachments */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$placeholders['attachment_fields'] = '';
if ($thread->canPostAttachments()) {
    $placeholders['attachment_fields'] = $discuss->getChunk('post/disAttachmentFields',$placeholders);
}
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
$(function() { DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].'; });
</script>');

/* quote functionality */
if (empty($_POST) && !empty($scriptProperties['quote'])) {
    $placeholders['message'] = str_replace(array('[',']'),array('&#91;','&#93;'),$post->br2nl($post->get('message')));
    $placeholders['message'] = '[quote author='.$author->get('username').' date='.strtotime($post->get('createdon')).']'.$placeholders['message'].'[/quote]'."\n";
} elseif (empty($_POST) && empty($scriptProperties['quote'])) {
    $placeholders['message'] = '';
}


/* output form to browser */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholders($placeholders,'fi.');

return $placeholders;
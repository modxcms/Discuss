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
if (empty($scriptProperties['post'])) { $modx->sendErrorPage(); }
$post = $modx->getObject('disPost',$scriptProperties['post']);
if ($post == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.modify_post_header',array('title' => $post->get('title'))));

$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread'),disThread::TYPE_MESSAGE));
if (empty($thread)) $modx->sendErrorPage();

/* setup defaults */
$placeholders = $post->toArray();
$placeholders['post'] = $post->get('id');
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));
$placeholders['participants_usernames'] = $thread->get('participants_usernames');
$placeholders['thread'] = $thread->get('id');
$placeholders['message'] = $post->br2nl($placeholders['message']);
$placeholders['message'] = str_replace(array('[',']'),array('&#91;','&#93;'),$placeholders['message']);

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
$placeholders['attachments'] = implode("\n",$atts);
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);
$placeholders['attachmentCurIdx'] = count($attachments)+1;

/* get board breadcrumb trail */
$c = $modx->newQuery('disBoard');
$c->innerJoin('disBoardClosure','Ancestors');
$c->where(array(
    'Ancestors.descendant' => $post->get('board'),
));
$c->sortby('Ancestors.depth','ASC');
$ancestors = $modx->getCollection('disBoard',$c);

/* get thread */
$thread = $discuss->hooks->load('post/getthread',array(
    'post' => &$post,
    'thread' => $post->get('thread'),
    'limit' => 5,
));
$placeholders['thread_posts'] = $thread['results'];

/* output form to browser */
$modx->regClientHTMLBlock('<script type="text/javascript">
var DISModifyMessage = $(function() {
    DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].';
    DIS.DISModifyMessage.init({
        attachments: '.(count($attachments)+1).'
    });
});</script>');
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('disError'));
$modx->setPlaceholders($placeholders,'fi.');

return $placeholders;
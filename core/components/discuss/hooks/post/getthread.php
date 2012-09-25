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
 * Get a threaded view of a post.
 *
 * @package discuss
 * @subpackage hooks
 *
 * @var modX $modx
 * @var array $scriptProperties
 * @var Discuss $discuss
 * @var DiscussController $controller
 */
/* get thread or root of post */

/** @var disThread|disThreadQuestion $thread */
$thread = $modx->getOption('thread',$scriptProperties,'');
if (empty($thread)) return false;
if (!is_object($thread)) {
    $thread = $modx->call('disThread', 'fetch', array(&$modx,$thread));
    if (empty($thread)) return false;
}

$limit = $modx->getOption('limit',$scriptProperties,(int)$modx->getOption('discuss.post_per_page',$scriptProperties, 10));
$page = !empty($_GET['page']) ? $_GET['page'] - 1 : 0;
$start = $page * $limit;
$tpl = !empty($_REQUEST['print']) ? 'post/disThreadPostPrint' : 'post/disThreadPost';

/* Verify the posts output type - Flat or Threaded */
$flat = $modx->getOption('flat',$scriptProperties,false);
$flat = true;
/* get default properties */
$postTpl = $modx->getOption('postTpl',$scriptProperties,'post/disThreadPost');
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostAttachment');

$isAdmin = $discuss->user->isAdmin();
$isModerator = $thread->isModerator();
$sortDir = $modx->getOption('discuss.post_sort_dir',$scriptProperties,'ASC');
$sortAnswerFirst = $modx->getOption('sortAnswerFirst', $scriptProperties, false);
/* get posts */
$post = $modx->getOption('post',$scriptProperties,false);
$posts = $thread->fetchPosts($post,array(
    'limit' => $limit,
    'start' => $start,
    'flat' => $flat,
    'sortDir' => $sortDir,
    'sortAnswerFirst' => $sortAnswerFirst,
));

/* setup basic settings/permissions */
$dateFormat = $modx->getOption('discuss.date_format',null,'%b %d, %Y, %H:%M %p');
$allowCustomTitles = $modx->getOption('discuss.allow_custom_titles',null,true);
$maxPostDepth = $modx->getOption('discuss.max_post_depth',null,3);

$canViewAttachments = $modx->hasPermission('discuss.view_attachments');
$canTrackIp = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.track_ip');
$canViewEmails = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.view_emails');
$canViewProfiles = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.view_profiles');
$canReportPost = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.thread_report');
$canMarkAsAnswer = $thread->get('class_key') == 'disThreadQuestion' && $thread->canMarkAsAnswer();

$rowCls = $controller->getOption('rowCls','dis-post');
$childrenCls = $controller->getOption('childrenCls','dis-board-post');
$actionLinkTpl = $controller->getOption('actionLinkTpl','disActionLink');

/* iterate */
$plist = array();
$output = array();
$idx = $sortDir == 'ASC' ? $start : $posts['total'] - $start - 1;
/** @var disPost $post */
foreach ($posts['results'] as $post) {
    $post->set('idx',$idx);
    $postArray = $post->toArray();
    $postArray['url'] = $post->getUrl();
    $postArray['children'] = '';

    if (!empty($post->EditedBy)) {
        $postArray = array_merge($postArray,$post->EditedBy->toArray('editedby.'));
        unset($postArray['editedby.password'],$postArray['editedby.cachepwd']);
    }

    $post->renderAuthorMeta($postArray);

    $postArray['children_class'] = array($childrenCls);
    $postArray['class'] = array($rowCls);
    if (!$flat) {
        /* set depth and check max post depth */
        $postArray['children_class'][] = 'dis-depth-'.$postArray['depth'];
        if ($postArray['depth'] > $maxPostDepth) {
            /* Don't hide post if it exceed max depth, set its depth placeholder to max depth value instead */
            $postArray['depth'] = $maxPostDepth;
        }
    }

    /* format bbcode */
    $postArray['content'] = $post->getContent();

    /* check allowing of custom titles */
    if (!$allowCustomTitles) {
        $postArray['author.title'] = '';
    }

    /* load actions */
    $postArray['action_reply'] = '';
    $postArray['actions'] = array();
    if (($isAdmin || $isModerator || !$thread->get('locked')) && $discuss->user->isLoggedIn) {
        if ($post->canReply()) {
            $postArray['action_reply'] = $discuss->getChunk($actionLinkTpl,array(
                'url' => $discuss->request->makeUrl('thread/reply',array('post' => $post->get('id'))),
                'text' => $modx->lexicon('discuss.reply'),
                'class' => 'dis-post-reply',
                'id' => '',
                'attributes' => '',
            ));
            $postArray['action_quote'] = $discuss->getChunk($actionLinkTpl,array(
                'url' => $discuss->request->makeUrl('thread/reply',array('post' => $post->get('id'),'quote' => 1)),
                'text' => $modx->lexicon('discuss.quote'),
                'class' => 'dis-post-quote',
                'id' => '',
                'attributes' => '',
            ));
        }

        if ($post->canModify()) {
            $postArray['action_modify'] = $discuss->getChunk($actionLinkTpl,array(
                'url' => $discuss->request->makeUrl('thread/modify',array('post' => $post->get('id'))),
                'text' => $modx->lexicon('discuss.modify'),
                'class' => 'dis-post-modify',
                'id' => '',
                'attributes' => '',
            ));
        }

        if ($post->canRemove()) {
            $postArray['action_remove'] = $discuss->getChunk($actionLinkTpl,array(
                'url' => $discuss->request->makeUrl('post/remove',array('post' => $post->get('id'))),
                'text' => $modx->lexicon('discuss.remove'),
                'class' => 'dis-post-remove',
                'id' => '',
                'attributes' => '',
            ));
            if ($isModerator || $isAdmin) {
                $postArray['action_spam'] = $discuss->getChunk($actionLinkTpl,array(
                    'url' => $discuss->request->makeUrl('post/spam',array('post' => $post->get('id'))),
                    'text' => $modx->lexicon('discuss.post_spam'),
                    'class' => 'dis-post-spam',
                    'id' => '',
                    'attributes' => '',
                ));
            }
        }
    }

    /* order action buttons */
    $postArray['actions'] = $thread->aggregateThreadActionButtons($postArray);

    /* get attachments */
    $postArray['attachments'] = '';
    if ($canViewAttachments) {
        $attachments = $post->getMany('Attachments');
        if (!empty($attachments)) {
            $postArray['attachments'] = array();
            /** @var disPostAttachment $attachment */
            foreach ($attachments as $attachment) {
                $attachmentArray = $attachment->toArray();
                $attachmentArray['filesize'] = $attachment->convert();
                $attachmentArray['url'] = $attachment->getUrl();
                $postArray['attachments'][] = $discuss->getChunk('post/disPostAttachment',$attachmentArray);
            }
            $postArray['attachments'] = implode("\n",$postArray['attachments']);
        }
    }

    if ($canReportPost) {
        $postArray['report_link'] = $discuss->getChunk('disActionLink',array(
            'url' => $discuss->request->makeUrl('post/report',array('post' => $postArray['id'],'thread' => $postArray['thread'])),
            'text' => $modx->lexicon('discuss.report_to_mod'),
            'class' => 'dis-report-link',
            'id' => '',
            'attributes' => '',
        ));
    } else {
        $postArray['report_link'] = '';
    }
    
    if (!$isModerator || !$canTrackIp) {
        $postArray['ip'] = '';
    }
    $postArray['idx'] = $idx+1;

    /* prepare thread view for derivative thread types */
    $postArray = $thread->prepareThreadView($postArray);

    /* prepare specific properties for rendering */
    $postArray['actions'] = implode("\n",$postArray['actions']);
    $postArray['createdon'] = strftime($dateFormat,strtotime($postArray['createdon']));
    $postArray['class'] = implode(' ',$postArray['class']);
    $postArray['children_class'] = implode(' ',$postArray['children_class']);
    
    /* fire OnDiscussPostBeforeRender */
    $modx->invokeEvent('OnDiscussPostBeforeRender',array(
        'post' => &$post,
        'postArray' => &$postArray,
        'idx' => $idx+1,
        'tpl' => $tpl,
        'flat' => $flat,
        'isAdmin' => $isAdmin,
        'isModerator' => $isModerator,
    ));
    
    if ($flat) {
        $output[] = $discuss->getChunk($tpl,$postArray);
    } else {
        $plist[] = $postArray;
    }
    $idx = $sortDir == 'ASC' ? $idx + 1 : $idx - 1;
}
$response = array(
    'total' => $posts['total'],
    'start' => $start,
    'limit' => $limit,
);
if (empty($flat)) {
    /* parse posts via tree parser */
    $discuss->loadTreeParser();
    if (count($plist) > 0) {
        $output = $discuss->treeParser->parse($plist,$tpl);
    }
} else {
    $output = implode($controller->getOption('rowSeparator',"\n"),$output);
}
$response['results'] = $output;
return $response;

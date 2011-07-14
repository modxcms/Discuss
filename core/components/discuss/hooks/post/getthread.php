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
 */
/* get thread or root of post */
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
$isModerator = $discuss->user->isGlobalModerator() || $thread->isModerator($discuss->user->get('id')) || $discuss->user->isAdmin();
$sortDir = $modx->getOption('discuss.post_sort_dir',$scriptProperties,'ASC');

/* get posts */
$post = $modx->getOption('post',$scriptProperties,false);
$posts = $thread->fetchPosts($post,array(
    'limit' => $limit,
    'start' => $start,
    'flat' => $flat,
    'sortDir' => $sortDir,
));

/* setup basic settings/permissions */
$dateFormat = $modx->getOption('discuss.date_format',null,'%b %d, %Y, %H:%M %p');
$allowCustomTitles = $modx->getOption('discuss.allow_custom_titles',null,true);
$canModifyPost = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.thread_modify');
$canRemovePost = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.thread_remove');
$canViewAttachments = $modx->hasPermission('discuss.view_attachments');
$canTrackIp = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.track_ip');
$canViewEmails = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.view_emails');
$canViewProfiles = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.view_profiles');
$canReportPost = $discuss->user->isLoggedIn && $modx->hasPermission('discuss.thread_report');
$canMarkAsAnswer = $thread->get('class_key') == 'disThreadQuestion' && $thread->canMarkAsAnswer();

/* iterate */
$plist = array();
$output = array();
$idx = $sortDir == 'ASC' ? $start : $posts['total'] - $start - 1;
foreach ($posts['results'] as $post) {
    $post->set('idx',$idx);
    $postArray = $post->toArray();
    $postArray['url'] = $post->getUrl();
    $postArray['children'] = '';

    if ($post->Author) {
        $postArray = array_merge($postArray,$post->Author->toArray('author.'));
        $postArray['author.signature'] = $post->Author->parseSignature();
        $postArray['author.posts'] = number_format($postArray['author.posts']);
    }
    unset($postArray['author.password'],$postArray['author.cachepwd']);
    if (!empty($post->EditedBy)) {
        $postArray = array_merge($postArray,$post->EditedBy->toArray('editedby.'));
        unset($postArray['editedby.password'],$postArray['editedby.cachepwd']);
    }

    if ($post->Author) {
        if ($canViewProfiles) {
            $postArray['author.username_link'] = '<a href="'.$post->Author->getUrl().'">'.$post->Author->get('name').'</a>';
        } else {
            $postArray['author.username_link'] = '<span class="dis-username">'.$post->Author->get('name').'</span>';
        }
        if ($post->Author->get('status') == disUser::BANNED) {
            $postArray['author.username_link'] .= '<span class="dis-banned">'.$modx->lexicon('discuss.banned').'</span>';
        }

        /* set author avatar */
        $avatarUrl = $post->Author->getAvatarUrl();
        if (!empty($avatarUrl)) {
            $postArray['author.avatar'] = '<img class="dis-post-avatar" alt="'.$postArray['author'].'" src="'.$avatarUrl.'" />';
        }

        /* check if author wants to show email */
        if ($post->Author->get('show_email') && $discuss->user->isLoggedIn && $canViewEmails) {
            $post->loadParser();
            $postArray['author.email'] = disBBCodeParser::encodeEmail($post->Author->get('email'),$modx->lexicon('discuss.email_author'));
        } else {
            $postArray['author.email'] = '';
        }

        /* get primary group badge/name, if applicable */
        $postArray['author.group_badge'] = $post->Author->getGroupBadge();
        $postArray['author.group_name'] = '';
        if (!empty($post->Author->PrimaryGroup)) {
            $postArray['author.group_name'] = $post->Author->PrimaryGroup->get('name');
        }
    }

    $postArray['children_class'] = array('dis-board-post');
    $postArray['class'] = array('dis-post');
    if (!$flat) {
        /* set depth and check max post depth */
        $postArray['children_class'][] = 'dis-depth-'.$postArray['depth'];
        if ($postArray['depth'] > $modx->getOption('discuss.max_post_depth',null,3)) {
            /* Don't hide post if it exceed max depth, set its depth placeholder to max depth value instead */
            $postArray['depth'] = $modx->getOption('discuss.max_post_depth',null,3);
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
            $postArray['action_reply'] = '<a href="'.$discuss->request->makeUrl('thread/reply',array('post' => $post->get('id'))).'" class="dis-post-reply">'.$modx->lexicon('discuss.reply').'</a>';
            $postArray['actions'][] = $postArray['action_reply'];
            $postArray['actions'][] = '<a href="'.$discuss->request->makeUrl('thread/reply',array('post' => $post->get('id'),'quote' => 1)).'" class="dis-post-quote">'.$modx->lexicon('discuss.quote').'</a>';
        }

        $canModifyPost = $discuss->user->get('id') == $post->get('author') || ($isModerator && $canModifyPost);
        if ($canModifyPost) {
            $postArray['actions'][] = '<a href="'.$discuss->request->makeUrl('thread/modify',array('post' => $post->get('id'))).'" class="dis-post-modify">'.$modx->lexicon('discuss.modify').'</a>';
        }

        $canRemovePost = $discuss->user->get('id') == $post->get('author') || ($isModerator && $canRemovePost);
        if ($canRemovePost) {
            $postArray['actions'][] = '<a href="'.$discuss->request->makeUrl('post/remove',array('post' => $post->get('id'))).'" class="dis-post-remove">'.$modx->lexicon('discuss.remove').'</a>';
            if ($isModerator || $isAdmin) {
                $postArray['actions'][] = '<a href="'.$discuss->request->makeUrl('post/spam',array('post' => $post->get('id'))).'" class="dis-post-spam">'.$modx->lexicon('discuss.post_spam').'</a>';
            }
        }
    }

    /* get attachments */
    $postArray['attachments'] = '';
    if ($canViewAttachments) {
        $attachments = $post->getMany('Attachments');
        if (!empty($attachments)) {
            $postArray['attachments'] = array();
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
        $postArray['report_link'] = '<a class="dis-report-link" href="'.$discuss->request->makeUrl('post/report',array(
            'thread' => $postArray['thread'],
            'post' => $postArray['id']
        )).'">'.$modx->lexicon('discuss.report_to_mod').'</a>';
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
    $output = implode("\n",$output);
}
$response['results'] = str_replace(array('[',']'),array('&#91;','&#93;'),$output);
return $response;
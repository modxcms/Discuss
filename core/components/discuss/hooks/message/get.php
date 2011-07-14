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

$limit = $modx->getOption('limit',$scriptProperties,(int)$modx->getOption('discuss.post_per_page',$scriptProperties, 10));
$start = intval(isset($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0);

/* Verify the posts output type - Flat or Threaded */
$flat = $modx->getOption('flat',$scriptProperties,false);
$flat = true;
/* get default properties */
$postTpl = $modx->getOption('postTpl',$scriptProperties,'post/disThreadPost');
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostAttachment');

/* get posts */
$c = $modx->newQuery('disPost');
$c->innerJoin('disThread','Thread');
$c->where(array(
    'thread' => $thread->get('id'),
));
$cc = clone $c;
$total = $modx->getCount('disPost',$cc);
if ($flat) {
    $c->sortby($modx->getSelectColumns('disPost','disPost','',array('createdon')),'ASC');
    $c->limit($limit, $start);
} else {
    $c->sortby($modx->getSelectColumns('disPost','disPost','',array('rank')),'ASC');
}


if (!empty($scriptProperties['post'])) {
    if (!is_object($scriptProperties['post'])) {
        $post = $modx->getObject('disPost',$scriptProperties['post']);
    } else {
        $post =& $scriptProperties['post'];
    }
    if ($post) {
        $c->where(array(
            'disPost.createdon:>=' => $post->get('createdon'),
        ));
    }
}

$c->bindGraph('{"Author":{},"EditedBy":{}}');
$posts = $modx->getCollectionGraph('disPost','{"Author":{},"EditedBy":{}}',$c);

/* setup basic settings/permissions */
$dateFormat = $modx->getOption('discuss.date_format',null,'%b %d, %Y, %H:%M %p');
$allowCustomTitles = $modx->getOption('discuss.allow_custom_titles',null,true);
$globalCanRemovePost = $modx->hasPermission('discuss.pm_remove');
$globalCanReplyPost = $modx->hasPermission('discuss.pm_send');
$globalCanModifyPost = true;
$canViewAttachments = $modx->hasPermission('discuss.view_attachments');
$canTrackIp = $modx->hasPermission('discuss.track_ip');
$canViewEmails = $modx->hasPermission('discuss.view_emails');
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

/* iterate */
$plist = array();
$output = array();
$idx = 0;
foreach ($posts as $post) {
    $postArray = $post->toArray();
    $postArray['url'] = $discuss->request->makeUrl('messages/view',array('thread' => $post->get('thread'))).'#dis-post-'.$post->get('id');
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
            $postArray['author.username_link'] = '<a href="'.$discuss->request->makeUrl('user',array('user' => $post->get('author'))).'">'.$post->Author->get('username').'</a>';
        } else {
            $postArray['author.username_link'] = '<span class="dis-username">'.$post->Author->get('username').'</span>';
        }

        /* set author avatar */
        $avatarUrl = $post->Author->getAvatarUrl();
        if (!empty($avatarUrl)) {
            $postArray['author.avatar'] = '<img class="dis-post-avatar" alt="'.$postArray['author'].'" src="'.$avatarUrl.'" />';
        }

        /* check if author wants to show email */
        if ($post->Author->get('show_email') && $discuss->user->isLoggedIn && $canViewEmails) {
            $postArray['author.email'] = '<a href="mailto:'.$post->Author->get('email').'">'.$modx->lexicon('discuss.email_author').'</a>';
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
    $postArray['title'] = str_replace(array('[',']'),array('&#91;','&#93;'),$postArray['title']);

    $postArray['class'] = array('dis-board-post');
    if (!$flat) {
        /* set depth and check max post depth */
        $postArray['class'][] = 'dis-depth-'.$postArray['depth'];
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
    $postArray['action_quote'] = '';
    $postArray['action_modify'] = '';
    $postArray['action_remove'] = '';
    if (!$thread->get('locked') && $discuss->user->isLoggedIn) {
        if ($globalCanReplyPost) {
            $postArray['action_reply'] = '<a href="'.$discuss->request->makeUrl('messages/reply',array('post' => $post->get('id'))).'" class="dis-post-reply">'.$modx->lexicon('discuss.reply').'</a>';
            $postArray['action_quote'] = '<a href="'.$discuss->request->makeUrl('messages/reply',array('post' => $post->get('id'),'quote' => 1)).'" class="dis-post-quote">'.$modx->lexicon('discuss.quote').'</a>';
        }

        $canModifyPost = $discuss->user->get('id') == $post->get('author') && $globalCanModifyPost;
        if ($canModifyPost) {
            $postArray['action_modify'] = '<a href="'.$discuss->request->makeUrl('messages/modify',array('post' => $post->get('id'))).'" class="dis-post-modify">'.$modx->lexicon('discuss.modify').'</a>';
        }

        $canRemovePost = $discuss->user->get('id') == $post->get('author') && $globalCanRemovePost;
        if ($canRemovePost) {
            $postArray['action_remove'] = '<a href="'.$discuss->request->makeUrl('messages/remove_post',array('post' => $post->get('id'))).'">'.$modx->lexicon('discuss.remove').'</a>';
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

    $postArray['createdon'] = strftime($dateFormat,strtotime($postArray['createdon']));
    $postArray['class'] = implode(' ',$postArray['class']);
    $postArray['report_link'] = '';
    $postArray['ip'] = '';
    $postArray['idx'] = $idx+1;
    
    if ($flat) {
        $output[] = $discuss->getChunk('post/disThreadPost',$postArray);
    } else {
        $plist[] = $postArray;
    }
    $idx++;
}
$response = array(
    'total' => $total,
    'start' => $start,
    'limit' => $limit,
);
if (empty($flat)) {
    /* parse posts via tree parser */
    $discuss->loadTreeParser();
    if (count($plist) > 0) {
        $output = $discuss->treeParser->parse($plist,'post/disThreadPost');
    }
} else {
    $output = implode("\n",$output);
}
$response['results'] = str_replace(array('[',']'),array('&#91;','&#93;'),$output);

/* mark as read */
$thread->read($discuss->user->get('id'));

return $response;
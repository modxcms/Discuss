<?php
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
$start = intval(isset($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0);

/* Verify the posts output type - Flat or Threaded */
$flat = $modx->getOption('flat',$scriptProperties,false);
$flat = true;
/* get default properties */
$postTpl = $modx->getOption('postTpl',$scriptProperties,'post/disThreadPost');
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostAttachment');

$isAdmin = $discuss->user->isAdmin();
$isModerator = $discuss->user->isGlobalModerator() || $thread->isModerator($discuss->user->get('id')) || $discuss->user->isAdmin();
$userUrl = $discuss->url.'user/';

/* get posts */
$post = $modx->getOption('post',$scriptProperties,false);
$posts = $thread->fetchPosts($post,$limit,$start,$flat);

/* setup basic settings/permissions */
$dateFormat = $modx->getOption('discuss.date_format',null,'%b %d, %Y, %H:%M %p');
$allowCustomTitles = $modx->getOption('discuss.allow_custom_titles',null,true);
$canModifyPost = $modx->hasPermission('discuss.thread_modify');
$canRemovePost = $modx->hasPermission('discuss.thread_remove');
$canViewAttachments = $modx->hasPermission('discuss.view_attachments');
$canTrackIp = $modx->hasPermission('discuss.track_ip');
$canViewEmails = $modx->hasPermission('discuss.view_emails');
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');
$canReportPost = $modx->hasPermission('discuss.thread_report');

/* iterate */
$plist = array();
$output = array();
foreach ($posts['results'] as $post) {
    $postArray = $post->toArray();
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
            $postArray['author.username_link'] = '<a href="'.$userUrl.'?user='.$post->get('author').'">'.$post->Author->get('username').'</a>';
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
            $postArray['author.email'] = disPost::encodeEmail($post->Author->get('email'),$modx->lexicon('discuss.email_author'));
        } else {
            $postArray['author.email'] = '';
        }
    }

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
    if (($isAdmin || $isModerator || !$thread->get('locked')) && $discuss->user->isLoggedIn) {
        if ($post->canReply()) {
            $postArray['action_reply'] = '<a href="'.$discuss->url.'thread/reply?post='.$post->get('id').'" class="dis-post-reply">'.$modx->lexicon('discuss.reply').'</a>';
            $postArray['action_quote'] = '<a href="'.$discuss->url.'thread/reply?post='.$post->get('id').'&quote=1" class="dis-post-quote">'.$modx->lexicon('discuss.quote').'</a>';
        }

        $canModifyPost = $discuss->user->get('id') == $post->get('author') || ($isModerator && $canModifyPost);
        if ($canModifyPost) {
            $postArray['action_modify'] = '<a href="'.$discuss->url.'thread/modify?post='.$post->get('id').'" class="dis-post-modify">'.$modx->lexicon('discuss.modify').'</a>';
        }

        $canRemovePost = $discuss->user->get('id') == $post->get('author') || ($isModerator && $canRemovePost);
        if ($canRemovePost) {
            $postArray['action_remove'] = '<a href="'.$discuss->url.'post/remove?post='.$post->get('id').'">'.$modx->lexicon('discuss.remove').'</a>';
            $postArray['action_remove'] .= '<a href="'.$discuss->url.'post/spam?post='.$post->get('id').'">'.$modx->lexicon('discuss.post_spam').'</a>';
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
    if ($canReportPost) {
        $postArray['report_link'] = '<a class="dis-report-link" href="'.$discuss->url.'post/report?thread='.$postArray['thread'].'&post='.$postArray['id'].'">'.$modx->lexicon('discuss.report_to_mod').'</a>';
    } else {
        $postArray['report_link'] = '';
    }
    
    if (!$isModerator || !$canTrackIp) {
        $postArray['ip'] = '';
    }
    
    if ($flat) {
        $output[] = $discuss->getChunk('post/disThreadPost',$postArray);
    } else {
        $plist[] = $postArray;
    }
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
        $output = $discuss->treeParser->parse($plist,'post/disThreadPost');
    }
} else {
    $output = implode("\n",$output);
}
$response['results'] = str_replace(array('[',']'),array('&#91;','&#93;'),$output);
return $response;
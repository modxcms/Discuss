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

$limit = (int)$modx->getOption('discuss.post_per_page',$scriptProperties, 10);
$start = intval(isset($_GET['page']) ? ($_GET['page'] - 1) * $limit : 0);

/* Verify the posts output type - Flat or Threaded */
$flat = $modx->getOption('flat',$scriptProperties,false);
$flat = true;
/* get default properties */
$postTpl = $modx->getOption('postTpl',$scriptProperties,'post/disThreadPost');
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostAttachment');

$isModerator = $modx->getCount('disModerator',array(
    'user' => $modx->user->get('id'),
    'board' => $thread->get('board'),
)) > 0 ? true : false;
$isAuthenticated = $modx->user->isAuthenticated();
$currentResourceUrl = $discuss->url;
$userUrl = $currentResourceUrl.'user/';

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
$c->bindGraph('{"Author":{},"EditedBy":{}}');
$c->prepare();
//$cacheKey = 'discuss/thread/'.$thread->get('id').'/'.md5($c->toSql());

$posts = $modx->getCollectionGraph('disPost','{"Author":{},"EditedBy":{}}',$c);

$dateFormat = $modx->getOption('discuss.date_format',null,'%b %d, %Y, %H:%M %p');
/* iterate */
$plist = array();
$output = array();
foreach ($posts as $post) {
    $postArray = $post->toArray();

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
        $postArray['author.username_link'] = '<a href="'.$userUrl.'?user='.$post->get('author').'">'.$post->Author->get('username').'</a>';

        /* set author avatar */
        $avatarUrl = $post->Author->getAvatarUrl();
        if (!empty($avatarUrl)) {
            $postArray['author.avatar'] = '<img class="dis-post-avatar" alt="'.$postArray['author'].'" src="'.$avatarUrl.'" />';
        }

        /* check if author wants to show email */
        if ($post->Author->get('show_email') && $discuss->isLoggedIn) {
            $postArray['author.email'] = '<a href="mailto:'.$post->Author->get('email').'">'.$modx->lexicon('discuss.email_author').'</a>';
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
    if (!$modx->getOption('discuss.allow_custom_titles',null,true)) {
        $postArray['author.title'] = '';
    }

    /* load actions */
    $postArray['action_reply'] = '';
    $postArray['action_modify'] = '';
    $postArray['action_remove'] = '';
    if (!$thread->get('locked') && $discuss->isLoggedIn) {
        $postArray['action_reply'] = '<a href="'.$currentResourceUrl.'thread/reply?post='.$post->get('id').'" class="dis-post-reply">'.$modx->lexicon('discuss.reply').'</a>';

        $canModifyPost = $modx->user->get('id') == $post->get('author') || $isModerator;
        if ($canModifyPost) {
            $postArray['action_modify'] = '<a href="'.$currentResourceUrl.'thread/modify?post='.$post->get('id').'" class="dis-post-modify">'.$modx->lexicon('discuss.modify').'</a>';
        }

        $canRemovePost = $modx->user->get('id') == $post->get('author') || $isModerator;
        if ($canRemovePost) {
            $postArray['action_remove'] = '<a class="dis-post-remove">'.$modx->lexicon('discuss.remove').'</a>';
        }
    }

    /* get attachments */
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
    $postArray['createdon'] = strftime($dateFormat,strtotime($postArray['createdon']));

    $postArray['class'] = implode(' ',$postArray['class']);
    
    if ($flat) {
        $output[] = $discuss->getChunk('post/disThreadPost',$postArray);
    } else {
        $plist[] = $postArray;
    }
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
return $response;
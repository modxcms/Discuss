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

/* Verify the posts output type - Flat or Threaded */
$flat = $modx->getOption('flat',$scriptProperties,false);
$flat = true;
if ($flat) {
    $postPerPage = $modx->getOption('discuss.post_per_page',$scriptProperties, 10);
    $param = $modx->getOption('discuss.page_param',$scriptProperties,'page');
    $start = isset($_GET[$param]) ? ($_GET[$param] - 1) * $postPerPage : 0;
}

/* get default properties */
$postTpl = $modx->getOption('postTpl',$scriptProperties,'post/disThreadPost');
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostAttachment');

$isModerator = $modx->getCount('disModerator',array(
    'user' => $modx->user->get('id'),
    'board' => $thread->get('board'),
)) > 0 ? true : false;
$isAuthenticated = $modx->user->isAuthenticated();
$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'));
$userUrl = $currentResourceUrl.'user/';

/* get posts */
$c = $modx->newQuery('disPost');
$c->innerJoin('disThread','Thread');
$c->where(array(
    'thread' => $thread->get('id'),
));
if ($flat) {
    $ct = clone $c;
    if ($ct->prepare() && $ct->stmt->execute()) {
        $total = $ct->stmt->rowCount();
        $count = ceil($total/$postPerPage);
    }
    $modx->hooks->load('pagination/build',array(
        'total' => $count,
        'id' => $thread->get('id'),
        'view' => 'thread',
        'limit' => $postPerPage,
        'param' => $param,
    ));
    unset($ct,$count);

    $c->sortby($modx->getSelectColumns('disPost','disPost','',array('id')),'ASC');
    $c->limit($postPerPage, $start);
    $c->limit(10,0);
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
foreach ($posts as $post) {
    $postArray = $post->toArray('',true);

    if ($post->Author) {
        $postArray = array_merge($postArray,$post->Author->toArray('author.'));
        $postArray['author.signature'] = $post->Author->parseSignature();
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

    if (!$flat) {
        /* set depth and check max post depth */
        $postArray['class'] = 'dis-board-post dis-depth-'.$postArray['depth'];
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
    if (!$thread->get('locked') && $isAuthenticated) {
        $postArray['action_reply'] = '<a href="'.$currentResourceUrl.'thread/reply?post=[[+id]]" class="dis-post-reply">'.$modx->lexicon('discuss.reply').'</a>';

        $canModifyPost = $modx->user->get('id') == $post->get('author') || $isModerator;
        if ($canModifyPost) {
            $postArray['action_modify'] = '<a href="'.$currentResourceUrl.'thread/modify?post=[[+id]]" class="dis-post-modify">'.$modx->lexicon('discuss.modify').'</a>';
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

    if ($flat) {
        $output[] = $discuss->getChunk('post/disThreadPost',$postArray);
    } else {
        $plist[] = $postArray;
    }
}
if (empty($flat)) {
    /* parse posts via tree parser */
    $discuss->loadTreeParser();
    if (count($plist) > 0) {
        $output = $discuss->treeParser->parse($plist,'post/disThreadPost');
    }
} else {
    $output = implode("\n",$output);
}
$output = str_replace(array('[',']'),array('&#91;','&#93;'),$output);
return $output;
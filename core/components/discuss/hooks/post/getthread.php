<?php
/**
 * Get a threaded view of a post.
 *
 * @package discuss
 * @subpackage hooks
 */
if (empty($scriptProperties['post']) && empty($scriptProperties['thread'])) return false;
$post = !empty($scriptProperties['post']) ? $scriptProperties['post'] : $scriptProperties['thread'];

/* Verify the posts output type - Flat or Threaded */
$flat = $modx->getOption('flat',$scriptProperties,false);
if ($flat) {
    $postPerPage = $modx->getOption('discuss.post_per_page',$scriptProperties, 10);
    $param = $modx->getOption('discuss.page_param',$scriptProperties,'page');
    $start = isset($_GET[$param]) ? ($_GET[$param] - 1) * $postPerPage : 0;
}

/* get default properties */
$postTpl = $modx->getOption('postTpl',$scriptProperties,'post/disThreadPost');
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'post/disPostAttachment');

/* get thread or root of post */
if (empty($scriptProperties['thread'])) {
    $thread = $post->getThreadRoot();
} else { $thread = $scriptProperties['thread']; }
if (empty($thread)) $modx->sendErrorPage();

$isModerator = $modx->getCount('disModerator',array(
    'user' => $modx->user->get('id'),
    'board' => $thread->get('board'),
)) > 0 ? true : false;

/* get posts */
$c = $modx->newQuery('disPost');
$c->where(array(
    'Descendants.ancestor' => $post->get('id'),
));
if ($flat){
    $ct = clone $c;
    if ($ct->prepare() && $ct->stmt->execute()) {
        $total = $ct->stmt->rowCount();
        $count = ceil($total/$postPerPage);
    }
    $modx->hooks->load('pagination/build',array(
        'total' => $count,
        'id' => $post->get('id'),
        'view' => 'thread',
        'limit' => $postPerPage,
        'param' => $param,
    ));
    unset($ct,$count);

    $c->sortby($modx->getSelectColumns('disPost','disPost','',array('id')),'ASC');
    $c->limit($postPerPage, $start);
} else {
    $c->sortby($modx->getSelectColumns('disPost','disPost','',array('rank')),'ASC');
}
$c->bindGraph('{"Author":{},"AuthorProfile":{},"Descendants":{},"EditedBy":{}}');
$c->groupBy($modx->getSelectColumns('disPost','disPost','',array('id')));
$posts = $modx->getCollectionGraph('disPost','{"Author":{},"AuthorProfile":{},"Descendants":{},"EditedBy":{}}',$c);
$isAuthenticated = $modx->user->isAuthenticated();

$plist = array();
$currentResourceUrl = $modx->makeUrl($modx->resource->get('id'));
$userUrl = $currentResourceUrl.'user/';
foreach ($posts as $post) {
    $postArray = $post->toArray('',true);
    $postArray = array_merge($postArray,$post->AuthorProfile->toArray('author.'));
    $postArray = array_merge($postArray,$post->Author->toArray('author.'));
    unset($postArray['author.password'],$postArray['author.cachepwd']);
    if (!empty($post->EditedBy)) {
        $postArray = array_merge($postArray,$post->EditedBy->toArray('editedby.'));
        unset($postArray['editedby.password'],$postArray['editedby.cachepwd']);
    }
    
    $postArray['author.username_link'] = '<a href="'.$userUrl.'?user='.$post->get('author').'">'.$post->Author->get('username').'</a>';

    /* set author avatar */
    $avatarUrl = $post->AuthorProfile->getAvatarUrl();
    if (!empty($avatarUrl)) {
        $postArray['author.avatar'] = '<img class="dis-post-avatar" alt="'.$postArray['author'].'" src="'.$avatarUrl.'" />';
    }
    
    /* check if author wants to show email */
    if ($post->AuthorProfile->get('show_email') && $isAuthenticated) {
        $postArray['author.email'] = '<a href="mailto:'.$post->AuthorProfile->get('email').'">'.$modx->lexicon('discuss.email_author').'</a>';
    } else {
        $postArray['author.email'] = '';
    }

    if (!$flat && !empty($post->Descendants)) {
        $desc = array_pop($post->Descendants);
        
        /* set depth and check max post depth */
        $postArray['depth'] = $desc->get('depth');
        $postArray['class'] = 'dis-board-post dis-depth-'.$desc->get('depth');
        if ($desc->get('depth') > $modx->getOption('discuss.max_post_depth',null,3)) {
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
            $postArray['attachments'][] = $discuss->getChunk($postAttachmentRowTpl,$attachmentArray);
        }
        $postArray['attachments'] = implode("\n",$postArray['attachments']);
    }
    if ($flat) {
        $output[] = $this->discuss->getChunk($postTpl,$pa);
    } else {
        $plist[] = $postArray;
    }
}

if (empty($flat)) {
    /* parse posts via tree parser */
    $discuss->loadTreeParser();
    if (count($plist) > 0) {
        $output = $discuss->treeParser->parse($plist,$postTpl);
    }
} else {
    $output = implode("\n",$output);
}
return $output;
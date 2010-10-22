<?php
/**
 * Get a threaded view of a post.
 *
 * @package discuss
 * @subpackage hooks
 */
if (empty($scriptProperties['post']) && empty($scriptProperties['thread'])) return false;
$post = !empty($scriptProperties['post']) ? $scriptProperties['post'] : $scriptProperties['thread'];

/* get default properties */
$postTpl = $modx->getOption('postTpl',$scriptProperties,'disThreadPost');
$postAttachmentRowTpl = $modx->getOption('postAttachmentRowTpl',$scriptProperties,'disPostAttachment');

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
$c->select('
    disPost.*,
    Descendants.depth AS depth,
    Author.username AS username,
    AuthorProfile.posts AS author_posts,
    AuthorProfile.title AS author_title,
    AuthorProfile.avatar AS author_avatar,
    AuthorProfile.signature AS author_signature,
    AuthorProfile.ip AS author_ip,
    AuthorProfile.location AS author_location,
    AuthorProfile.email AS author_email,
    AuthorProfile.show_email AS author_show_email,
    AuthorProfile.show_online AS author_show_online,
    EditedBy.username AS editedby_username
');
$c->innerJoin('disPostClosure','Descendants');
$c->innerJoin('disPostClosure','Ancestors');
$c->innerJoin('modUser','Author');
$c->leftJoin('modUser','EditedBy');
$c->innerJoin('disUserProfile','AuthorProfile');
$c->where(array(
    'Descendants.ancestor' => $post->get('id'),
));
$c->sortby('disPost.rank','ASC');
$posts = $modx->getCollection('disPost',$c);

$isAuthenticated = $modx->user->isAuthenticated();

$plist = array();
$userUrl = $modx->makeUrl($modx->getOption('discuss.user_resource'));
$profileUrl = $modx->getOption('discuss.files_url').'/profile/';
foreach ($posts as $post) {
    $pa = $post->toArray('',true);
    $pa['username'] = '<a href="'.$userUrl.'?user='.$post->get('author').'">'.$post->get('username').'</a>';

    /* set author avatar */
    if (!empty($pa['author_avatar'])) {
        $pa['author_avatar'] = '<img class="dis-post-avatar" alt="'.$pa['author'].'"
            src="'.$profileUrl.$pa['author'].'/'.$pa['author_avatar'].'" />';
    }
    /* check if author wants to show email */
    if (!empty($pa['author_show_email']) && $isAuthenticated) {
        $pa['author_email'] = '<a href="mailto:'.$post->get('author_email').'">'.$modx->lexicon('discuss.email_author').'</a>';
    } else {
        $pa['author_email'] = '';
    }

    /* set depth and check max post depth */
    $pa['class'] = 'dis-board-post dis-depth-'.$pa['depth'];
    if ($post->get('depth') > $modx->getOption('discuss.max_post_depth',null,3)) {
         /* Don't hide post if it exceed max depth, set its depth placeholder to max depth value instead */
        $pa['depth'] = $modx->getOption('discuss.max_post_depth',null,3);
    }
    /* format bbcode */
    $pa['content'] = $post->getContent();

    /* check allowing of custom titles */
    if (!$modx->getOption('discuss.allow_custom_titles',null,true)) {
        $pa['author_title'] = '';
    }

    /* load actions */
    if (!$thread->get('locked') && $isAuthenticated) {
        $pa['action_reply'] = '<a href="[[~[[++discuss.reply_post_resource]]? &post=`[[+id]]`]]" class="dis-post-reply">'.$modx->lexicon('discuss.reply').'</a>';

        $canModifyPost = $modx->user->get('id') == $post->get('author') || $isModerator;
        if ($canModifyPost) {
            $pa['action_modify'] = '<a href="[[~[[++discuss.modify_post_resource]]? &post=`[[+id]]`]]" class="dis-post-modify">'.$modx->lexicon('discuss.modify').'</a>';
        }

        $canRemovePost = $modx->user->get('id') == $post->get('author') || $isModerator;
        if ($canRemovePost) {
            $pa['action_remove'] = '<a class="dis-post-remove">'.$modx->lexicon('discuss.remove').'</a>';
        }
    }

    /* get attachments */
    $attachments = $post->getMany('Attachments');
    if (!empty($attachments)) {
        $pa['attachments'] = '';
        foreach ($attachments as $attachment) {
            $attachmentArray = $attachment->toArray();
            $attachmentArray['filesize'] = $attachment->convert();
            $attachmentArray['url'] = $attachment->getUrl();
            $pa['attachments'] .= $discuss->getChunk($postAttachmentRowTpl,$attachmentArray);
        }
    }

    $plist[] = $pa;
}

/* parse posts via tree parser */
$discuss->loadTreeParser();
if (count($plist) > 0) {
    return $discuss->treeParser->parse($plist,$postTpl);
}
return '';

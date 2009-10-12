<?php
/**
 * Get a threaded view of a post.
 *
 * @package discuss
 * @subpackage hooks
 */
if (empty($scriptProperties['post']) && empty($scriptProperties['thread'])) return false;

$post = !empty($scriptProperties['post']) ? $scriptProperties['post'] : $scriptProperties['thread'];

if (empty($scriptProperties['thread'])) {
    $thread = $post->getThreadRoot();
} else { $thread = $scriptProperties['thread']; }
if (empty($thread)) $modx->sendErrorPage();

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
    AuthorProfile.show_online AS author_show_online
');
$c->innerJoin('disPostClosure','Descendants');
$c->innerJoin('disPostClosure','Ancestors');
$c->innerJoin('modUser','Author');
$c->innerJoin('disUserProfile','AuthorProfile');
$c->where(array(
    'Descendants.ancestor' => $post->get('id'),
));
$c->sortby('disPost.rank','ASC');
$posts = $modx->getCollection('disPost',$c);

$plist = array();
$userUrl = $modx->makeUrl($modx->getOption('discuss.user_resource'));
$profileUrl = $modx->getOption('discuss.files_url').'/profile/';
foreach ($posts as $post) {
    $pa = $post->toArray();
    $pa['username'] = '<a href="'.$userUrl.'?user='.$post->get('author').'">'.$post->get('username').'</a>';

    /* set author avatar */
    if (!empty($pa['author_avatar'])) {
        $pa['author_avatar'] = '<img class="dis-post-avatar" alt="'.$pa['author'].'"
            src="'.$profileUrl.$pa['author'].'/'.$pa['author_avatar'].'" />';
    }
    /* check if author wants to show email */
    if (!empty($pa['author_show_email'])) {
        $pa['author_email'] = '<a href="mailto:'.$post->get('author_email').'">'.$modx->lexicon('discuss.email_author').'</a>';
    } else {
        $pa['author_email'] = '';
    }

    /* set depth and check max post depth */
    $pa['class'] = 'dis-board-post dis-depth-'.$pa['depth'];
    if ($post->get('depth') > $modx->getOption('discuss.max_post_depth',null,3)) {
        $pa['class'] .= ' dis-collapsed';
    }
    /* format bbcode */
    $pa['content'] = $post->getContent();

    /* check allowing of custom titles */
    if (!$modx->getOption('discuss.allow_custom_titles',null,true)) {
        $pa['author_title'] = '';
    } else {
        $pa['author_title'] = ' - '.$pa['author_title'];
    }

    /* load actions */
    if (!$thread->get('locked')) {
        $pa['action_reply'] = '<a href="[[~[[++discuss.reply_post_resource]]]]?post=[[+id]]" class="dis-post-reply">'.$modx->lexicon('discuss.reply').'</a>';
        $pa['action_modify'] = '<a href="[[~[[++discuss.modify_post_resource]]]]?post=[[+id]]" class="dis-post-modify">'.$modx->lexicon('discuss.modify').'</a>';
        $pa['action_remove'] = '<a class="dis-post-remove">'.$modx->lexicon('discuss.remove').'</a>';
    }

    /* get attachments */
    $attachments = $post->getMany('Attachments');
    if (!empty($attachments)) {
        $attTpl = '<ul class="dis-attachments">';
        foreach ($attachments as $attachment) {
            $attachmentArray = $attachment->toArray();
            $attachmentArray['filesize'] = $attachment->convert();
            $attachmentArray['url'] = $attachment->getUrl();
            $attTpl .= $discuss->getChunk('disPostAttachment',$attachmentArray);
        }
        $attTpl .= '</ul>';
        $pa['attachments'] = $attTpl;
    }


    $plist[] = $pa;
}

/* parse posts via tree parser */
$discuss->loadTreeParser();
if (count($plist) <= 0) {
    $postsOutput = '<p>'.$modx->lexicon('discuss.thread_no_posts').'</p>';
} else {
    $postsOutput = $discuss->treeParser->parse($plist,'disThreadPost');
}
unset($pa,$plist,$userUrl,$profileUrl);

return $postsOutput;

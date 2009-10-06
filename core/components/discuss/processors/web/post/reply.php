<?php
/**
 * Post a reply to a post via AJAX
 *
 * @package discuss
 */
if (empty($_POST['post'])) return $modx->error->failure('Parent Post not specified');
$parent = $modx->getObject('disPost',$_POST['post']);
if ($parent == null) return $modx->error->failure('Parent Post not found.');

$_POST['message'] = substr($_POST['message'],$modx->getOption('discuss.maximum_post_size',null,30000));

$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',$parent->get('id'));
$post->set('board',$parent->get('board'));
$post->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
$post->set('ip',$_SERVER['REMOTE_ADDR']);

if ($post->save() == false) {
    return $modx->error->failure('An error occurred while trying to post a reply.');
}

/* now output html back to browser */
$author = $post->getOne('Author');
$post->set('username',$author->get('username'));

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
    AuthorProfile.email AS author_email

');
$c->innerJoin('disPostClosure','Descendants');
$c->innerJoin('disPostClosure','Ancestors');
$c->innerJoin('modUser','Author');
$c->innerJoin('disUserProfile','AuthorProfile');
$c->where(array(
    'id' => $post->get('id'),
));
$newPost = $modx->getObject('disPost',$c);

$pa = $newPost->toArray();

$pa['username'] = '<a href="'.$userUrl.'?user='.$post->get('author').'">'.$post->get('username').'</a>';
if (!empty($pa['author_avatar'])) {
    $pa['author_avatar'] = '<img
        class="dis-post-avatar" alt=""
        src="'.$modx->getOption('discuss.assets_url').'/profile/'.$pa['author'].'/'.$pa['author_avatar'].'" />';
}
$pa['content'] = $newPost->getContent();

$o = $discuss->getChunk('disThreadPost',$pa);

$o = '<li class="dis-board-post" id="dis-board-post-'.$newPost->get('id').'">'.$o.'</li>';

return $modx->error->success($o,$newPost);
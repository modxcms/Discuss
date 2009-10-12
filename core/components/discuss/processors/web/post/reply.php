<?php
/**
 * Post a reply to a post via AJAX
 *
 * @package discuss
 */
if (empty($_POST['post'])) return $modx->error->failure('Parent Post not specified');
$parent = $modx->getObject('disPost',$_POST['post']);
if ($parent == null) return $modx->error->failure('Parent Post not found.');

$thread = $parent->getThreadRoot();
if ($thread == null) return $modx->error->failure('Thread not found.');

/* validation */
if (empty($_POST['title'])) { $modx->error->addField('title','Please enter a title for this post.'); }
if (empty($_POST['message'])) { $modx->error->addField('message','Please enter a valid message.'); }

if ($modx->error->hasError()) {
    return $modx->error->failure('Please correct the errors in your form.');
}

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

if (!empty($_POST['notify'])) {
    $notify = $modx->newObject('disUserNotification');
    $notify->set('user',$modx->user->get('id'));
    $notify->set('post',$post->get('id'));
    $notify->save();
}

/* send out notifications */
$modx->hooks->load('notifications/send',array(
    'board' => $board->get('id'),
    'thread' => $thread->get('id'),
    'title' => $thread->get('title'),
    'subject' => '[Discuss] A New Post Has Been Made',
));

$c = $modx->newQuery('dhUserNotification');
$c->where(array(
    'post' => $thread->get('id'),
));
$c->orCondition(array(
    'board' => $thread->get('board'),
));
$notifications = $modx->getCollection('dhUserNotification',$c);
foreach ($notifications as $notification) {
    $user = $notification->getOne('User');
    if ($user == null) { $notification->remove(); continue; }
    $profile = $notification->getOne('UserProfile');
    if ($profile == null) { $notification->remove(); continue; }

    $subject = '[Discuss] A Reply Has Been Made';
    $emailProperties = $user->toArray();
    $emailProperties = array_merge($emailProperties,$profile->toArray());
    $emailProperties['tpl'] = 'disNotificationEmail';
    $emailProperties['type'] = 'post';
    $emailProperties['name'] = $thread->get('title');
    $emailProperties['url'] = $modx->makeUrl($modx->getOption('discuss.thread_resource')).'?thread='.$thread->get('id');
    $sent = $discuss->sendEmail($profile->get('email'),$user->get('username'),$subject,$emailProperties);
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
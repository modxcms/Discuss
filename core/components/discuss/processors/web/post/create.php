<?php
/**
 * @package discuss
 * @subpackage processors
 */
$errors = array();
if (empty($_POST['title'])) $errors['title'] = 'Please enter a valid post title.';
if (empty($_POST['message'])) $errors['message'] = 'Please enter a message.';

if (empty($errors)) {
    $_POST['message'] = substr($_POST['message'],$modx->getOption('discuss.maximum_post_size',null,30000));

    $post = $modx->newObject('disPost');
    $post->fromArray($_POST);
    $post->set('author',$modx->user->get('id'));
    $post->set('parent',0);
    $post->set('board',$board->get('id'));
    $post->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
    $post->set('ip',$_SERVER['REMOTE_ADDR']);

    $post->save();

    /* send out notifications */
    $notifications = $modx->getCollection('dhUserNotification',array('board' => $board->get('id')));
    foreach ($notifications as $notification) {
        $user = $notification->getOne('User');
        if ($user == null) { $notification->remove(); continue; }
        $profile = $notification->getOne('UserProfile');
        if ($profile == null) { $notification->remove(); continue; }

        $subject = '[Discuss] A New Post Has Been Made';
        $emailProperties = $user->toArray();
        $emailProperties = array_merge($emailProperties,$profile->toArray());
        $emailProperties['tpl'] = 'disNotificationEmail';
        $emailProperties['type'] = 'board';
        $emailProperties['name'] = $thread->get('title');
        $emailProperties['url'] = $modx->makeUrl($modx->getOption('discuss.thread_resource')).'?thread='.$thread->get('id');
        $sent = $discuss->sendEmail($profile->get('email'),$user->get('username'),$subject,$emailProperties);
    }


    $url = $modx->makeUrl($modx->getOption('discuss.board_resource')).'?board='.$board->get('id');
    $modx->sendRedirect($url);
    return true;
}
$modx->toPlaceholders($errors,'error');

return false;
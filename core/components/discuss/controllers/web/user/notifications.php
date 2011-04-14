<?php
/**
 *
 * @package discuss
 */
/* get user */
if (empty($scriptProperties['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$scriptProperties['user']);
if ($user == null) { $modx->sendErrorPage(); }

$modx->lexicon->load('discuss:user');
$placeholders = $user->toArray();

/* handle unsubscribing */
if (!empty($_POST) && !empty($_POST['remove'])) {
    foreach ($_POST['remove'] as $threadId) {
        $notification = $modx->getObject('disUserNotification',array('thread' => $threadId));
        if ($notification == null) continue;
        $notification->remove();
    }
    $url = $discuss->url.'user/notifications?user='.$user->get('id');
    $modx->sendRedirect($url);
}

/* get notifications */
$c = $modx->newQuery('disThread');
$c->select($modx->getSelectColumns('disThread','disThread'));
$c->select(array(
    'first_post_id' => 'FirstPost.id',
    'last_post_id' => 'LastPost.id',
    'title' => 'FirstPost.title',
    'createdon' => 'LastPost.createdon',
    'board_name' => 'Board.name',
    'author' => 'FirstPost.author',
    'author_username' => 'FirstAuthor.username',
));
$c->innerJoin('disUserNotification','Notifications');
$c->innerJoin('disUser','FirstAuthor');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disBoard','Board');
$c->where(array(
    'Notifications.user' => $user->get('id'),
));
$c->sortby('FirstPost.title','ASC');
$notifications = $modx->getCollection('disThread',$c);
$placeholders['notifications'] = array();
foreach ($notifications as $notification) {
    $notificationArray = $notification->toArray();
    $notificationArray['class'] = 'dis-board-li';
    $notificationArray['createdon'] = strftime($discuss->dateFormat,strtotime($notificationArray['createdon']));
    $placeholders['notifications'][] = $discuss->getChunk('user/disUserNotificationRow',$notificationArray);
}
$placeholders['notifications'] = implode("\n",$placeholders['notifications']);

/* output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;
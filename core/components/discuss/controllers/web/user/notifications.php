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

/* get default properties */
$cssRowCls = $modx->getOption('cssRowCls',$scriptProperties,'dis-board-li');
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');
$rowTpl = $modx->getOption('rowTpl',$scriptProperties,'disUserNotificationRow');

$placeholders = $user->toArray();

/* handle unsubscribing */
if (!empty($_POST) && !empty($_POST['remove'])) {
    foreach ($_POST['remove'] as $postId) {
        $notification = $modx->getObject('disUserNotification',array('post' => $postId));
        if ($notification == null) continue;
        $notification->remove();
    }
    $url = $discuss->url.'?user='.$user->get('id');
    $modx->sendRedirect($url);
}

/* get notifications */
$c = $modx->newQuery('disPost');
$c->select($modx->getSelectColumns('disPost','disPost'));
$c->select(array(
    'board_name' => 'Board.name',
    'author_username' => 'Author.username',
));
$c->innerJoin('disUserNotification','Notifications');
$c->innerJoin('modUser','Author');
$c->innerJoin('disBoard','Board');
$c->where(array(
    'Notifications.user' => $user->get('id'),
));
$c->sortby('disPost.title','ASC');
$notifications = $modx->getCollection('disPost',$c);
$placeholders['notifications'] = '';
foreach ($notifications as $notification) {
    $notificationArray = $notification->toArray();
    $notificationArray['class'] = $cssRowCls;
    $placeholders['notifications'] .= $discuss->getChunk($rowTpl,$notificationArray);
}


/* output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));
return $placeholders;
<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

/* get user */
if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('modUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }

$modx->lexicon->load('discuss:user');

/* get default properties */
$cssRowCls = $modx->getOption('cssRowCls',$scriptProperties,'dis-board-li');
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');
$rowTpl = $modx->getOption('rowTpl',$scriptProperties,'disUserNotificationRow');

$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));

$placeholders = $user->toArray();
$placeholders = array_merge($user->profile->toArray(),$placeholders);

/* handle unsubscribing */
if (!empty($_POST) && !empty($_POST['remove'])) {
    foreach ($_POST['remove'] as $postId) {
        $notification = $modx->getObject('disUserNotification',array('post' => $postId));
        if ($notification == null) continue;
        $notification->remove();
    }
    $url = $modx->makeUrl($modx->resource->get('id')).'?user='.$user->get('id');
    $modx->sendRedirect($url);
}

/* get notifications */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Board.name AS board_name,
    Author.username AS author_username
');
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
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/user/dis.user.notifications.js');
$modx->setPlaceholder('usermenu',$discuss->getChunk($menuTpl,$placeholders));
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $discuss->output('user/notifications',$placeholders);
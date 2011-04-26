<?php
/**
 * Send out all notifications for a post
 * @package discuss
 * @subpackage hooks
 */
if (empty($scriptProperties['title']) || empty($scriptProperties['thread'])) return false;

/* setup default properties */
$type = $modx->getOption('type',$scriptProperties,'thread');
$subject = $modx->getOption('subject',$scriptProperties,$modx->getOption('discuss.notification_new_post_subject',null,'New Post'));
$tpl = $modx->getOption('tpl',$scriptProperties,$modx->getOption('discuss.notification_new_post_chunk',null,'emails/disNotificationEmail'));

/* get notification subscriptions */
$c = $modx->newQuery('disUserNotification');
$c->where(array(
    'thread' => $scriptProperties['thread'],
));
if (!empty($scriptProperties['board'])) {
    $c->orCondition(array(
        'board' => $scriptProperties['board'],
    ));
}
$notifications = $modx->getCollection('disUserNotification',$c);

/* build thread url */
$url = $type == 'message' ? 'messages/view' : 'thread';
$url = $modx->makeUrl($modx->resource->get('id'),'','','full').$url.'?thread='.$scriptProperties['thread'];
if (!empty($scriptProperties['post'])) {
    $url .= '#dis-post-'.$scriptProperties['post'];
}

/* send out notifications */
foreach ($notifications as $notification) {
    $user = $notification->getOne('User');
    if ($user == null) { $notification->remove(); continue; }

    $emailProperties = array_merge($scriptProperties,$user->toArray());
    $emailProperties['tpl'] = $tpl;
    $emailProperties['name'] = $scriptProperties['title'];
    $emailProperties['type'] = $type;
    $emailProperties['url'] = $url;
    $sent = $discuss->sendEmail($user->get('email'),$user->get('username'),$subject,$emailProperties);
    unset($emailProperties);
}

return true;
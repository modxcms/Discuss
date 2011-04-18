<?php
/**
 * Send out all notifications for a post
 * @package discuss
 * @subpackage hooks
 */
if (empty($scriptProperties['title']) || empty($scriptProperties['thread'])) return false;

/* setup default properties */
$type = $modx->getOption('type',$scriptProperties,'post');
$subject = $modx->getOption('subject',$scriptProperties,$modx->getOption('discuss.notification_new_post_subject'));
$tpl = $modx->getOption('tpl',$scriptProperties,$modx->getOption('discuss.notification_new_post_chunk',null,'emails/disNotificationEmail'));

/* get notification subscriptions */
$c = $modx->newQuery('dhUserNotification');
$c->where(array(
    'thread' => $scriptProperties['thread'],
));
if (!empty($scriptProperties['board'])) {
    $c->orCondition(array(
        'board' => $scriptProperties['board'],
    ));
}
$notifications = $modx->getCollection('dhUserNotification',$c);
foreach ($notifications as $notification) {
    $user = $notification->getOne('User');
    if ($user == null) { $notification->remove(); continue; }

    $emailProperties = $user->toArray();
    $emailProperties['tpl'] = $tpl;
    $emailProperties['name'] = $scriptProperties['title'];
    $emailProperties['url'] = $modx->makeUrl($modx->resource->get('id'),'','','full').'?thread='.$scriptProperties['thread'];
    $sent = $discuss->sendEmail($user->get('email'),$user->get('username'),$subject,$emailProperties);
    unset($emailProperties);
}

return true;
<?php
/**
 * @package discuss
 * @subpackage processors
 */
$thread = $modx->getObject('disPost',$scriptProperties['id']);
if ($thread == null) return $modx->error->failure();

$userId = $modx->user->get('id');
if (empty($userId)) return $modx->error->failure();

$notify = $modx->newObject('disUserNotification');
$notify->set('user',$modx->user->get('id'));
$notify->set('post',$thread->get('id'));
if (!$notify->save()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not create notification: '.print_r($notify->toArray(),true));
}


return $modx->error->success();
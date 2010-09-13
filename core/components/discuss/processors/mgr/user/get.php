<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get user */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.user_err_ns'));
$c = $modx->newQuery('disUserProfile');
$c->select(array(
    'disUserProfile.*',
    'User.username',
));
$c->innerJoin('modUser','User');
$c->where(array(
    'user' => $scriptProperties['id'],
));
$user = $modx->getObject('disUserProfile',$c);
if (!$user) return $modx->error->failure($modx->lexicon('discuss.user_err_nf'));

$userArray = $user->toArray('',true);
unset($userArray['password'],$userArray['cachepwd']);

return $modx->error->success('',$userArray);
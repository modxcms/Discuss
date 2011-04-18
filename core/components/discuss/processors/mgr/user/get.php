<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get user */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.user_err_ns'));
$c = $modx->newQuery('disUser');
$c->select(array(
    'disUser.*',
    'User.username',
));
$c->innerJoin('modUser','User');
$c->where(array(
    'id' => $scriptProperties['id'],
));
$user = $modx->getObject('disUser',$c);
if (!$user) return $modx->error->failure($modx->lexicon('discuss.user_err_nf'));

$userArray = $user->toArray('',true);
unset($userArray['password'],$userArray['cachepwd']);

return $modx->error->success('',$userArray);
<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get board */
if (empty($_REQUEST['id'])) return $modx->error->failure($modx->lexicon('discuss.user_err_ns'));
$c = $modx->newQuery('disUserProfile');
$c->select('
    disUserProfile.*,
    User.username
');
$c->innerJoin('modUser','User','disUserProfile.user = User.id');
$c->where(array(
    'user' => $_REQUEST['id'],
));
$user = $modx->getObject('disUserProfile',$c);
if ($user == null) return $modx->error->failure($modx->lexicon('discuss.user_err_nf'));

$userArray = $user->toArray('',true);
unset($userArray['password'],$userArray['cachepwd']);

return $modx->error->success('',$userArray);
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

$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));

$properties = $user->toArray();
$properties = array_merge($user->profile->toArray(),$properties);

$properties['topics'] = $modx->getCount('disPost',array(
    'parent' => 0,
    'author' => $user->get('id'),
));

/* do output */
$modx->setPlaceholder('usermenu',$discuss->getChunk('disUserMenu',$properties));
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $discuss->output('user/stats',$properties);
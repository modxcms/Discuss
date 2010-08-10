<?php
/**
 * User statistics page
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$modx->lexicon->load('discuss:user');

/* get user */
if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('modUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }
$user->profile = $modx->getObject('disUserProfile',array(
    'user' => $user->get('id'),
));

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');

$placeholders = $user->toArray();
$placeholders = array_merge($user->profile->toArray(),$placeholders);

$placeholders['topics'] = $modx->getCount('disPost',array(
    'parent' => 0,
    'author' => $user->get('id'),
));

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$modx->setPlaceholder('usermenu',$discuss->getChunk($menuTpl,$placeholders));
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $discuss->output('user/stats',$placeholders);
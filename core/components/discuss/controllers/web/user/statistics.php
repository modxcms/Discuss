<?php
/**
 * User statistics page
 *
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));
$modx->lexicon->load('discuss:user');

/* get user */
if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');

$placeholders = $user->toArray();

$placeholders['topics'] = $modx->getCount('disPost',array(
    'parent' => 0,
    'author' => $user->get('id'),
));

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $placeholders;
<?php
/**
 * User statistics page
 *
 * @package discuss
 */
$modx->lexicon->load('discuss:user');

if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}

/* get user */
if (empty($_REQUEST['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$_REQUEST['user']);
if ($user == null) { $modx->sendErrorPage(); }
$discuss->setPageTitle($modx->lexicon('discuss.user_statistics_header',array('user' => $user->get('username'))));

/* get default properties */
$menuTpl = $modx->getOption('menuTpl',$scriptProperties,'disUserMenu');

$placeholders = $user->toArray();

/* # of topics started */
$placeholders['topics'] = $modx->getCount('disThread',array(
    'author_first' => $user->get('id'),
));
$placeholders['topics'] = number_format($placeholders['topics']);

/* # of replies to topics */
$placeholders['replies'] = $modx->getCount('disPost',array(
    'author' => $user->get('id'),
    'parent:!=' => 0,
));
$placeholders['replies'] = number_format($placeholders['replies']);

/* # of total posts */
$placeholders['posts'] = number_format($placeholders['posts']);

/* do output */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk($menuTpl,$placeholders);
$modx->setPlaceholder('discuss.user',$user->get('username'));

return $placeholders;
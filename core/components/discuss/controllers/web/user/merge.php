<?php
/**
 * User statistics page
 *
 * @package discuss
 */
$modx->lexicon->load('discuss:user');
$discuss->setPageTitle($modx->lexicon('discuss.account_merge'));

if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}

$placeholders = $discuss->user->toArray();
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);
$modx->setPlaceholder('discuss.user',$discuss->user->get('username'));

return $placeholders;
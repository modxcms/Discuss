<?php
/**
 * Logout the user
 * 
 * @package discuss
 * @subpackage controllers
 */
$discuss->setPageTitle($modx->lexicon('discuss.logout'));

$contexts = $modx->user->getSessionContexts();
foreach ($contexts as $context => $level) {
    if ($context == 'mgr') continue;
    $modx->user->removeSessionContext($context);
}

$modx->sendRedirect($discuss->url.'home');
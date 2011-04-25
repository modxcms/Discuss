<?php
/**
 * Logout the user
 * 
 * @package discuss
 * @subpackage controllers
 */
$discuss->setPageTitle($modx->lexicon('discuss.logout'));
$modx->user->removeSessionContext($modx->context->get('key'));
$modx->sendRedirect($discuss->url.'home');
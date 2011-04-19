<?php
/**
 * Logout the user
 * 
 * @package discuss
 * @subpackage controllers
 */
$modx->user->removeSessionContext($modx->context->get('key'));
$modx->sendRedirect($discuss->url.'home');
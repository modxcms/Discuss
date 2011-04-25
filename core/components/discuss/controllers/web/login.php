<?php
/**
 * @package discuss
 */
$placeholders = array();
$discuss->setPageTitle($modx->lexicon('discuss.login'));

$loginResourceId = $modx->getOption('discuss.login_resource_id',null,0);
if (!empty($loginResourceId) && $discuss->ssoMode) {
    $url = $modx->makeUrl($loginResourceId,'',array('discuss' => 1));
    $modx->sendRedirect($url);
}

/* output */
return $placeholders;
<?php
/**
 * @package discuss
 */
$placeholders = array();
$discuss->setPageTitle($modx->lexicon('discuss.register'));


$registerResourceId = $modx->getOption('discuss.register_resource_id',null,0);
if (!empty($registerResourceId)) {
    $url = $modx->makeUrl($registerResourceId,'',array('discuss' => 1));
    $modx->sendRedirect($url);
}


/* output */
return $placeholders;
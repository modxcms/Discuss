<?php
/**
 * Build breadcrumbs from data
 * 
 * @package discuss
 * @subpackage hooks
 */
$tpl = $modx->getOption('breadcrumbLinkTpl',$scriptProperties,'breadcrumbs/disBreadcrumbsLink');
$activeTpl = $modx->getOption('breadcrumbActiveTpl',$scriptProperties,'breadcrumbs/disBreadcrumbsActive');
$containerTpl = $modx->getOption('breadcrumbsTpl',$scriptProperties,'breadcrumbs/disBreadcrumbs');

$output = array();
foreach ($scriptProperties['items'] as $item) {
    $output[] = $discuss->getChunk(!empty($item['active']) ? $activeTpl : $tpl,$item);
}
$output = implode("\n",$output);
return $discuss->getChunk($containerTpl,array('items' => $output));
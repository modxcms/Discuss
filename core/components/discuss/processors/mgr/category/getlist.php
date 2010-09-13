<?php
/**
 * Get a list of Categories
 *
 * @package discuss
 * @subpackage processors
 */
$isLimit = !empty($scriptProperties['limit']);
$isCombo = !empty($scriptProperties['combo']);
$sort = $modx->getOption('sort',$scriptProperties,'rank');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);

/* build query */
$c = $modx->newQuery('disCategory');
$count = $modx->getCount('disCategory',$c);
$c->sortby($sort,$dir);
if ($isCombo || $isLimit) {
    $c->limit($limit,$start);
}
$categories = $modx->getCollection('disCategory', $c);

/* iterate */
$list = array();
foreach ($categories as $category) {
    $categoryArray = $category->toArray();
    if ($isCombo) {
        unset($categoryArray['description'],$categoryArray['introtext']);
    }
    $list[]= $categoryArray;
}
return $this->outputArray($list,$count);
<?php
/**
 * Get a list of Categories
 *
 * @package discuss
 * @subpackage processors
 */
$limit = isset($_REQUEST['limit']);
$combo = isset($_REQUEST['combo']);
if (!isset($_REQUEST['start'])) $_REQUEST['start'] = 0;
if (!isset($_REQUEST['limit'])) $_REQUEST['limit'] = 20;
if (!isset($_REQUEST['sort'])) $_REQUEST['sort'] = 'name';
if (!isset($_REQUEST['dir'])) $_REQUEST['dir'] = 'ASC';

$c = $modx->newQuery('disCategory');
if ($combo || $limit) {
    $c->limit($_REQUEST['limit'], $_REQUEST['start']);
}

$categories = $modx->getCollection('disCategory', $c);
$count = $modx->getCount('disCategory');

$list = array();
foreach ($categories as $category) {
    $categoryArray = $category->toArray();

    if ($combo) {
        unset($categoryArray['description'],$categoryArray['introtext']);
    }

    $list[]= $categoryArray;
}
return $this->outputArray($list,$count);
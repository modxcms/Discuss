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

$c = $modx->newQuery('disUser');
$c->innerJoin('modUser','User');

if ($combo || $limit) {
    $c->limit($_REQUEST['limit'], $_REQUEST['start']);
}

$users = $modx->getCollection('modUser', $c);
$count = $modx->getCount('modUser',$c);

$list = array();
foreach ($users as $user) {
    $userArray = $user->toArray();
    $userArray['menu'] = array();
    $list[]= $userArray;
}
return $this->outputArray($list,$count);
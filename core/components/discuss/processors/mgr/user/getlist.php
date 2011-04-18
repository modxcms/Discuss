<?php
/**
 * Get a list of Categories
 *
 * @package discuss
 * @subpackage processors
 */
$isLimit = !empty($scriptProperties['limit']);
$isCombo = !empty($scriptProperties['combo']);
$sort = $modx->getOption('sort',$scriptProperties,'username');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$query = $modx->getOption('query',$scriptProperties,'');

/* build query */
$c = $modx->newQuery('disUser');
$c->innerJoin('modUser','User');
if (!empty($query)) {
    $c->where(array(
        'disUser.username:LIKE' => '%'.$query.'%',
    ));
}
$count = $modx->getCount('disUser',$c);
if ($isLimit || $isCombo) {
    $c->limit($limit,$start);
}
$users = $modx->getCollection('disUser', $c);

/* iterate */
$list = array();
foreach ($users as $user) {
    $userArray = $user->toArray();
    if (!empty($userArray['last_active']) && $userArray['last_active'] != '0000-00-00 00:00:00' && $userArray['last_active'] != '-001-11-30 00:00:00') {
        $userArray['last_active'] = strftime('%b %d, %Y %I:%M %p',strtotime($userArray['last_active']));
    } else {
        $userArray['last_active'] = '';
    }
    $list[]= $userArray;
}
return $this->outputArray($list,$count);
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
if (!isset($_REQUEST['sort'])) $_REQUEST['sort'] = 'title';
if (!isset($_REQUEST['dir'])) $_REQUEST['dir'] = 'ASC';

$c = $modx->newQuery('disPost');
$c->where(array(
    'author' => $_REQUEST['user'],
));
$count = $modx->getCount('disPost',$c);
if ($combo || $limit) {
    $c->limit($_REQUEST['limit'], $_REQUEST['start']);
}
$c->sortby($_REQUEST['sort'],$_REQUEST['dir']);
$posts = $modx->getCollection('disPost', $c);

$list = array();
foreach ($posts as $post) {
    $postArray = $post->toArray();
    $postArray['menu'] = array();
    $postArray['menu'][] = array(
        'text' => 'Modify Post',
        'handler' => 'this.updatePost',
    );
    $postArray['menu'][] = '-';
    $postArray['menu'][] = array(
        'text' => 'Remove Post',
        'handler' => 'this.removePost',
    );
    $list[]= $postArray;
}
return $this->outputArray($list,$count);
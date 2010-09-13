<?php
/**
 * Get a list of Posts for a User
 *
 * @package discuss
 * @subpackage processors
 */
$isLimit = !empty($scriptProperties['limit']);
$isCombo = !empty($scriptProperties['combo']);
$sort = $modx->getOption('sort',$scriptProperties,'title');
$dir = $modx->getOption('dir',$scriptProperties,'ASC');
$start = $modx->getOption('start',$scriptProperties,0);
$limit = $modx->getOption('limit',$scriptProperties,20);
$user = $modx->getOption('user',$scriptProperties,0);
if (empty($user)) return $modx->error->failure($modx->lexicon('discuss.user_err_ns'));

$c = $modx->newQuery('disPost');
$c->where(array(
    'author' => $user,
));
$count = $modx->getCount('disPost',$c);
if ($isCombo || $isLimit) {
    $c->limit($limit,$start);
}
$c->sortby($sort,$dir);
$posts = $modx->getCollection('disPost', $c);

$list = array();
foreach ($posts as $post) {
    $postArray = $post->toArray();
    $list[]= $postArray;
}
return $this->outputArray($list,$count);
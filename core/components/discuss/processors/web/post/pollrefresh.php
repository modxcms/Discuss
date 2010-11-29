<?php
/**
 * Gets the post count for a thread
 *
 * @package discuss
 * @subpackage processors
 */
$c = $modx->newQuery('disPost');
$c->innerJoin('disPostClosure','Descendants');
$c->where(array(
    'Descendants.ancestor' => $_POST['post'],
));
$c->sortby('disPost.rank','ASC');
$postCount = $modx->getCount('disPost',$c);

return $modx->error->success($postCount);
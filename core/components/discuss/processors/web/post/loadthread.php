<?php
/**
 * Load a thread from a post
 */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Descendants.depth AS depth,
    Author.username AS username,
    AuthorProfile.fullname AS author_name');
$c->innerJoin('disPostClosure','Descendants');
$c->innerJoin('modUser','Author');
$c->innerJoin('modUserProfile','AuthorProfile','Author.id = AuthorProfile.internalKey');
$c->where(array(
    'Descendants.ancestor' => $_POST['post'],
));
$c->sortby('disPost.rank','ASC');
$posts = $modx->getCollection('disPost',$c);

$pa = array();
foreach ($posts as $post) {
    $pa[] = $post->toArray();
}

$discuss->loadTreeParser();
$output = $discuss->treeParser->parse($pa,'disBoardThread');

return $modx->error->success($output);
<?php
/* get default options */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));

/* recent posts */
$c = $modx->newQuery('disPost');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->select($modx->getSelectColumns('disPost','disPost'));
$c->select(array(
    'board_name' => 'Board.name',
    'author_username' => 'Author.username',
));
$c->sortby('createdon','DESC');
$c->limit($limit);
$recentPosts = $modx->getCollection('disPost',$c);
$rps = array();
$idx = 0;
foreach ($recentPosts as $post) {
    $pa = $post->toArray('',true);
    $pa['class'] = 'dis-board-li';
    $pa['idx'] = $idx;

    $rps[] = $discuss->getChunk('disPostLi',$pa);
    $idx++;
}
$list = implode("\n",$rps);
unset($rps,$pa,$recentPosts,$post);

return $list;
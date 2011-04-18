<?php
/* get default options */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));
$start = $modx->getOption('start',$scriptProperties,0);

/* recent posts */
$c = $modx->newQuery('disThread');
$c->innerJoin('disBoard','Board');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disUser','LastAuthor');
if (!empty($scriptProperties['user'])) {
    $c->where(array(
        'LastPost.author' => $scriptProperties['user'],
    ));
}
if ($discuss->isLoggedIn) {
    $ignoreBoards = $discuss->user->get('ignore_boards');
    if (!empty($ignoreBoards)) {
        $c->where(array(
            'Board.id:NOT IN' => explode(',',$ignoreBoards),
        ));
    }
}
$total = $modx->getCount('disThread',$c);
$c->select($modx->getSelectColumns('disPost','LastPost'));
$c->select(array(
    'disThread.id',
    'disThread.replies',
    'disThread.views',
    'disThread.sticky',
    'disThread.locked',
    'FirstPost.title',
    'board_name' => 'Board.name',
    'post_id' => 'LastPost.id',
    'author' => 'LastPost.author',
    'author_username' => 'LastAuthor.username',
));
if (!empty($scriptProperties['showIfParticipating'])) {
    $c->select(array(
        '(SELECT GROUP_CONCAT(pAuthor.id)
            FROM '.$modx->getTableName('disPost').' AS pPost
            INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
            WHERE pPost.thread = disThread.id
         ) AS participants',
    ));
}
$c->sortby('LastPost.createdon','DESC');
$c->limit($limit,$start);
$recentPosts = $modx->getCollection('disThread',$c);

/* iterate */
$list = array();
$idx = 0;
foreach ($recentPosts as $thread) {
    $thread->buildIcons();
    $thread->buildCssClass('board-post');
    $threadArray = $thread->toArray();
    $threadArray['idx'] = $idx;
    $threadArray['createdon'] = strftime($discuss->dateFormat,strtotime($threadArray['createdon']));
    
    $threadArray['views'] = number_format($threadArray['views']);
    $threadArray['replies'] = number_format($threadArray['replies']);

    /* unread class */
    $list[] = $discuss->getChunk('post/disPostLi',$threadArray);
    $idx++;
}
$list = implode("\n",$list);
unset($rps,$pa,$recentPosts,$post);

return array(
    'results' => $list,
    'total' => $total,
    'start' => $start,
    'limit' => $limit,
);
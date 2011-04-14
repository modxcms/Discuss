<?php
/**
 * Get a list of posts in a board
 *
 * @package discuss
 */
$response = array(
    'start' => $scriptProperties['start'],
    'limit' => $scriptProperties['limit'],
);

$c = $modx->newQuery('disThread');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disUser','LastAuthor');
$c->leftJoin('disThreadRead','Reads','Reads.user = '.$discuss->user->get('id').' AND disThread.id = Reads.thread');
$c->where(array(
    'disThread.board' => is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board'],
));
$response['total'] = $modx->getCount('disThread',$c);
$c->select(array(
    'LastPost.*',
    'last_post_id' => 'LastPost.id',
    'FirstPost.title',
    'LastAuthor.username',
    'disThread.id',
    'disThread.replies',
    'disThread.views',
    'disThread.sticky',
    'disThread.locked',
    'viewed' => 'Reads.thread',
    '(SELECT GROUP_CONCAT(pAuthor.id)
        FROM '.$modx->getTableName('disPost').' AS pPost
        INNER JOIN '.$modx->getTableName('disUser').' AS pAuthor ON pAuthor.id = pPost.author
        WHERE pPost.thread = disThread.id
     ) AS participants',
));
if ($modx->getOption('discuss.enable_sticky',null,true)) {
    $c->sortby('disThread.sticky','DESC');
}
$c->sortby('LastPost.createdon','DESC');
if (!empty($scriptProperties['limit'])) {
    $c->limit($scriptProperties['limit'],$scriptProperties['start']);
}
$threads = $modx->getCollection('disThread',$c);

/* iterate through threads */
$response['results'] = array();
foreach ($threads as $thread) {
    $thread->buildCssClass('board-post');
    $thread->buildIcons();
    $threadArray = $thread->toArray();

    $phs = array(
        'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($threadArray['createdon'])),
        'user' => $threadArray['author'],
        'username' => $threadArray['username'],
    );
    $latestText = $discuss->getChunk('board/disLastPostBy',$phs);

    $threadArray['latest'] = $latestText;
    $threadArray['latest.id'] = $thread->get('last_post_id');
    $threadArray['views'] = number_format($threadArray['views']);
    $threadArray['replies'] = number_format($threadArray['replies']);

    /* unread class */
    $threadArray['unread'] = '';
    if (!$threadArray['viewed'] && $discuss->isLoggedIn) {
        $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
    }
    $response['results'][] = $discuss->getChunk('post/disBoardPost',$threadArray);
}

return $response;
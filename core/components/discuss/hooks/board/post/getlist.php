<?php
/**
 * Get a list of posts in a board
 *
 * @package discuss
 */

$c = $modx->newQuery('disPost');
$c->select(array(
    'disPost.*',
    'Descendants.depth',
    'Author.username',
));
/* TODO: abstract these subqueries */
$c->select(array(
    'author_name' => 'AuthorProfile.fullname',
    '(SELECT COUNT(*) FROM '.$modx->getTableName('disPostClosure').'
     WHERE
        ancestor = disPost.id
    AND descendant != disPost.id) AS `replies`,
    (SELECT COUNT(*) FROM '.$modx->getTableName('disPost').' AS dp
        WHERE
            id NOT IN (
                SELECT post FROM '.$modx->getTableName('disPostRead').'
                WHERE
                    user = '.$modx->user->get('id').'
                AND board = disPost.board
            )
        AND id IN (
            SELECT descendant FROM '.$modx->getTableName('disPostClosure').'
            WHERE ancestor = disPost.id
        )
        AND board = disPost.board
    ) AS `unread`',
));
$c->innerJoin('disPostClosure','Descendants');
$c->innerJoin('disPostClosure','Ancestors');
$c->innerJoin('modUser','Author');
$c->innerJoin('modUserProfile','AuthorProfile','`Author`.`id` = `AuthorProfile`.`internalKey`');
$c->where(array(
    'Descendants.ancestor' => 0,
    'Descendants.depth' => 0,
    'disPost.board' => is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board'],
));
if ($modx->getOption('discuss.enable_sticky',null,true)) {
    $c->sortby('disPost.sticky','DESC');
}
$c->sortby('disPost.rank','DESC');
$c->limit($scriptProperties['limit'],$scriptProperties['start']);
$posts = $modx->getCollection('disPost',$c);


/* iterate through threads */
$pa = array();
foreach ($posts as $post) {
    /* get latest post in thread
     * TODO: eventually move this to post/getList hook, where it can be one query
     * rather than in this foreach loop
     */
    $c = $modx->newQuery('disPost');
    $c->select($modx->getSelectColumns('disPost','disPost','',array('id','title','createdon','author')));
    $c->select($modx->getSelectColumns('modUser','Author','',array('username')));
    $c->innerJoin('disPostClosure','Descendants');
    $c->innerJoin('modUser','Author');
    $c->where(array(
        'Descendants.ancestor' => $post->get('id'),
    ));
    $c->sortby($modx->getSelectColumns('disPost','disPost','',array('createdon')),'DESC');
    $latestPost = $modx->getObject('disPost',$c);
    if ($latestPost != null) {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($latestPost->get('createdon'))),
            'user' => $latestPost->get('author'),
            'username' => $latestPost->get('username'),
        );
        $latestText = $discuss->getChunk('disLastPostBy',$phs);

        $createdon = strftime($modx->getOption('discuss.date_format'),strtotime($latestPost->get('createdon')));
        $post->set('latest',$latestText);
        $post->set('latest.id',$latestPost->get('id'));
    } else {
        $post->set('latest',$modx->lexicon('discuss.no_replies_yet'));
    }

    /* set css class */
    $class = 'board-post';
    if ($modx->getOption('discuss.enable_hot',null,true)) {
        $threshold = $modx->getOption('discuss.hot_thread_threshold',null,10);
        if ($modx->user->get('id') == $post->get('author') && $modx->user->isAuthenticated()) {
            $class .= $post->get('replies') < $threshold ? ' dis-my-normal-thread' : ' dis-my-veryhot-thread';
        } else {
            $class .= $post->get('replies') < $threshold ? '' : ' dis-veryhot-thread';
        }
    }
    $post->set('class',$class);

    /* if sticky/locked */
    $icons = array();
    if ($post->get('locked')) { $icons[] = '<div class="dis-thread-locked"></div>'; }
    if ($modx->getOption('discuss.enable_sticky',null,true) && $post->get('sticky')) {
        $icons[] = '<div class="dis-thread-sticky"></div>';
    }
    $post->set('icons',implode("\n",$icons));

    /* unread class */
    $unread = '';
    if ($post->get('unread') > 0 && $modx->user->isAuthenticated()) {
        $unread = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
    }
    $post->set('unread',$unread);

    $pa[] = $post->toArray();
}

return $pa;
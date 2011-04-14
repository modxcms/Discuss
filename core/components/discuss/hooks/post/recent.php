<?php
/* get default options */
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));

/* recent posts */
$c = $modx->newQuery('disThread');
$c->innerJoin('disBoard','Board');
$c->innerJoin('disPost','FirstPost');
$c->innerJoin('disPost','LastPost');
$c->innerJoin('disUser','LastAuthor');
$c->select($modx->getSelectColumns('disPost','FirstPost'));
$c->select(array(
    'disThread.id',
    'post_id' => 'LastPost.id',
    'board_name' => 'Board.name',
    'author_username' => 'LastAuthor.username',
));
$c->sortby('LastPost.createdon','DESC');
$c->limit($limit);
$recentPosts = $modx->getCollection('disThread',$c);
$rps = array();
$idx = 0;

foreach ($recentPosts as $post) {
    $threadArray = $post->toArray('',true);
    $threadArray['idx'] = $idx;
    $threadArray['class'] = 'dis-board-li';
    $threadArray['createdon'] = strftime($discuss->dateFormat,strtotime($threadArray['createdon']));
    $threadArray['icons'] = '';
    
    /* set css class */
    $class = array('board-post');
    if ($enableHot) {
        $threshold = $hotThreadThreshold;
        if ($discuss->user->get('id') == $threadArray['author'] && $discuss->isLoggedIn) {
            $class[] = $threadArray['replies'] < $threshold ? 'dis-my-normal-thread' : 'dis-my-veryhot-thread';
        } else {
            $class[] = $threadArray['replies'] < $threshold ? '' : 'dis-veryhot-thread';
        }
    }
    $threadArray['class'] = implode(' ',$class);

    /* if sticky/locked */
    $icons = array();
    if ($threadArray['locked']) { $icons[] = '<div class="dis-thread-locked"></div>'; }
    if ($enableSticky && $threadArray['sticky']) {
        $icons[] = '<div class="dis-thread-sticky"></div>';
    }
    $threadArray['icons'] = implode("\n",$icons);

    $threadArray['views'] = number_format($threadArray['views']);
    $threadArray['replies'] = number_format($threadArray['replies']);

    /* unread class */
    $threadArray['unread'] = '<img src="'.$discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';

    $rps[] = $discuss->getChunk('post/disPostLi',$threadArray);
    $idx++;
}
$list = implode("\n",$rps);
unset($rps,$pa,$recentPosts,$post);

return $list;
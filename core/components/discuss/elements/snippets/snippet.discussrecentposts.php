<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('home');

/* get default options */
$cssPostRowCls = $modx->getOption('cssBoardRowCls',$scriptProperties,'dis-board-li');
$postRowTpl = $modx->getOption('postRowTpl',$scriptProperties,'disPostLi');
$limit = $modx->getOption('limit',$scriptProperties,$modx->getOption('discuss.num_recent_posts',null,10));

/* recent posts */
$c = $modx->newQuery('disPost');
$c->select('
    disPost.*,
    Board.name AS board_name,
    Author.username AS author_username
');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->sortby('createdon','DESC');
$c->limit($limit);
$recentPosts = $modx->getCollection('disPost',$c);
$rps = array();
foreach ($recentPosts as $post) {
    $pa = $post->toArray('',true);
    $pa['class'] = $cssPostRowCls;

    $rps[] = $discuss->getChunk($postRowTpl,$pa);
}
$list = implode("\n",$rps);
unset($rps,$pa,$recentPosts,$post);


return $list;
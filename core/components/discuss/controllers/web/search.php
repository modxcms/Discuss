<?php
/**
 * Search the forums
 *
 * @package discuss
 */
$discuss->setSessionPlace('search');

/* setup default properties */
$cssSearchResultCls = $modx->getOption('cssSearchResultCls',$scriptProperties,'dis-search-result');
$cssSearchResultParentCls = $modx->getOption('cssSearchResultParentCls',$scriptProperties,'dis-search-parent-result');
$resultRowTpl = $modx->getOption('resultRowTpl',$scriptProperties,'disSearchResult');
$toggle = $modx->getOption('toggle',$scriptProperties,'+');

/* do search */
$placeholders = array();
if (!empty($_REQUEST['s'])) {
    $s = str_replace(array(';',':'),'',strip_tags($_REQUEST['s']));
    $c = $modx->newQuery('disPost');
    $c->innerJoin('disThread','Thread');
    $c->innerJoin('disBoard','Board');
    $c->innerJoin('disUser','Author');
    $c->innerJoin('disPostClosure','PostClosure','PostClosure.descendant = disPost.id AND PostClosure.ancestor != 0');
    $c->where(array(
        'MATCH (disPost.title,disPost.message) AGAINST ("'.$s.'" IN BOOLEAN MODE)',
    ));
    if ($discuss->isLoggedIn) {
        $ignoreBoards = $discuss->user->get('ignore_boards');
        if (!empty($ignoreBoards)) {
            $c->where(array(
                'Board.id:NOT IN' => explode(',',$ignoreBoards),
            ));
        }
    }
    $c->select($modx->getSelectColumns('disPost','disPost'));
    $c->select(array(
        'username' => 'Author.username',
        'board_name' => 'Board.name',
        'MATCH (disPost.title,disPost.message) AGAINST ("'.$s.'" IN BOOLEAN MODE) AS score',
    ));
    $c->sortby('score','ASC');
    $c->sortby('disPost.rank','ASC');
    $c->limit(10);
    $posts = $modx->getCollection('disPost',$c);

    $placeholders['results'] = array();
    $maxScore = 0;
    foreach ($posts as $post) {
        $postArray = $post->toArray();

        if ($postArray['score'] > $maxScore) {
            $maxScore = $postArray['score'];
        }

        $postArray['relevancy'] = @number_format(($post->get('score')/$maxScore)*100,0);
        if ($postArray['parent']) {
            $postArray['cls'] = 'dis-search-result dis-result-'.$postArray['thread'];
        } else {
            $postArray['toggle'] = $toggle;
            $postArray['cls'] = 'dis-search-parent-result dis-parent-result-'.$postArray['thread'];
        }
        $postArray['content'] = strip_tags(substr($post->getContent(),0,100));
        $placeholders['results'][] = $discuss->getChunk('disSearchResult',$postArray);
    }
    $placeholders['results'] = implode("\n",$placeholders['results']);
    $placeholders['search'] = $s;
}

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->url,
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array(
    'text' => $modx->lexicon('discuss.search'),
    'active' => true,
);
$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

return $placeholders;
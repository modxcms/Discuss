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
    $s = strip_tags($_REQUEST['s']);

    $c = new xPDOCriteria($modx,'
        SELECT
            Post.*,
            Author.username AS username,
            Board.name AS board_name,
            Thread.id AS thread,
            PostClosure.depth AS depth,
            MATCH (Post.title,Post.message) AGAINST ("'.$s.'" IN BOOLEAN MODE) AS score

        FROM '.$modx->getTableName('disPost').' AS Post
            INNER JOIN '.$modx->getTableName('modUser').' AS Author
            ON Author.id = Post.author
            INNER JOIN '.$modx->getTableName('disBoard').' AS Board
            ON Board.id = Post.board

            INNER JOIN '.$modx->getTableName('disPostClosure').' AS PostClosure
             ON PostClosure.descendant = Post.id
            AND PostClosure.ancestor != 0

            INNER JOIN '.$modx->getTableName('disPost').' AS Thread
             ON PostClosure.ancestor = Thread.id
            AND Thread.parent = 0
        WHERE
            MATCH (Post.title,Post.message) AGAINST ("'.$s.'" IN BOOLEAN MODE)
        ORDER BY score,Post.rank ASC

    ');
    $posts = $modx->getCollection('disPost',$c);

    $placeholders['results'] = '';
    $maxScore = 0;
    foreach ($posts as $post) {
        $pa = $post->toArray();

        if ($post->get('score') > $maxScore) {
            $maxScore = $post->get('score');
        }

        $pa['relevancy'] = @number_format(($post->get('score')/$maxScore)*100,0);

        if ($post->get('parent') > 0) {
            $pa['cls'] = $cssSearchResultCls.' dis-result-'.$post->get('thread');
        } else {
            $pa['toggle'] = $toggle;
            $pa['cls'] = $cssSearchResultParentCls.' dis-parent-result-'.$post->get('thread');
        }
        $pa['content'] = strip_tags(substr($post->getContent(),0,60));
        $placeholders['results'] .= $discuss->getChunk($resultRowTpl,$pa);
    }
    $placeholders['search'] = $s;
}

/* get board breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $modx->makeUrl($modx->getOption('discuss.board_list_resource')),
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array(
    'text' => $modx->lexicon('discuss.search'),
    'active' => true,
);
$trail = $modx->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

return $placeholders;
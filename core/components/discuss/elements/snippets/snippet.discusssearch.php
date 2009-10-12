<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('search');

$properties = array();

if (!empty($_REQUEST['s'])) {
    $s = strip_tags($_REQUEST['s']);

    $c = new xPDOCriteria($modx,'
        SELECT
            Post.*,
            Author.username AS username,
            Board.name AS board_name,
            Thread.id AS thread,
            PostClosure.depth AS depth,
            MATCH (Post.title,Post.message) AGAINST ("'.$s.'") AS score

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
            MATCH (Post.title,Post.message) AGAINST ("'.$s.'")
        ORDER BY score,Post.rank ASC

    ');
    $posts = $modx->getCollection('disPost',$c);

    $resultsTpl = '';
    $maxScore = 0;
    foreach ($posts as $post) {
        $pa = $post->toArray();

        if ($post->get('score') > $maxScore) {
            $maxScore = $post->get('score');
        }

        $pa['relevancy'] = @number_format(($post->get('score')/$maxScore)*100,0);

        if ($post->get('parent') > 0) {
            $pa['cls'] = 'dis-search-result dis-result-'.$post->get('thread');
        } else {
            $pa['toggle'] = '+';
            $pa['cls'] = 'dis-search-parent-result dis-parent-result-'.$post->get('thread');
        }
        $pa['content'] = strip_tags(substr($post->getContent(),0,60));
        $resultsTpl .= $discuss->getChunk('disSearchResult',$pa);
    }
    $properties['results'] = $resultsTpl;
    $properties['search'] = $s;
}

/* output */
$modx->regClientStartupScript($discuss->config['jsUrl'].'web/dis.search.js');
$modx->regClientCSS($discuss->config['cssUrl'].'search.css');
return $discuss->output('search',$properties);
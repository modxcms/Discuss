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
$c->select('
    `AuthorProfile`.`fullname` AS `author_name`,
    (SELECT COUNT(*) FROM '.$modx->getTableName('disPostClosure').'
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
    ) AS `unread`
');
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
return $modx->getCollection('disPost',$c);
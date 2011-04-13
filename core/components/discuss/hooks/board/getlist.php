<?php
/**
 * Get a list of boards
 *
 * @package discuss
 */

$board = !empty($scriptProperties['board']) ? (is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board']) : 0;
/* unread sql */
$ucs = $modx->newQuery('disPostRead');
$ucs->select($modx->getSelectColumns('disPostRead','disPostRead','',array('post')));
$ucs->where(array(
    'user' => $modx->user->get('id'),
    'board = disBoard.id',
));
$ucs->prepare();
$ucsSql = $ucs->toSql();
$uc = $modx->newQuery('disPost');
$uc->setClassAlias('dp');
$uc->select('COUNT('.$modx->getSelectColumns('disPost','','',array('id')).')');
$uc->where(array(
    $modx->getSelectColumns('disPost','','',array('id')).' NOT IN ('.$ucsSql.')',
    $modx->getSelectColumns('disPost','','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
));
$uc->prepare();
$unreadSql = $uc->toSql();

/* subboards sql */
$sbc = $modx->newQuery('disBoard');
$sbc->setClassAlias('subBoard');
$sbc->select('GROUP_CONCAT(CONCAT_WS(":",`subBoardClosureBoard`.`id`,`subBoardClosureBoard`.`name`) SEPARATOR ",") AS `name`');
$sbc->innerJoin('disBoardClosure','subBoardClosure','`subBoardClosure`.`ancestor` = `subBoard`.`id`');
$sbc->innerJoin('disBoard','subBoardClosureBoard','`subBoardClosureBoard`.`id` = `subBoardClosure`.`descendant`');
$sbc->where(array(
    '`subBoard`.`id` = `disBoard`.`id`',
    '`subBoardClosure`.`descendant` != `disBoard`.`id`',
    'subBoardClosure.depth' => 1,
));
$sbc->groupby($modx->getSelectColumns('disBoard','subBoard','',array('id')));
$sbc->prepare();
$sbSql = $sbc->toSql();

/* get main query */
$c = $modx->newQuery('disBoard');
$c->select('disBoard.*,
    ('.$unreadSql.') AS `unread`,
    ('.$sbSql.') AS `subboards`,
    `LastPost`.`title` AS `last_post_title`,
    `LastPost`.`author` AS `last_post_author`,
    `LastPost`.`createdon` AS `last_post_createdon`,
    `LastPostAuthor`.`username` AS `last_post_username`
');
$c->innerJoin('disCategory','Category');
$c->innerJoin('disBoardClosure','Descendants');
$c->leftJoin('disPost','LastPost');
$c->leftJoin('modUser','LastPostAuthor','LastPost.author = LastPostAuthor.id');
if (!empty($board)) {
    $c->where(array(
        'disBoard.parent' => $board,
    ));
}
$c->sortby('Category.rank','ASC');
$c->sortby('disBoard.rank','ASC');
$subBoards = $modx->getCollection('disBoard',$c);
unset($c);

return $subBoards;
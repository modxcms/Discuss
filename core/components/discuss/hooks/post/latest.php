<?php
/**
 * Get latest post
 */

$c = $modx->newQuery('disPost');
$c->select(array(
    'disPost.id',
    'disPost.title',
    'disPost.createdon',
    'disPost.author',
    'Author.username',
    'thread' => 'Thread.id',
));
$c->select($modx->getSelectColumns('disBoard','Board','',array('name')).' AS `board`');
$c->innerJoin('disBoard','Board');
$c->innerJoin('modUser','Author');
$c->innerJoin('disPost','Thread');
$c->leftJoin('disBoardUserGroup','UserGroups',$modx->getSelectColumns('disBoard','Board','',array('id')).' = '.$modx->getSelectColumns('disBoardUserGroup','UserGroups','',array('board')));
$c->orCondition(array(
    'UserGroups.usergroup' => null,
),null,1);
if (!empty($scriptProperties['groups'])) {
    /* restrict boards by user group if applicable */
    $g = array(
        'UserGroups.usergroup:IN' => $scriptProperties['groups'],
    );
    $g['OR:UserGroups.usergroup:='] = null;
    $where[] = $g;
    $c->andCondition($where,null,2);
}
$c->sortby($modx->getSelectColumns('disPost','disPost','',array('createdon')),'DESC');
$latestPost = $modx->getObject('disPost',$c);

return $latestPost;
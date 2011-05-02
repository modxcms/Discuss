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
    'Thread.id AS thread',
));
if ($discuss->isLoggedIn) {
    $ignoreBoards = $discuss->user->get('ignore_boards');
    if (!empty($ignoreBoards)) {
        $c->where(array(
            'Board.id:NOT IN' => explode(',',$ignoreBoards),
        ));
    }
}
$c->innerJoin('disBoard','Board');
$c->innerJoin('disUser','Author');
$c->innerJoin('disPost','Thread');
$c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
$c->where(array(
    'Board.status:!=' => disBoard::STATUS_INACTIVE,
));

/* ignore spam/recycle bin boards */
$spamBoard = $modx->getOption('discuss.spam_bucket_board',null,false);
if (!empty($spamBoard)) {
    $c->where(array(
        'Board.id:!=' => $spamBoard,
    ));
}
$trashBoard = $modx->getOption('discuss.recycle_bin_board',null,false);
if (!empty($trashBoard)) {
    $c->where(array(
        'Board.id:!=' => $trashBoard,
    ));
}

$groups = $discuss->user->getUserGroups();
if (!$discuss->user->isAdmin()) {
    if (!empty($groups)) {
        /* restrict boards by user group if applicable */
        $g = array(
            'UserGroups.usergroup:IN' => $groups,
        );
        $g['OR:UserGroups.usergroup:IS'] = null;
        $where[] = $g;
        $c->andCondition($where,null,2);
    } else {
        $c->where(array(
            'UserGroups.usergroup:IS' => null,
        ));
    }
}
$c->select($modx->getSelectColumns('disBoard','Board','',array('name')).' AS `board`');
$c->sortby($modx->getSelectColumns('disPost','disPost','',array('createdon')),'DESC');
$latestPost = $modx->getObject('disPost',$c);

return $latestPost;
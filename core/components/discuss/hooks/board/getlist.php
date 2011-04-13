<?php
/**
 * Get a list of boards
 *
 * @package discuss
 */

$board = isset($scriptProperties['board']) ? (is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board']) : 0;
/* unread sql */
$unreadSubCriteria = $modx->newQuery('disPostRead');
$unreadSubCriteria->select($modx->getSelectColumns('disPostRead','disPostRead','',array('post')));
$unreadSubCriteria->where(array(
    'disPostRead.user' => $modx->user->get('id'),
    $modx->getSelectColumns('disPostRead','disPostRead','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
));
$unreadSubCriteria->prepare();
$unreadSubCriteriaSql = $unreadSubCriteria->toSql();
$unreadCriteria = $modx->newQuery('disPost');
$unreadCriteria->setClassAlias('dp');
$unreadCriteria->select('COUNT('.$modx->getSelectColumns('disPost','','',array('id')).')');
$unreadCriteria->where(array(
    $modx->getSelectColumns('disPost','','',array('id')).' NOT IN ('.$unreadSubCriteriaSql.')',
    $modx->getSelectColumns('disPost','','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
));
$unreadCriteria->prepare();
$unreadSql = $unreadCriteria->toSql();

/* subboards sql */
$sbCriteria = $modx->newQuery('disBoard');
$sbCriteria->setClassAlias('subBoard');
$sbCriteria->select('GROUP_CONCAT(CONCAT_WS(":",subBoardClosureBoard.id,subBoardClosureBoard.name) SEPARATOR ",") AS name');
$sbCriteria->innerJoin('disBoardClosure','subBoardClosure','subBoardClosure.ancestor = subBoard.id');
$sbCriteria->innerJoin('disBoard','subBoardClosureBoard','subBoardClosureBoard.id = subBoardClosure.descendant');
$sbCriteria->where(array(
    'subBoard.id = disBoard.id',
    'subBoardClosure.descendant != disBoard.id',
    'subBoardClosure.depth' => 1,
));
$sbCriteria->groupby($modx->getSelectColumns('disBoard','subBoard','',array('id')));
$sbCriteria->prepare();
$sbSql = $sbCriteria->toSql();

/* get main query */
$c = $modx->newQuery('disBoard');
$c->select($modx->getSelectColumns('disBoard','disBoard'));
$c->select(array(
    'category_name' => 'Category.name',
    '('.$unreadSql.') AS '.$modx->escape('unread'),
    '('.$sbSql.') AS '.$modx->escape('subboards'),
    'last_post_title' => 'LastPost.title',
    'last_post_author' => 'LastPost.author',
    'last_post_createdon' => 'LastPost.createdon',
    'last_post_username' => 'LastPostAuthor.username',
));
$c->innerJoin('disCategory','Category');
$c->innerJoin('disBoardClosure','Descendants');
$c->leftJoin('disPost','LastPost');
$c->leftJoin('modUser','LastPostAuthor','LastPost.author = LastPostAuthor.id');
$c->leftJoin('disBoardUserGroup','UserGroups');
if (isset($board) && $board !== null) {
    $c->where(array(
        'disBoard.parent' => $board,
    ));
}
if (!empty($scriptProperties['groups'])) {
    /* restrict boards by user group if applicable */
    $g = array(
        'UserGroups.usergroup:IN' => $scriptProperties['groups'],
    );
    $g['OR:UserGroups.usergroup:='] = null;
    $where[] = $g;
    $c->andCondition($where,null,2);
}
$c->sortby('Category.rank','ASC');
$c->sortby('disBoard.rank','ASC');
$boards = $modx->getCollection('disBoard',$c);
unset($c);

/* now loop through boards */
$list = array();
$currentCategory = 0;
$rowClass = 'even';
$boardList = array();

foreach ($boards as $board) {
    if ($board->get('unread') > 0 && $modx->user->isAuthenticated()) {
        $board->set('unread-cls','dis-unread');
    }

    if ($board->get('last_post_author')) {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($board->get('last_post_createdon'))),
            'user' => $board->get('last_post_author'),
            'username' => $board->get('last_post_username'),
        );
        $lp = $discuss->getChunk('disLastPostBy',$phs);
        $board->set('lastPost',$lp);
    }

    $subBoards = $board->get('subboards');
    $ba['subforums'] = '';

    if (!empty($subBoards)) {
        $subBoards = explode(',',$subBoards);
        $ph = array();
        $sbl = '';
        foreach ($subBoards as $subboard) {
            $sb = explode(':',$subboard);
            $ph['id'] = $sb[0];
            $ph['title'] = $sb[1];

            $sbl .= $discuss->getChunk('board/disSubForumLink',$ph);
        }
        $board->set('subforums',$sbl);
    }

    /* get current category */
    $currentCategory = $board->get('category');
    if (!isset($lastCategory)) {
        $lastCategory = $board->get('category');
    }

    /* if changing categories */
    if ($currentCategory != $lastCategory) {
        $ba['list'] = implode("\n",$boardList);
        unset($ba['rowClass']);

        $list[] = $discuss->getChunk('category/disCategoryLi',$ba);

        $boardList = array(); /* reset current category board list */
        $ba = $board->toArray('',true);
        $ba['rowClass'] = $rowClass;

        $lastCategory = $board->get('category');
        $boardList[] = $discuss->getChunk('board/disBoardLi',$ba);

    } else { /* otherwise add to temp board list */

        $ba = $board->toArray('',true);
        $ba['rowClass'] = $rowClass;
        $lastCategory = $board->get('category');
        $boardList[] = $discuss->getChunk('board/disBoardLi',$ba);
        $rowClass = ($rowClass == 'alt') ? 'even' : 'alt';
    }
}
/* Last category */
$ba['list'] = implode("\n",$boardList);
$ba['rowClass'] = $rowClass;
$list[] = $discuss->getChunk('category/disCategoryLi',$ba);
$list = implode("\n",$list);
unset($currentCategory,$ba,$boards,$board,$lp);

return $list;
<?php
/**
 * Get a list of boards
 *
 * @package discuss
 */

$board = isset($scriptProperties['board']) ? (is_object($scriptProperties['board']) ? $scriptProperties['board']->get('id') : $scriptProperties['board']) : 0;

$cacheKey = 'discuss/board/index';
/* cant use caching yet b/c of unread status
$list = $modx->cacheManager->get($cacheKey);
if (!empty($list)) {
    //return $list;
}*/

/* unread sql */
$unreadSubCriteria = $modx->newQuery('disThreadRead');
$unreadSubCriteria->select($modx->getSelectColumns('disThreadRead','disThreadRead','',array('thread')));
$unreadSubCriteria->where(array(
    'disThreadRead.user' => $discuss->user->get('id'),
    $modx->getSelectColumns('disThreadRead','disThreadRead','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
));
$unreadSubCriteria->prepare();
$unreadSubCriteriaSql = $unreadSubCriteria->toSql();
$unreadCriteria = $modx->newQuery('disThread');
$unreadCriteria->setClassAlias('dp');
$unreadCriteria->select('COUNT('.$modx->getSelectColumns('disThread','','',array('id')).')');
$unreadCriteria->where(array(
    $modx->getSelectColumns('disThread','','',array('id')).' NOT IN ('.$unreadSubCriteriaSql.')',
    $modx->getSelectColumns('disThread','','',array('board')).' = '.$modx->getSelectColumns('disBoard','disBoard','',array('id')),
));
$unreadCriteria->prepare();
$unreadSql = $unreadCriteria->toSql();


/* subboards sql */
$sbCriteria = $modx->newQuery('disBoard');
$sbCriteria->setClassAlias('subBoard');
$sbCriteria->select('GROUP_CONCAT(CONCAT_WS(":",subBoardClosureBoard.id,subBoardClosureBoard.name) SEPARATOR "||") AS name');
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
    '('.$sbSql.') AS '.$modx->escape('subboards'),
    '('.$unreadSql.') AS '.$modx->escape('unread'),
    'last_post_title' => 'LastPost.title',
    'last_post_author' => 'LastPost.author',
    'last_post_createdon' => 'LastPost.createdon',
    'last_post_username' => 'LastPostAuthor.username',
));
$c->innerJoin('disCategory','Category');
$c->innerJoin('disBoardClosure','Descendants');
$c->leftJoin('disPost','LastPost');
$c->leftJoin('disUser','LastPostAuthor','LastPost.author = LastPostAuthor.id');
$c->leftJoin('disBoardUserGroup','UserGroups');
if (isset($board) && $board !== null) {
    $c->where(array(
        'disBoard.parent' => $board,
    ));
}
if (!empty($scriptProperties['category'])) {
    $c->where(array(
        'disBoard.category' => (int)(is_object($scriptProperties['category']) ? $scriptProperties['category']->get('id') : $scriptProperties['category']),
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
if ($discuss->isLoggedIn) {
    $ignoreBoards = $discuss->user->get('ignore_boards');
    if (!empty($ignoreBoards)) {
        $c->where(array(
            'id:NOT IN' => explode(',',$ignoreBoards),
        ));
    }
}
$c->sortby('Category.rank','ASC');
$c->sortby('disBoard.rank','ASC');
$boardObjects = $modx->getCollection('disBoard',$c);

$boards = array();
foreach ($boardObjects as $board) {
    $boards[] = $board->toArray();
}
unset($c);

/* now loop through boards */
$list = array();
$currentCategory = 0;
$rowClass = 'even';
$boardList = array();

/* setup perms */
$canViewProfiles = $modx->hasPermission('discuss.view_profiles');

foreach ($boards as $board) {
    $board['unread-cls'] = ($board['unread'] > 0 && $discuss->isLoggedIn) ? 'dis-unread' : 'dis-read';
    if (!empty($board['last_post_createdon'])) {
        $phs = array(
            'createdon' => strftime($modx->getOption('discuss.date_format'),strtotime($board['last_post_createdon'])),
            'user' => $board['last_post_author'],
            'username' => $board['last_post_username'],
            'author_link' => $canViewProfiles ? '<a class="dis-last-post-by" href="'.$discuss->url.'user/?user='.$board['last_post_author'].'">'.$board['last_post_username'].'</a>' : $board['last_post_username'],
        );
        $lp = $discuss->getChunk('board/disLastPostBy',$phs);
        $board['lastPost'] = $lp;
    } else {
        $board['lastPost'] = '';
    }

    $board['subforums'] = '';
    if (!empty($board['subboards'])) {
        $subBoards = explode('||',$board['subboards']);
        $ph = array();
        $sbl = array();
        foreach ($subBoards as $subboard) {
            $sb = explode(':',$subboard);
            $ph['id'] = $sb[0];
            $ph['title'] = $sb[1];

            $sbl[] = $discuss->getChunk('board/disSubForumLink',$ph);
        }
        $board['subforums'] = implode(",\n",$sbl);
    }

    /* get current category */
    $currentCategory = $board['category'];
    if (!isset($lastCategory)) {
        $lastCategory = $board['category'];
    }

    $board['post_stats'] = $modx->lexicon('discuss.board_post_stats',array(
        'posts' => number_format($board['total_posts']),
        'topics' => number_format($board['num_topics']),
        'unread' => number_format($board['unread']),
    ));

    /* if changing categories */
    if ($currentCategory != $lastCategory) {
        $ba['list'] = implode("\n",$boardList);
        unset($ba['rowClass']);
        $list[] = $discuss->getChunk('category/disCategoryLi',$ba);

        $ba = $board;
        $boardList = array(); /* reset current category board list */
        $ba['rowClass'] = $rowClass;
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk('board/disBoardLi',$ba);

    } else { /* otherwise add to temp board list */
        $ba = $board;
        $ba['rowClass'] = $rowClass;
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk('board/disBoardLi',$ba);
        $rowClass = ($rowClass == 'alt') ? 'even' : 'alt';
    }
}
if (count($boards) > 0) {
    /* Last category */
    $ba['list'] = implode("\n",$boardList);
    $ba['rowClass'] = $rowClass;
    $list[] = $discuss->getChunk('category/disCategoryLi',$ba);
    $list = implode("\n",$list);
    unset($currentCategory,$ba,$boards,$board,$lp);

    $modx->cacheManager->set($cacheKey,$list,3600);
}

return $list;
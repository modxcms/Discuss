<?php
/**
 *
 * @package discuss
 */
/* get user */
if (empty($scriptProperties['user'])) { $modx->sendErrorPage(); }
$user = $modx->getObject('disUser',$scriptProperties['user']);
if ($user == null) { $modx->sendErrorPage(); }

if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}

$modx->lexicon->load('discuss:user');
$placeholders = $user->toArray();
$discuss->setPageTitle($modx->lexicon('discuss.user_ignore_boards_header',array('user' => $user->get('username'))));

/* handle ignoring */
if (!empty($_POST) && !empty($scriptProperties['boards'])) {
    $ignores = array();
    foreach ($scriptProperties['boards'] as $board) {
        $ignores[] = $board;
    }
    $ignores = array_unique($ignores);
    sort($ignores);
    $user->set('ignore_boards',implode(',',$ignores));
    if ($user->save()) {
        $user->clearCache();
        $url = $discuss->url.'user/ignoreboards?user='.$user->get('id');
        $modx->sendRedirect($url);
    }
}

/* build query */
$_groups = $modx->user->getUserGroups();
$cacheKey = 'discuss/user/'.$user->get('id').'/ignoreboards';
$boards = $modx->cacheManager->get($cacheKey);
if (empty($boards) || !$modx->getOption('discuss.caching_enabled',null,false)) {
    $boards = array();
    $c = $modx->newQuery('disBoard');
    $c->select($modx->getSelectColumns('disBoard','disBoard'));
    $c->select(array(
        'Category.name AS category_name',
        'LastPost.title AS last_post_title',
        'LastPost.author AS last_post_author',
        'LastPost.createdon AS last_post_createdon',
        'LastPostAuthor.username AS last_post_username',
        'Ancestors.depth AS depth',
    ));
    $c->innerJoin('disCategory','Category');
    $c->innerJoin('disBoardClosure','Descendants');
    $c->innerJoin('disBoardClosure','Ancestors');
    $c->leftJoin('disPost','LastPost');
    $c->leftJoin('disUser','LastPostAuthor','LastPost.author = LastPostAuthor.id');
    $c->leftJoin('disBoardUserGroup','UserGroups');
    if (!empty($_groups)) {
        $g = array(
            'UserGroups.usergroup:IN' => $_groups,
        );
        $g['OR:UserGroups.usergroup:='] = null;
        $where[] = $g;
        $c->andCondition($where,null,2);
    }
    $c->sortby('Category.rank','ASC');
    $c->sortby('disBoard.rank','ASC');
    $boardObjects = $modx->getCollection('disBoard',$c);
    foreach ($boardObjects as $board) {
        $boards[] = $board->toArray();
    }
    $modx->cacheManager->set($cacheKey,$boards,3600);
}
/* now loop through boards */
$list = array();
$currentCategory = 0;
$rowClass = 'even';
$boardList = array();
$categoryIgnoreList = array('checked' => array(),'all' => array());
$ignores = $user->get('ignore_boards');
$ignores = explode(',',$ignores);
$idx = 0;
foreach ($boards as $board) {
    $boardArray = $board;
    /* get current category */
    $currentCategory = $board['category'];
    if (!isset($lastCategory)) {
        $lastCategory = $board['category'];
        $lastCategoryName = $board['category_name'];
    }

    $boardArray['cls'] = 'dis-board-cb '.$rowClass;
    if (in_array($boardArray['id'],$ignores)) {
        $boardArray['checked'] = 'checked="checked"';
        $categoryIgnoreList['checked'][] = $boardArray['id'];
    }
    $categoryIgnoreList['all'][] = $boardArray['id'];
    
    if ($currentCategory != $lastCategory) {
        $ba['list'] = implode("\n",$boardList);
        unset($ba['rowClass']);
        $ba['checked'] = (count($categoryIgnoreList['all'])-1 == count($categoryIgnoreList['checked'])) ? ' checked="checked"' : '';
        if (empty($ba['category_name'])) $ba['category_name'] = $lastCategoryName;
        $list[] = $discuss->getChunk('board/disBoardCategoryIb',$ba);
        $categoryIgnoreList = array('checked' => array(),'all' => array());

        $ba = $board;
        $boardList = array(); /* reset current category board list */
        $ba['cls'] = 'dis-board-cb '.$rowClass;
        $ba['checked'] = '';
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk('board/disBoardCheckbox',$ba);

    } else { /* otherwise add to temp board list */
        if ($boardArray['depth'] > 0) {
            $boardArray['name'] = str_repeat('---',$boardArray['depth']).$boardArray['name'];
        }
        $boardList[] = $discuss->getChunk('board/disBoardCheckbox',$boardArray);
        $rowClass = ($rowClass == 'alt') ? 'even' : 'alt';
    }
}
if (count($boards) > 0) {
    if (in_array($boardArray['id'],$ignores)) {
        $boardArray['checked'] = 'checked="checked"';
    }
    /* Last category */
    $boardArray['list'] = implode("\n",$boardList);
    $boardArray['rowClass'] = 'dis-board-cb '.$rowClass;
    $list[] = $discuss->getChunk('board/disBoardCategoryIb',$boardArray);
}
$placeholders['boards'] = implode("\n",$list);

/* get left menu */
$placeholders['canEdit'] = $modx->user->get('username') == $user->get('username');
$placeholders['canAccount'] = $modx->user->get('username') == $user->get('username');
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);

return $placeholders;
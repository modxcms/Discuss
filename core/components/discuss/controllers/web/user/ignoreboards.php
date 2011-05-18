<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
/**
 *
 * @package discuss
 */
if (!$discuss->user->isLoggedIn) {
    $discuss->sendUnauthorizedPage();
}
$modx->lexicon->load('discuss:user');
$placeholders = $discuss->user->toArray();
$discuss->setPageTitle($modx->lexicon('discuss.user_ignore_boards_header',array('user' => $discuss->user->get('username'))));

/* handle ignoring */
if (!empty($_POST) && !empty($scriptProperties['boards'])) {
    $ignores = array();
    foreach ($scriptProperties['boards'] as $board) {
        $ignores[] = $board;
    }
    $ignores = array_unique($ignores);
    sort($ignores);
    $discuss->user->set('ignore_boards',implode(',',$ignores));
    if ($discuss->user->save()) {
        $discuss->user->clearCache();
        $url = $discuss->request->makeUrl('user/ignoreboards');
        $modx->sendRedirect($url);
    }
}

/* build query */
$boards = $modx->call('disBoard','fetchList',array(&$modx,false));

/* now loop through boards */
$list = array();
$currentCategory = 0;
$rowClass = 'even';
$boardList = array();
$categoryIgnoreList = array('checked' => array(),'all' => array());
$ignores = $discuss->user->get('ignore_boards');
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

        $ba = $boardArray;
        $boardList = array(); /* reset current category board list */
        $ba['cls'] = 'dis-board-cb '.$rowClass;
        $lastCategory = $board['category'];
        $boardList[] = $discuss->getChunk('board/disBoardCheckbox',$ba);

    } else { /* otherwise add to temp board list */
        if ($boardArray['depth'] - 1 > 0) {
            $boardArray['name'] = str_repeat('---',$boardArray['depth'] - 1).$boardArray['name'];
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
$placeholders['canEdit'] = true;
$placeholders['canAccount'] = true;
$placeholders['canMerge'] = true;
$placeholders['usermenu'] = $discuss->getChunk('disUserMenu',$placeholders);

return $placeholders;
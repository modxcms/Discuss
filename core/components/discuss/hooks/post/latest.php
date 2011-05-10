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
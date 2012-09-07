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
 * Gets a tree node list of boards
 * 
 * @package discuss
 */
$curNode = !empty($scriptProperties['id']) ? $scriptProperties['id'] : 'root_0';
$curNode = explode('_',$curNode);
$type = $curNode[0];
$id = (isset($curNode[1])) ? $curNode[1] : 0;
$nodes = array();
$parentFK = 'parent';
switch ($type) {
    /* get all boards in category - will progress to next step after
     * setting FK to category */
    case 'category':
        $where = array(
            'parent' => 0,
            'category' => $id,
        );
    /* get all subboards */
    case 'board':
        if (!isset($where)) {
            $where = array(
                'parent' => $id,
            );
        }
        $c = $modx->newQuery('disBoard');
        $c->where($where);
        $c->sortby($modx->getSelectColumns('disBoard','disBoard','',array('rank')),'ASC');
        $boards = $modx->getCollection('disBoard',$c);

        foreach ($boards as $board) {
            $boardArray = $board->toArray();

            $boardArray['pk'] = $board->get('id');
            $boardArray['text'] = $board->get('name').' ('.$board->get('id').')';
            $boardArray['leaf'] = false;
            $boardArray['cls'] = 'dis-icon-board';
            $boardArray['classKey'] = 'disBoard';

            unset($boardArray['id']);
            $boardArray['id'] = 'board_'.$board->get('id');
            $nodes[] = $boardArray;
        }

        break;
    /* get all categories */
    default:

        $c = $modx->newQuery('disCategory');
        $c->sortby('rank','ASC');
        $categories = $modx->getCollection('disCategory',$c);

        foreach ($categories as $category) {
            $categoryArray = $category->toArray();

            $categoryArray['pk'] = $category->get('id');
            $categoryArray['text'] = $category->get('name').' ('.$category->get('id').')';
            $categoryArray['leaf'] = false;
            $categoryArray['parent'] = 0;
            $categoryArray['cls'] = 'dis-icon-category';
            $categoryArray['category'] = $category->get('id');
            $categoryArray['classKey'] = 'disCategory';

            unset($categoryArray['id']);
            $categoryArray['id'] = 'category_'.$category->get('id');
            $nodes[] = $categoryArray;
        }
        break;
}



return $this->toJSON($nodes);

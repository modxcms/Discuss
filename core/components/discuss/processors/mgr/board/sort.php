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
 * Sort the boards via drag/drop
 *
 * @package discuss
 * @subpackage processors
 */

$data = $modx->fromJSON($scriptProperties['data']);
$nodes = array();

$target = $data['target'];
$node = $data['node'];
$action = $data['action'];

unset ($data);

$target['id'] = (int)substr($target['id'], strrpos($target['id'], '_') + 1);
$node['id'] = (int)substr($node['id'], strrpos($node['id'], '_') + 1);
if ($node['classKey'] == 'disBoard') {
    switch ($action) {
        case 'append' :
            $board = $modx->getObject('disBoard', $node['id']);
            $board->fromArray(array(
                'category' => $target['cat'],
                'parent' => ($target['classKey'] == 'disBoard') ? $target['id'] : 0
            ));
            $board->save();
            break;
        case 'below' :
        case 'above' :
            $board = $modx->getObject('disBoard', $node['id']);
            if ($target['cat'] != $board->get('category') || $target['parent'] != $board->get('parent')) {
                $board->fromArray(array(
                    'category' => $target['cat'],
                    'parent' => ($target['classKey'] == 'disBoard') ? $target['id'] : 0
                ));
                $board->save();
            }
            $board->reorder($action, $target['id']);
            break;
    }
} else if ($node['classKey'] == 'disCategory') {
    $category = $modx->getObject('disCategory', $node['id']);
    $category->reorder($action, $target['id']);
    // Clear "home" screen board cache
    $del = $modx->cacheManager->delete('discuss/board/index/'.md5(serialize(array(
        'board' => 0,
        'category' => 0,
    ))));
}
return $modx->error->success();
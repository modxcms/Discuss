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
$data = urldecode($scriptProperties['data']);
$data = $modx->fromJSON($data);
$nodes = array();
getNodesFormatted($nodes,$data);

/* readjust cache */
foreach ($nodes as $nodeArray) {
    $node = $modx->getObject($nodeArray['classKey'],$nodeArray['id']);
    if ($node == null) continue;

    switch ($nodeArray['classKey']) {
        case 'disCategory':
            $node->set('rank',$nodeArray['rank']);
            break;
        default:
            $oldParentId = $node->get('parent');
            $node->set('parent',$nodeArray['parent']);
            $node->set('rank',$nodeArray['rank']);
            break;
    }
    $node->save();
}

function getNodesFormatted(&$nodes,$cur_level,$parent = 0) {
    $order = 0;
    foreach ($cur_level as $id => $curNode) {

        $ar = explode('_',$id);
        if (isset($ar[1]) && $ar[1] != '0' && $ar[0] != 'root') {
            $par = explode('_',$parent);
            $nodes[] = array(
                'id' => $ar[1],
                'classKey' => 'dis'.ucfirst($ar[0]),
                'parent' => $par[0] == 'board' ? $par[1] : 0,
                'rank' => $order,
            );
            $order++;
        }
        getNodesFormatted($nodes,$curNode['children'],$id);
    }
}

return $modx->error->success();
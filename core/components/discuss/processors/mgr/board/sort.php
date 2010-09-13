<?php
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
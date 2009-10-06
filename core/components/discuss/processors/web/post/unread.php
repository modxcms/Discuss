<?php
/**
 * @package discuss
 * @subpackage processors
 */
$thread = $modx->getObject('disPost',$scriptProperties['id']);
if ($thread == null) return $modx->error->failure();


if (!empty($scriptProperties['recurse'])) {
    $children = $thread->getDescendants();

    foreach ($children as $child) {
        $child->markAsUnread();
    }
}
return $modx->error->success();
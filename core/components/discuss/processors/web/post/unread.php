<?php
/**
 * @package discuss
 * @subpackage processors
 */
$thread = $modx->getObject('disPost',$scriptProperties['id']);
if ($thread == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));


if (!empty($scriptProperties['recurse'])) {
    $children = $thread->getDescendants();

    foreach ($children as $child) {
        $child->markAsUnread();
    }
}
return $modx->error->success();
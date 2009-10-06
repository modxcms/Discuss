<?php
/**
 * @package discuss
 * @subpackage processors
 */
$thread = $modx->getObject('disPost',$scriptProperties['id']);
if ($thread == null) return $modx->error->failure();

$thread->set('sticky',true);

if (!$thread->save()) {
    return $modx->error->failure();
}

return $modx->error->success();
<?php
/**
 * @package discuss
 * @subpackage processors
 */
$thread = $modx->getObject('disPost',$scriptProperties['id']);
if ($thread == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));

$thread->set('locked',false);

if (!$thread->save()) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_save'));
}

return $modx->error->success();
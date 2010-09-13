<?php
/**
 * Update a post
 *
 * @package discuss
 * @subpackage processors
 */
/* get object */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$post = $modx->getObject('disPost',$scriptProperties['id']);
if ($post == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));

/* set fields */
$post->fromArray($scriptProperties);

/* save post */
if ($post->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_save'));
}

return $modx->error->success('',$post);
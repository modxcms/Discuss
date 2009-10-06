<?php
/**
 * Remove a post
 *
 * @package discuss
 * @subpackage processors
 */
/* get object */
if (empty($_POST['id'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$post = $modx->getObject('disPost',$_POST['id']);
if ($post == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));


$post->fromArray($_POST);


/* save */
if ($post->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_save'));
}

return $modx->error->success('',$post);
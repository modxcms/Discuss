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

/* remove */
if ($post->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_remove'));
}


return $modx->error->success('',$post);
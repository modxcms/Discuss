<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get category */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.category_err_ns'));
$category = $modx->getObject('disCategory',$scriptProperties['id']);
if ($category == null) return $modx->error->failure($modx->lexicon('discuss.category_err_nf'));

/* remove category */
if ($category->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.category_err_remove'));
}


return $modx->error->success('',$category);
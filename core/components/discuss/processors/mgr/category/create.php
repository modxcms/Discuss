<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* assure name */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.category_err_ns_name'));

/* check for dupes */
$alreadyExists = $modx->getObject('disCategory',array('name' => $scriptProperties['name']));
if ($alreadyExists) $modx->error->addField('name',$modx->lexicon('discuss.category_err_ae'));

/* if has errors, return */
if ($modx->error->hasError()) {
    return $modx->error->failure();
}

/* create category */
$category = $modx->newObject('disCategory');
$category->fromArray($scriptProperties);

/* save */
if ($category->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.category_err_save'));
}

return $modx->error->success('',$category);
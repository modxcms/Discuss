<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get category */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.category_err_ns'));
$category = $modx->getObject('disCategory',$scriptProperties['id']);
if (!$category) return $modx->error->failure($modx->lexicon('discuss.category_err_nf'));

/* do validation */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.category_err_ns_name'));

$scriptProperties['collapsible'] = !empty($scriptProperties['collapsible']) ? true : false;

if ($modx->error->hasError()) {
    $modx->error->failure();
}

/* set fields */
$category->fromArray($scriptProperties);

/* save board */
if ($category->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.category_err_save'));
}

return $modx->error->success('',$category);
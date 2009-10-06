<?php
/**
 * @package discuss
 * @subpackage processors
 */
if (empty($_POST['name'])) $modx->error->addField('name','Please enter a valid name.');

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$category = $modx->newObject('disCategory');
$category->fromArray($_POST);

if ($category->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.category_err_save'));
}

return $modx->error->success('',$category);
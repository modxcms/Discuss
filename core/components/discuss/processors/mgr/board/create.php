<?php
/**
 * @package discuss
 * @subpackage processors
 */
if (empty($_POST['name'])) $modx->error->addField('name','Please enter a valid name.');
if (empty($_POST['category'])) $modx->error->addField('category','Please select a Category for this Board to belong in.');

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$board = $modx->newObject('disBoard');
$board->fromArray($_POST);

if ($board->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_save'));
}

return $modx->error->success('',$board);
<?php
/**
 * Create a Board.
 * 
 * @package discuss
 * @subpackage processors
 */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.board_err_ns_name'));
if (empty($scriptProperties['category'])) $modx->error->addField('category',$modx->lexicon('discuss.board_err_ns_category'));

$scriptProperties['locked'] = !empty($scriptProperties['locked']) ? true : false;
$scriptProperties['ignoreable'] = !empty($scriptProperties['ignoreable']) ? true : false;

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$board = $modx->newObject('disBoard');
$board->fromArray($scriptProperties);

if ($board->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_save'));
}

return $modx->error->success('',$board);
<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get board */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.board_err_ns'));
$board = $modx->getObject('disBoard',$scriptProperties['id']);
if (!$board) return $modx->error->failure($modx->lexicon('discuss.board_err_nf'));

/* remove board */
if ($board->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_remove'));
}


return $modx->error->success('',$board);
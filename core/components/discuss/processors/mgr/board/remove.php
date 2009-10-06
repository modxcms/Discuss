<?php
/**
 * @package discuss
 * @subpackage processors
 */
/* get board */
if (empty($_POST['id'])) return $modx->error->failure($modx->lexicon('discuss.board_err_ns'));
$board = $modx->getObject('disBoard',$_POST['id']);
if ($board == null) return $modx->error->failure($modx->lexicon('discuss.board_err_nf'));

/* remove board */
if ($board->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_save'));
}


return $modx->error->success('',$board);
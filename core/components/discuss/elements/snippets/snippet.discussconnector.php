<?php
/**
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$modx->lexicon->load('discuss:default');

if (empty($_REQUEST['action'])) { $_REQUEST['action'] = ''; }
$output = $discuss->loadProcessor($_REQUEST['action']);

return $modx->toJSON($output);
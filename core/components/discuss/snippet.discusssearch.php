<?php
/**
 *
 * @package discuss
 */
require_once  $modx->getOption('core_path').'components/discuss/model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));
$discuss->setSessionPlace('search');

$properties = array();


$o = $discuss->getChunk('disSearch',$properties);
return $discuss->output($o);
<?php
/**
 * @package discuss
 */
require_once  $modx->getOption('core_path').'components/discuss/model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

$properties = array();

if (!empty($_POST)) {
    $properties = array_merge($properties,$_POST);
    $errors = include $discuss->config['processorsPath'].'web/user/login.php';
}

/* output */
$o = $discuss->getChunk('disLogin',$properties);
return $discuss->output($o);
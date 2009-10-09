<?php
/**
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

$properties = array();

if (!empty($_POST)) {
    $properties = array_merge($properties,$_POST);
    $errors = include $discuss->config['processorsPath'].'web/user/login.php';
}

/* output */
return $discuss->output('login',$properties);
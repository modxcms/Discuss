<?php
/**
 * @package discuss
 */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/',$scriptProperties);
if (!($discuss instanceof Discuss)) return '';
$discuss->initialize($modx->context->get('key'));

$properties = array();

if (!empty($_POST)) {
    $properties = array_merge($properties,$_POST);
    $errors = include $discuss->config['processorsPath'].'web/user/login.php';
}

/* output */
return $discuss->output('login',$properties);
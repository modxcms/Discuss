<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

$properties = array();

if (!empty($_POST)) $properties = array_merge($properties,$_POST);

if (!empty($_POST)) {
    $errors = include $discuss->config['processorsPath'].'web/user/register.php';
    $modx->toPlaceholders($errors,'error');
}

/* get board breadcrumb trail */
$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">[[++discuss.forum_title]]</a> / ';
$trail .= 'Register';
$properties['trail'] = $trail;


/* output */
return $discuss->output('register',$properties);


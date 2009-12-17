<?php
/**
 *
 * @package discuss
 */
require_once $modx->getOption('discuss.core_path').'model/discuss/discuss.class.php';
$discuss = new Discuss($modx,$scriptProperties);
$discuss->initialize($modx->context->get('key'));

$placeholders = array();

/* if user is already logged in to Discuss, redirect to forums */
if ($modx->user->isAuthenticated($modx->context->get('key')) && !empty($discuss->user->profile)) {
    $url = $modx->makeUrl($modx->getOption('discuss.board_list_resource'));
    $modx->sendRedirect($url);
}

/* merge POST data */
if (!empty($_POST)) $placeholders = array_merge($placeholders,$_POST);

/* if POST, send to register processor */
if (!empty($_POST)) {
    $errors = include $discuss->config['processorsPath'].'web/user/register.php';
    $modx->toPlaceholders($errors,'error');
}

/* get board breadcrumb trail */
$trail = '<a href="'.$modx->makeUrl($modx->getOption('discuss.board_list_resource')).'">[[++discuss.forum_title]]</a> / ';
$trail .= $modx->lexicon('discuss.register');
$placeholders['trail'] = $trail;


/* output */
return $discuss->output('register',$placeholders);


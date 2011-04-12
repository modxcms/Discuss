<?php
/**
 * @package discuss
 */
$placeholders = array();

if (!empty($_POST)) {
    $properties = array_merge($placeholders,$_POST);
    $errors = include $discuss->config['processorsPath'].'web/user/login.php';
}

/* output */
return $placeholders;
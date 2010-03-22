<?php
/**
 * Properties for the  snippet.
 *
 * @package discuss
 */
/* get default options */
$properties = array(
    array(
        'name' => 'cssBoardRowCls',
        'desc' => 'The CSS class to use for each board row.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'dis-board-li',
    ),
    array(
        'name' => 'postRowTpl',
        'desc' => 'The row chunk for each recent post.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'disPostLi',
    ),
    array(
        'name' => 'limit',
        'desc' => 'The number of recent posts to show.',
        'type' => 'textfield',
        'options' => '',
        'value' => 10,
    ),
/*
    array(
        'name' => '',
        'desc' => '',
        'type' => 'textfield',
        'options' => '',
        'value' => '',
    ),
    */
);

return $properties;
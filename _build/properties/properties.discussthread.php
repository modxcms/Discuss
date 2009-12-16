<?php
/**
 * Properties for the DiscussThread snippet.
 *
 * @package discuss
 */
$properties = array(
    array(
        'name' => 'cssNormalThreadCls',
        'desc' => 'The CSS class for a thread with posts below the hot thread threshold.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'dis-normal-thread',
    ),
    array(
        'name' => 'cssMyNormalThreadCls',
        'desc' => 'The CSS class for a thread with posts below the hot thread threshold that the user has posted in.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'dis-my-normal-thread',
    ),
    array(
        'name' => 'cssHotThreadCls',
        'desc' => 'The CSS class for a thread with posts above or at the hot thread threshold.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'dis-veryhot-thread',
    ),
    array(
        'name' => 'cssMyHotThreadCls',
        'desc' => 'The CSS class for a thread with posts above or at the hot thread threshold that the user has posted in.',
        'type' => 'textfield',
        'options' => '',
        'value' => 'dis-my-veryhot-thread',
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
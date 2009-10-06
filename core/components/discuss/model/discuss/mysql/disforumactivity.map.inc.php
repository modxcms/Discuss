<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disForumActivity']= array (
  'package' => 'discuss',
  'table' => 'forum_activity',
  'fields' => 
  array (
    'day' => NULL,
    'hits' => 0,
    'topics' => 0,
    'replies' => 0,
    'registers' => 0,
    'visitors' => 0,
  ),
  'fieldMeta' => 
  array (
    'day' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
      'index' => 'index',
    ),
    'hits' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'topics' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'replies' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'registers' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'visitors' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
  ),
);
$xpdo_meta_map['disforumactivity']= & $xpdo_meta_map['disForumActivity'];

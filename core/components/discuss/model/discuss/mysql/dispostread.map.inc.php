<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disPostRead']= array (
  'package' => 'discuss',
  'table' => 'discuss_posts_read',
  'fields' => 
  array (
    'user' => 0,
    'board' => 0,
    'post' => 0,
  ),
  'fieldMeta' => 
  array (
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'board' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'post' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
  ),
  'aggregates' => 
  array (
    'User' => 
    array (
      'class' => 'disUser',
      'local' => 'user',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Board' => 
    array (
      'class' => 'disBoard',
      'local' => 'board',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Post' => 
    array (
      'class' => 'disPost',
      'local' => 'post',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

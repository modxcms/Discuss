<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disUserModerated']= array (
  'package' => 'discuss',
  'table' => 'discuss_user_moderated',
  'fields' => 
  array (
    'user' => 0,
    'reason' => 0,
    'register_ip' => '0.0.0.0',
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
    'reason' => 
    array (
      'dbtype' => 'mediumint',
      'precision' => '5',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'register_ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '0.0.0.0',
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
  ),
);

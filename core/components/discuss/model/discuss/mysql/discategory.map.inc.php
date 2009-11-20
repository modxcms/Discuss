<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disCategory']= array (
  'package' => 'discuss',
  'table' => 'categories',
  'fields' => 
  array (
    'name' => '',
    'description' => '',
    'collapsible' => 1,
    'rank' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'default' => '',
      'null' => false,
      'index' => 'index',
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'default' => '',
      'null' => false,
    ),
    'collapsible' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
    ),
    'rank' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
  ),
  'composites' => 
  array (
    'Boards' => 
    array (
      'class' => 'disBoard',
      'local' => 'id',
      'foreign' => 'category',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);

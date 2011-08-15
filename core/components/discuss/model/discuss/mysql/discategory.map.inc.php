<?php
/**
 * @package discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disCategory']= array (
  'package' => 'discuss',
  'table' => 'discuss_categories',
  'fields' => 
  array (
    'name' => '',
    'description' => '',
    'collapsible' => 1,
    'rank' => 0,
    'default_moderators' => NULL,
    'default_usergroups' => NULL,
    'integrated_id' => 0,
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
    'default_moderators' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
    ),
    'default_usergroups' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
    ),
    'integrated_id' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'name' => 
    array (
      'alias' => 'name',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'name' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
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

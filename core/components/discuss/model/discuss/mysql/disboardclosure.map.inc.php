<?php
/**
 * @package discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disBoardClosure']= array (
  'package' => 'discuss',
  'version' => '1.1',
  'table' => 'discuss_boards_closure',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'ancestor' => 0,
    'descendant' => 0,
    'depth' => 0,
  ),
  'fieldMeta' => 
  array (
    'ancestor' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'descendant' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'pk',
    ),
    'depth' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'ancestor' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'descendant' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Ancestor' => 
    array (
      'class' => 'disBoard',
      'local' => 'ancestor',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Descendant' => 
    array (
      'class' => 'disBoard',
      'local' => 'descendant',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

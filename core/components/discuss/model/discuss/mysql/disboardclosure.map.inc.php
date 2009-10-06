<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disBoardClosure']= array (
  'package' => 'discuss',
  'table' => 'dis_boards_closure',
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
if (XPDO_PHP4_MODE) $xpdo_meta_map['disBoardClosure']['aggregates']= array_merge($xpdo_meta_map['disBoardClosure']['aggregates'], array_change_key_case($xpdo_meta_map['disBoardClosure']['aggregates']));
$xpdo_meta_map['disboardclosure']= & $xpdo_meta_map['disBoardClosure'];

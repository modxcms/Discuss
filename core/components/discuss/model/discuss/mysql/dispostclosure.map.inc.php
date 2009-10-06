<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disPostClosure']= array (
  'package' => 'discuss',
  'table' => 'posts_closure',
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
      'class' => 'disPost',
      'local' => 'ancestor',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Descendant' => 
    array (
      'class' => 'disPost',
      'local' => 'descendant',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disPostClosure']['aggregates']= array_merge($xpdo_meta_map['disPostClosure']['aggregates'], array_change_key_case($xpdo_meta_map['disPostClosure']['aggregates']));
$xpdo_meta_map['dispostclosure']= & $xpdo_meta_map['disPostClosure'];

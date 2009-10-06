<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disPost']= array (
  'package' => 'discuss',
  'table' => 'dis_posts',
  'fields' => 
  array (
    'board' => 0,
    'parent' => 0,
    'title' => '',
    'message' => NULL,
    'author' => 0,
    'createdon' => NULL,
    'editedby' => 0,
    'editedon' => NULL,
    'icon' => '',
    'allow_replies' => 1,
    'rank' => NULL,
    'ip' => '0.0.0.0',
    'views' => 0,
    'locked' => 0,
    'sticky' => 0,
  ),
  'fieldMeta' => 
  array (
    'board' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'default' => '',
      'null' => false,
      'index' => 'index',
    ),
    'message' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
    ),
    'author' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'editedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
    ),
    'editedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'icon' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'allow_replies' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'rank' => 
    array (
      'dbtype' => 'tinytext',
      'phptype' => 'string',
      'null' => false,
    ),
    'ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '0.0.0.0',
    ),
    'views' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
    ),
    'locked' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
    'sticky' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Board' => 
    array (
      'class' => 'disBoard',
      'local' => 'board',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Author' => 
    array (
      'class' => 'modUser',
      'local' => 'author',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'AuthorProfile' => 
    array (
      'class' => 'disUserProfile',
      'local' => 'author',
      'foreign' => 'user',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'CreatedBy' => 
    array (
      'class' => 'modUser',
      'local' => 'author',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'EditedBy' => 
    array (
      'class' => 'modUser',
      'local' => 'author',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Parent' => 
    array (
      'class' => 'disPost',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'composites' => 
  array (
    'Children' => 
    array (
      'class' => 'disPost',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Ancestors' => 
    array (
      'class' => 'disPostClosure',
      'local' => 'id',
      'foreign' => 'ancestor',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Descendants' => 
    array (
      'class' => 'disPostClosure',
      'local' => 'id',
      'foreign' => 'descendant',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PostReads' => 
    array (
      'class' => 'disPostRead',
      'local' => 'id',
      'foreign' => 'post',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disPost']['aggregates']= array_merge($xpdo_meta_map['disPost']['aggregates'], array_change_key_case($xpdo_meta_map['disPost']['aggregates']));
if (XPDO_PHP4_MODE) $xpdo_meta_map['disPost']['composites']= array_merge($xpdo_meta_map['disPost']['composites'], array_change_key_case($xpdo_meta_map['disPost']['composites']));
$xpdo_meta_map['dispost']= & $xpdo_meta_map['disPost'];

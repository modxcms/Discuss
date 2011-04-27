<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disBoard']= array (
  'package' => 'discuss',
  'table' => 'discuss_boards',
  'fields' => 
  array (
    'category' => 0,
    'parent' => 0,
    'name' => '',
    'description' => '',
    'last_post' => 0,
    'num_topics' => 0,
    'num_replies' => 0,
    'total_posts' => 0,
    'ignoreable' => 1,
    'rank' => 0,
    'map' => '',
    'minimum_post_level' => 9999,
    'status' => 1,
    'integrated_id' => 0,
  ),
  'fieldMeta' => 
  array (
    'category' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'parent' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
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
      'null' => false,
      'default' => '',
    ),
    'last_post' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'num_topics' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'num_replies' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'total_posts' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'ignoreable' => 
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
    'map' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'minimum_post_level' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 9999,
    ),
    'status' => 
    array (
      'dbtype' => 'integer',
      'precision' => '4',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 1,
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
  'aggregates' => 
  array (
    'Category' => 
    array (
      'class' => 'disCategory',
      'local' => 'category',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Parent' => 
    array (
      'class' => 'disBoard',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'LastPost' => 
    array (
      'class' => 'disPost',
      'local' => 'last_post',
      'foreign' => 'id',
      'cardinality' => 'many',
      'owner' => 'foreign',
    ),
  ),
  'composites' => 
  array (
    'Children' => 
    array (
      'class' => 'disBoard',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Moderators' => 
    array (
      'class' => 'disModerator',
      'local' => 'id',
      'foreign' => 'board',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'UserGroups' => 
    array (
      'class' => 'disBoardUserGroup',
      'local' => 'id',
      'foreign' => 'board',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Ancestors' => 
    array (
      'class' => 'disBoardClosure',
      'local' => 'id',
      'foreign' => 'ancestor',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Descendants' => 
    array (
      'class' => 'disBoardClosure',
      'local' => 'id',
      'foreign' => 'descendant',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Posts' => 
    array (
      'class' => 'disPost',
      'local' => 'id',
      'foreign' => 'board',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PostReads' => 
    array (
      'class' => 'disPostRead',
      'local' => 'id',
      'foreign' => 'board',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Notifications' => 
    array (
      'class' => 'disUserNotification',
      'local' => 'id',
      'foreign' => 'board',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);

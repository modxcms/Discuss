<?php
/**
 * @package discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disThread']= array (
  'package' => 'discuss',
  'table' => 'discuss_threads',
  'fields' => 
  array (
    'class_key' => 'disThreadDiscussion',
    'board' => 0,
    'title' => '',
    'post_first' => 0,
    'post_last' => 0,
    'author_first' => 0,
    'author_last' => 0,
    'replies' => 0,
    'views' => 0,
    'locked' => 0,
    'answered' => 0,
    'sticky' => 0,
    'private' => 0,
    'users' => '',
    'last_view_ip' => '',
    'integrated_id' => 0,
  ),
  'fieldMeta' => 
  array (
    'class_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '120',
      'phptype' => 'string',
      'null' => false,
      'default' => 'disThreadDiscussion',
      'index' => 'index',
    ),
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
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
      'index' => 'index',
    ),
    'post_first' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'post_last' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'author_first' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'author_last' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'replies' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'views' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'locked' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'answered' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'sticky' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'private' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'users' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => true,
      'default' => '',
    ),
    'last_view_ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '120',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
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
    'FirstAuthor' => 
    array (
      'class' => 'disUser',
      'local' => 'author_first',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'LastAuthor' => 
    array (
      'class' => 'disUser',
      'local' => 'author_last',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'FirstPost' => 
    array (
      'class' => 'disPost',
      'local' => 'post_first',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'LastPost' => 
    array (
      'class' => 'disPost',
      'local' => 'post_last',
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
  ),
  'composites' => 
  array (
    'Reads' => 
    array (
      'class' => 'disThreadRead',
      'local' => 'id',
      'foreign' => 'thread',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Posts' => 
    array (
      'class' => 'disPost',
      'local' => 'id',
      'foreign' => 'thread',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Notifications' => 
    array (
      'class' => 'disUserNotification',
      'local' => 'id',
      'foreign' => 'thread',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Users' => 
    array (
      'class' => 'disThreadUser',
      'local' => 'id',
      'foreign' => 'thread',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);

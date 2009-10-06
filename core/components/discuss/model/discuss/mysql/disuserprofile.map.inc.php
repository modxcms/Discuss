<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disUserProfile']= array (
  'package' => 'discuss',
  'table' => 'dis_user_profile',
  'fields' => 
  array (
    'user' => 0,
    'createdon' => NULL,
    'name_first' => '',
    'name_last' => '',
    'email' => '',
    'gender' => '',
    'birthdate' => NULL,
    'website' => '',
    'location' => '',
    'ip' => '0.0.0.0',
    'status' => 0,
    'confirmed' => 0,
    'confirmedon' => NULL,
    'last_login' => NULL,
    'last_active' => NULL,
    'ignore_boards' => '',
    'signature' => '',
    'title' => '',
    'avatar' => '',
    'thread_last_visited' => 0,
    'posts' => 0,
    'show_email' => 1,
    'show_online' => 1,
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
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'name_first' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'name_last' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'email' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '200',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'gender' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '10',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'birthdate' => 
    array (
      'dbtype' => 'date',
      'phptype' => 'date',
      'null' => false,
    ),
    'website' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'location' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '0.0.0.0',
    ),
    'status' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'confirmed' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'confirmedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'last_login' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'last_active' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
      'index' => 'index',
    ),
    'ignore_boards' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'signature' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'title' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'avatar' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'thread_last_visited' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'posts' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'show_email' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
    'show_online' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
    ),
  ),
  'aggregates' => 
  array (
    'User' => 
    array (
      'class' => 'modUser',
      'local' => 'user',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'ThreadLastVisited' => 
    array (
      'class' => 'disPost',
      'local' => 'thread_last_visited',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'composites' => 
  array (
    'UserModerated' => 
    array (
      'class' => 'disUserModerated',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Posts' => 
    array (
      'class' => 'disPost',
      'local' => 'id',
      'foreign' => 'author',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'PostReads' => 
    array (
      'class' => 'disPostRead',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Session' => 
    array (
      'class' => 'disSession',
      'local' => 'user',
      'foreign' => 'user',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Friends' => 
    array (
      'class' => 'disUserFriend',
      'local' => 'user',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disUserProfile']['aggregates']= array_merge($xpdo_meta_map['disUserProfile']['aggregates'], array_change_key_case($xpdo_meta_map['disUserProfile']['aggregates']));
if (XPDO_PHP4_MODE) $xpdo_meta_map['disUserProfile']['composites']= array_merge($xpdo_meta_map['disUserProfile']['composites'], array_change_key_case($xpdo_meta_map['disUserProfile']['composites']));
$xpdo_meta_map['disuserprofile']= & $xpdo_meta_map['disUserProfile'];

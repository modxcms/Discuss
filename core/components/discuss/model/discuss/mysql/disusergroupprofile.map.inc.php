<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disUserGroupProfile']= array (
  'package' => 'discuss',
  'table' => 'dis_usergroup_profiles',
  'fields' => 
  array (
    'usergroup' => 0,
    'post_based' => 0,
    'min_posts' => 0,
    'color' => '',
    'image' => '',
  ),
  'fieldMeta' => 
  array (
    'usergroup' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'post_based' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'min_posts' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'color' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '20',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'image' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
  ),
  'aggregates' => 
  array (
    'UserGroup' => 
    array (
      'class' => 'modUserGroup',
      'local' => 'user',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'composites' => 
  array (
    'Boards' => 
    array (
      'class' => 'disBoardUserGroup',
      'local' => 'usergroup',
      'foreign' => 'usergroup',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disUserGroupProfile']['aggregates']= array_merge($xpdo_meta_map['disUserGroupProfile']['aggregates'], array_change_key_case($xpdo_meta_map['disUserGroupProfile']['aggregates']));
if (XPDO_PHP4_MODE) $xpdo_meta_map['disUserGroupProfile']['composites']= array_merge($xpdo_meta_map['disUserGroupProfile']['composites'], array_change_key_case($xpdo_meta_map['disUserGroupProfile']['composites']));
$xpdo_meta_map['disusergroupprofile']= & $xpdo_meta_map['disUserGroupProfile'];

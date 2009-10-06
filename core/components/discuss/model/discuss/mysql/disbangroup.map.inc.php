<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disBanGroup']= array (
  'package' => 'discuss',
  'table' => 'ban_groups',
  'fields' => 
  array (
    'name' => '',
    'ip' => '',
    'reason' => '',
    'createdon' => NULL,
    'createdby' => 0,
    'expires' => 0,
    'prevent_access' => 0,
    'prevent_register' => 0,
    'prevent_login' => 0,
    'prevent_post' => 0,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
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
      'default' => '',
      'index' => 'index',
    ),
    'reason' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'createdby' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'expires' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
    ),
    'prevent_access' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'prevent_register' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'prevent_login' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
    'prevent_post' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'CreatedBy' => 
    array (
      'class' => 'modUser',
      'local' => 'createdby',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
  'composites' => 
  array (
    'BanItems' => 
    array (
      'class' => 'disBanItem',
      'local' => 'id',
      'foreign' => 'grp',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disBanGroup']['aggregates']= array_merge($xpdo_meta_map['disBanGroup']['aggregates'], array_change_key_case($xpdo_meta_map['disBanGroup']['aggregates']));
if (XPDO_PHP4_MODE) $xpdo_meta_map['disBanGroup']['composites']= array_merge($xpdo_meta_map['disBanGroup']['composites'], array_change_key_case($xpdo_meta_map['disBanGroup']['composites']));
$xpdo_meta_map['disbangroup']= & $xpdo_meta_map['disBanGroup'];

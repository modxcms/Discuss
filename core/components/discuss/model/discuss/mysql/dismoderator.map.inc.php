<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disModerator']= array (
  'package' => 'discuss',
  'table' => 'dis_moderators',
  'fields' => 
  array (
    'user' => 0,
    'board' => 0,
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
    'board' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
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
    'UserProfile' => 
    array (
      'class' => 'disUserProfile',
      'local' => 'user',
      'foreign' => 'user',
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
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disModerator']['aggregates']= array_merge($xpdo_meta_map['disModerator']['aggregates'], array_change_key_case($xpdo_meta_map['disModerator']['aggregates']));
$xpdo_meta_map['dismoderator']= & $xpdo_meta_map['disModerator'];

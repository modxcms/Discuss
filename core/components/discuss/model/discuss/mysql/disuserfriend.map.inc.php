<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disUserFriend']= array (
  'package' => 'discuss',
  'table' => 'dis_user_friends',
  'fields' => 
  array (
    'user' => 0,
    'friend' => 0,
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
    ),
    'friend' => 
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
    'Friend' => 
    array (
      'class' => 'modUser',
      'local' => 'friend',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disUserFriend']['aggregates']= array_merge($xpdo_meta_map['disUserFriend']['aggregates'], array_change_key_case($xpdo_meta_map['disUserFriend']['aggregates']));
$xpdo_meta_map['disuserfriend']= & $xpdo_meta_map['disUserFriend'];

<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disUserFriend']= array (
  'package' => 'discuss',
  'table' => 'discuss_user_friends',
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

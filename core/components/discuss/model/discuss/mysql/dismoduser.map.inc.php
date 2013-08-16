<?php
/**
 * @package discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disModUser']= array (
  'package' => 'discuss',
  'version' => '1.1',
  'extends' => 'modUser',
  'fields' => 
  array (
  ),
  'fieldMeta' => 
  array (
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
    'Reads' => 
    array (
      'class' => 'disThreadRead',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Session' => 
    array (
      'class' => 'disSession',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
    'Friends' => 
    array (
      'class' => 'disUserFriend',
      'local' => 'id',
      'foreign' => 'user',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'disProfile' => 
    array (
      'class' => 'disProfile',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'one',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'PrimaryGroup' => 
    array (
      'class' => 'modUserGroup',
      'local' => 'primary_group',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'PrimaryDiscussGroup' => 
    array (
      'class' => 'disUserGroupProfile',
      'local' => 'primary_group',
      'foreign' => 'usergroup',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'ThreadLastVisited' => 
    array (
      'class' => 'disThread',
      'local' => 'thread_last_visited',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Activity' => 
    array (
      'class' => 'modActiveUser',
      'local' => 'id',
      'foreign' => 'internalKey',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

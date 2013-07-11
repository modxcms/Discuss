<?php
/**
 * @package discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disThreadParticipant']= array (
  'package' => 'discuss',
  'version' => '1.1',
  'table' => 'discuss_thread_participants',
  'extends' => 'xPDOObject',
  'fields' => 
  array (
    'thread' => NULL,
    'user' => NULL,
  ),
  'fieldMeta' => 
  array (
    'thread' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
    ),
    'user' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'PRIMARY' => 
    array (
      'alias' => 'PRIMARY',
      'primary' => true,
      'unique' => false,
      'columns' => 
      array (
        'thread' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'user' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Thread' => 
    array (
      'class' => 'disThread',
      'local' => 'thread',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'User' => 
    array (
      'class' => 'disUser',
      'local' => 'user',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

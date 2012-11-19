<?php
/**
 * @package discuss
 * @subpackage mysql
 */
$xpdo_meta_map['disPost']= array (
  'package' => 'discuss',
  'version' => '1.1',
  'table' => 'discuss_posts',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'board' => 0,
    'thread' => 0,
    'parent' => 0,
    'title' => '',
    'message' => NULL,
    'author' => 0,
    'createdon' => NULL,
    'editedby' => 0,
    'editedon' => NULL,
    'icon' => '',
    'allow_replies' => 1,
    'rank' => NULL,
    'ip' => '0.0.0.0',
    'integrated_id' => 0,
    'depth' => 0,
    'answer' => 0,
  ),
  'fieldMeta' => 
  array (
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
    'thread' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'parent' => 
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
      'default' => '',
      'null' => false,
    ),
    'message' => 
    array (
      'dbtype' => 'mediumtext',
      'phptype' => 'string',
      'null' => false,
    ),
    'author' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
    'createdon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
      'index' => 'index',
    ),
    'editedby' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
    ),
    'editedon' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
    'icon' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'allow_replies' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
    'rank' => 
    array (
      'dbtype' => 'tinytext',
      'phptype' => 'string',
      'null' => true,
    ),
    'ip' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
      'default' => '0.0.0.0',
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
    'depth' => 
    array (
      'dbtype' => 'integer',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'answer' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'boolean',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
  ),
  'indexes' => 
  array (
    'board' => 
    array (
      'alias' => 'board',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'board' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'thread' => 
    array (
      'alias' => 'thread',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'thread' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'parent' => 
    array (
      'alias' => 'parent',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'parent' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'author' => 
    array (
      'alias' => 'author',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'author' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'createdon' => 
    array (
      'alias' => 'createdon',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'createdon' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'allow_replies' => 
    array (
      'alias' => 'allow_replies',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'allow_replies' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'depth' => 
    array (
      'alias' => 'depth',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'depth' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'answer' => 
    array (
      'alias' => 'answer',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'answer' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Ancestors' => 
    array (
      'class' => 'disPostClosure',
      'local' => 'id',
      'foreign' => 'ancestor',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Descendants' => 
    array (
      'class' => 'disPostClosure',
      'local' => 'id',
      'foreign' => 'descendant',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
    'Attachments' => 
    array (
      'class' => 'disPostAttachment',
      'local' => 'id',
      'foreign' => 'post',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
  'aggregates' => 
  array (
    'Board' => 
    array (
      'class' => 'disBoard',
      'local' => 'board',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Thread' => 
    array (
      'class' => 'disThread',
      'local' => 'thread',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Author' => 
    array (
      'class' => 'disUser',
      'local' => 'author',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'CreatedBy' => 
    array (
      'class' => 'disUser',
      'local' => 'author',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'EditedBy' => 
    array (
      'class' => 'disUser',
      'local' => 'editedby',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Parent' => 
    array (
      'class' => 'disPost',
      'local' => 'parent',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
    'Children' => 
    array (
      'class' => 'disPost',
      'local' => 'id',
      'foreign' => 'parent',
      'cardinality' => 'many',
      'owner' => 'local',
    ),
  ),
);

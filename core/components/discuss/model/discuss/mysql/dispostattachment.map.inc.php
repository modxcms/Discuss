<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disPostAttachment']= array (
  'package' => 'discuss',
  'table' => 'posts_attachments',
  'fields' => 
  array (
    'post' => 0,
    'board' => 0,
    'filename' => '',
    'createdon' => NULL,
    'filesize' => 0,
    'downloads' => 0,
  ),
  'fieldMeta' => 
  array (
    'post' => 
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
    'filename' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
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
    'filesize' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
    'downloads' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'int',
      'null' => false,
      'default' => 0,
    ),
  ),
  'aggregates' => 
  array (
    'Post' => 
    array (
      'class' => 'disPost',
      'local' => 'post',
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
);
if (XPDO_PHP4_MODE) $xpdo_meta_map['disPostAttachment']['aggregates']= array_merge($xpdo_meta_map['disPostAttachment']['aggregates'], array_change_key_case($xpdo_meta_map['disPostAttachment']['aggregates']));
$xpdo_meta_map['dispostattachment']= & $xpdo_meta_map['disPostAttachment'];

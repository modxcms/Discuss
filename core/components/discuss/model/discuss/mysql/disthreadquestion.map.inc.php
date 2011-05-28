<?php
/**
 * @package discuss
 */
$xpdo_meta_map['disThreadQuestion']= array (
  'package' => 'discuss',
  'fields' => 
  array (
    'post_answer' => 0,
  ),
  'fieldMeta' => 
  array (
    'post_answer' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'attributes' => 'unsigned',
      'default' => 0,
      'null' => false,
      'index' => 'index',
    ),
  ),
  'aggregates' => 
  array (
    'AnswerPost' => 
    array (
      'class' => 'disPost',
      'local' => 'post_answer',
      'foreign' => 'id',
      'cardinality' => 'one',
      'owner' => 'foreign',
    ),
  ),
);

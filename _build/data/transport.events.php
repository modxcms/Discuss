<?php
/**
 * Adds custom System Events
 *
 * @package discuss
 * @subpackage build
 */
$events = array();

/* Attachment Verify */
$events['OnDiscussAttachmentVerify']= $modx->newObject('modEvent');
$events['OnDiscussAttachmentVerify']->fromArray(array (
  'name' => 'OnDiscussAttachmentVerify',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);

/* Post Render */
$events['OnDiscussBeforePostSave']= $modx->newObject('modEvent');
$events['OnDiscussBeforePostSave']->fromArray(array (
  'name' => 'OnDiscussBeforePostSave',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);
$events['OnDiscussPostBeforeRemove']= $modx->newObject('modEvent');
$events['OnDiscussPostBeforeRemove']->fromArray(array (
  'name' => 'OnDiscussPostBeforeRemove',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);
$events['OnDiscussPostBeforeRender']= $modx->newObject('modEvent');
$events['OnDiscussPostBeforeRender']->fromArray(array (
  'name' => 'OnDiscussPostBeforeRender',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);
$events['OnDiscussPostCustomParser']= $modx->newObject('modEvent');
$events['OnDiscussPostCustomParser']->fromArray(array (
  'name' => 'OnDiscussPostCustomParser',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);
$events['OnDiscussPostFetchContent']= $modx->newObject('modEvent');
$events['OnDiscussPostFetchContent']->fromArray(array (
  'name' => 'OnDiscussPostFetchContent',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);
$events['OnDiscussPostRemove']= $modx->newObject('modEvent');
$events['OnDiscussPostRemove']->fromArray(array (
  'name' => 'OnDiscussPostRemove',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);
$events['OnDiscussPostSave']= $modx->newObject('modEvent');
$events['OnDiscussPostSave']->fromArray(array (
  'name' => 'OnDiscussPostSave',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);

return $events;
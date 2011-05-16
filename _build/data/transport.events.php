<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
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

/* Ban/Moderation */
$events['OnDiscussBeforeBanUser']= $modx->newObject('modEvent');
$events['OnDiscussBeforeBanUser']->fromArray(array (
  'name' => 'OnDiscussBeforeBanUser',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);
$events['OnDiscussBanUser']= $modx->newObject('modEvent');
$events['OnDiscussBanUser']->fromArray(array (
  'name' => 'OnDiscussBanUser',
  'service' => 1,
  'groupname' => 'Discuss',
), '', true, true);


return $events;
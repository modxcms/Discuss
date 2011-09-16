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
 * Default Discuss Access Policies
 *
 * @package discuss
 * @subpackage build
 */
$policies = array();
$policies[1]= $modx->newObject('modAccessPolicy');
$policies[1]->fromArray(array (
  'id' => 1,
  'name' => 'Discuss Administrator Policy',
  'description' => 'A policy with all Discuss permissions.',
  'parent' => 0,
  'class' => '',
  'lexicon' => 'discuss:permissions',
  'data' => '{"discuss.pm_remove":true,"discuss.pm_send":true,"discuss.pm_view":true,"discuss.search":true,"discuss.thread_attach":true,"discuss.thread_create":true,"discuss.thread_lock":true,"discuss.thread_merge":true,"discuss.thread_modify":true,"discuss.thread_move":true,"discuss.thread_print":true,"discuss.thread_remove":true,"discuss.thread_reply":true,"discuss.thread_report":true,"discuss.thread_send":true,"discuss.thread_split":true,"discuss.thread_stick":true,"discuss.thread_subscribe":true,"discuss.thread_unlock":true,"discuss.thread_unstick":true,"discuss.track_ip":true,"discuss.view_attachments":true,"discuss.view_emails":true,"discuss.view_memberlist":true,"discuss.view_online":true,"discuss.view_profiles":true,"discuss.view_statistics":true}',
), '', true, true);

$policies[2]= $modx->newObject('modAccessPolicy');
$policies[2]->fromArray(array (
  'id' => 2,
  'name' => 'Discuss Moderator Policy',
  'description' => 'A policy with Discuss moderator permissions.',
  'parent' => 0,
  'class' => '',
  'lexicon' => 'discuss:permissions',
  'data' => '{"discuss.pm_remove":true,"discuss.pm_send":true,"discuss.pm_view":true,"discuss.search":true,"discuss.thread_attach":true,"discuss.thread_create":true,"discuss.thread_lock":true,"discuss.thread_merge":true,"discuss.thread_modify":true,"discuss.thread_move":true,"discuss.thread_print":true,"discuss.thread_remove":false,"discuss.thread_reply":true,"discuss.thread_report":true,"discuss.thread_send":true,"discuss.thread_split":true,"discuss.thread_stick":true,"discuss.thread_subscribe":true,"discuss.thread_unlock":true,"discuss.thread_unstick":true,"discuss.track_ip":false,"discuss.view_attachments":true,"discuss.view_emails":true,"discuss.view_memberlist":true,"discuss.view_online":true,"discuss.view_profiles":true,"discuss.view_statistics":false}',
), '', true, true);

$policies[3]= $modx->newObject('modAccessPolicy');
$policies[3]->fromArray(array (
  'id' => 3,
  'name' => 'Discuss Member Policy',
  'description' => 'A policy with basic Discuss posting, viewing and editing permissions for forum members.',
  'parent' => 0,
  'class' => '',
  'lexicon' => 'discuss:permissions',
  'data' => '{"discuss.pm_remove":true,"discuss.pm_send":true,"discuss.pm_view":true,"discuss.search":true,"discuss.thread_attach":true,"discuss.thread_create":true,"discuss.thread_lock":false,"discuss.thread_merge":false,"discuss.thread_modify":true,"discuss.thread_move":false,"discuss.thread_print":true,"discuss.thread_remove":false,"discuss.thread_reply":true,"discuss.thread_report":true,"discuss.thread_send":true,"discuss.thread_split":false,"discuss.thread_stick":false,"discuss.thread_subscribe":true,"discuss.thread_unlock":false,"discuss.thread_unstick":false,"discuss.track_ip":false,"discuss.view_attachments":true,"discuss.view_emails":true,"discuss.view_memberlist":true,"discuss.view_online":true,"discuss.view_profiles":true,"discuss.view_statistics":false}',
), '', true, true);

return $policies;
<?php
/**
 * Default Quip Access Policies
 *
 * @package quip
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

return $policies;
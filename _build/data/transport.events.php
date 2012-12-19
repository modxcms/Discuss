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

$eventsMap = array(
    'OnDiscussAttachmentVerify' => array(),
    'OnDiscussBeforePostSave' => array(),
    'OnDiscussPostBeforeRemove' => array(),
    'OnDiscussPostBeforeRender' => array(),
    'OnDiscussPostCustomParser' => array(),
    'OnDiscussPostFetchContent' => array(),
    'OnDiscussPostRemove' => array(),
    'OnDiscussPostSave' => array(),
    'OnDiscussBeforeBanUser' => array(),
    'OnDiscussBanUser' => array(),
    'OnDiscussRenderHome' => array(),
    'OnDiscussRenderBoard' => array(),
    'OnDiscussRenderThread' => array(),
    'OnDiscussBeforeMarkAsAnswer' => array(),
    'OnDiscussBeforeUnmarkAsAnswer' => array(),
    'OnDiscussMarkAsAnswer' => array(),
    'OnDiscussUnmarkAsAnswer' => array(),
);


$events = array();
$defaultService = 1;
$defaultGroupname = 'Discuss';

foreach ($eventsMap as $key => $options) {
    $events[$key] = $modx->newObject('modEvent');
    $events[$key]->set('name', $key);
    $events[$key]->set('service', (!empty($options['service'])) ? $options['service'] : $defaultService);
    $events[$key]->set('groupname', (!empty($options['groupname'])) ? $options['groupname'] : $defaultGroupname);
}

return $events;

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
 * Handle Discuss link creation
 *
 * @var modX $modx
 * @var Discuss $discuss
 * @var array $scriptProperties
 * 
 * @package discuss
 */
 
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');

$action = '';

if (!empty($scriptProperties['action'])) {
    $action = $scriptProperties['action'];
}

$params = array();

if (!empty($scriptProperties['params'])) {
    $params = $modx->fromJSON($scriptProperties['params'], true);
}

$discuss->loadRequest();
$discuss->url = $modx->makeUrl($modx->getOption('discuss.forums_resource_id'));
return $discuss->request->makeUrl($action,$params);
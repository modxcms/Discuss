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
 * Handle Discuss URL Routing
 *
 * @var modX $modx
 * @var Discuss $discuss
 * @package discuss
 */
 
if ($modx->event->name != 'OnPageNotFound' || $modx->request->getResourceMethod() != 'alias' || $modx->context->key == 'mgr') {
    return true;
}
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return true;

$discuss->url = $modx->makeUrl($modx->getOption('discuss.forums_resource_id'));

$request = $modx->request->getResourceIdentifier('alias');

// Checking to stop everything if we are sure that this is not our path or it doesn't have any parameters to parse (exact match of alias to the base resource alias)
if (strpos($request, $discuss->url) !== 0 || strlen($request) === strlen($discuss->url) || array_key_exists($request, $modx->aliasMap)) {
    return true;
}

$containersuffix = $modx->getOption('container_suffix', null, '/');

$chunk = substr($request, 0, strrpos($request, $containersuffix));
while (!empty($chunk)) {
    if (array_key_exists($request, $modx->aliasMap)) {
        return true;
    }
    $chunk = substr($chunk, 0, strrpos($chunk, $containersuffix));
}

// Now lets check that we have a valid manifest
$f = $discuss->config['themePath'].'manifest.php';
if (!file_exists($f)) {
    return true;
}
$manifest = require $f;

// Here I am sure that the URL is the one we need. Now lets break it into chunks (delimited by '/') and check against the manifest.
$chunks = explode('/', $request);


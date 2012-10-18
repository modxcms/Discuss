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

$discuss->loadRequest();
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

// Now we sort the manifest array just to make sure that the actions of maximum length are checked first
if (!function_exists('sorter')) {
    function sorter($a, $b)
    {
        return strlen($a) - strlen($b);    
    }
}
uksort($manifest, "sorter");

// Here I am sure that the URL is the one we need. Now lets search it for the actions.
$request = ltrim(substr($request, strlen($discuss->url)), '/');
if (!function_exists('url_parser')) {
    function url_parser($action, $requested, $furls)
    {
        foreach($furls as $furl) {
            if (array_key_exists('data', $furl)) {
                $paramnumber = count($furl['data']);
                if ($paramnumber > 0) {
                    $parameters = array();
                    $matched = 0;
                    $request = $requested;
                    foreach ($furl['data'] as $param) {
                        $previousmatch = $matched;
                        switch ($param['type']) {
                            case 'action':
                                $position = strpos($request, $key);
                                if ($position===0) {
                                    $file = $discuss->request->getControllerFile($key);
                                    if (file_exists($file) && !is_dir($file)) {
                                        $parameters['action'] = $key;
                                        $request = ltrim(substr($request, $position), '/');
                                        $matched++;
                                    }
                                }
                                else if ($key === 'global') {
                                    $actionname = $request;
                                    while (!empty($actionname)) {
                                        $file = $discuss->request->getControllerFile($actionname);
                                        if (file_exists($file) && !is_dir($file)) {
                                            $parameters['action'] = $key;
                                            $request = ltrim(substr($request, strpos($request, $actionname)), '/');
                                            $matched++;
                                            break;
                                        }
                                        $actionname = substr($actionname, 0, strrpos($actionname, '/'));
                                    }
                                }
                                break;
                            case 'variable':
                            case 'variable-required':
                                $position = strpos($request, '/');
                                if ($position===false) {
                                    $position = strlen($request);
                                }
                                $data = substr($request, 0, $position);
                                $parameters[$param['key']] = $data;
                                $request = ltrim(substr($request, $position), '/');
                                $matched++;
                                break;
                            case 'constant':
                                $position = strpos($request, $param['value']);
                                if ($position===0) {
                                    $request = ltrim(substr($request, $position), '/');
                                    $matched++;
                                }
                                break;
                            default:
                                $matched++;
                                break;
                        }
                        if ($previousmatch>=$matched) {
                            break;
                        }
                    }
                    if ($paramnumber === $matched) {
                        return parameters;
                        break;
                    }
                }
            }
        }
        return false;
    }
}
$parsed = false;
foreach ($manifest as $key => $value) {
    if ($key == 'global' || $key == 'preview' || $key == 'print') {
        continue;
    }
    if (array_key_exists('furl', $value) && count($value['furl'])>0) {
        $parsed = url_parser($key, $request, $value['furl']);
    }
}
if (!is_array($parsed)) {
    $parsed = url_parser('global', $request, $manifest['global']['furl']);
}
if (!is_array($parsed)) {
    return true;
}

foreach($parsed as $paramkey => $paramvalue) {
    $modx->request->parameters['GET'][$paramkey]=$paramvalue;
    if(empty($modx->request->parameters['POST'][$paramkey]))
        $modx->request->parameters['REQUEST'][$paramkey]=$paramvalue;
}

$modx->sendForward($modx->getOption('discuss.forums_resource_id'));

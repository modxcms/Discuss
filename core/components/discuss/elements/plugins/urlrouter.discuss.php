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
if ($modx->event->name != 'OnHandleRequest' || $modx->context->key == 'mgr') {
    return;
}
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return;

$discuss->loadRequest();
$discuss->url = $modx->makeUrl($modx->getOption('discuss.forums_resource_id'));

$request = trim($modx->request->getResourceIdentifier('alias'), '/');
// Checking to stop everything if we are sure that this is not our path or it doesn't have any parameters to parse (exact match of alias to the base resource alias)
if ((strpos($request, $discuss->url) !== 0 && $discuss->url != '/') ||strlen($request) === strlen($discuss->url) || array_key_exists($request, $modx->aliasMap)) {
    return;
}

$containersuffix = $modx->getOption('container_suffix', null, '/');

$chunk = substr($request, 0, strrpos($request, $containersuffix));
while (!empty($chunk)) {
    if (array_key_exists($request, $modx->aliasMap)) {
        return;
    }
    $chunk = substr($chunk, 0, strrpos($chunk, $containersuffix));
}
// Now lets check that we have a valid manifest
$manifest = $discuss->request->getManifest();
if (!is_array($manifest)) {
    return;
}

// Now we sort the manifest array just to make sure that the actions of maximum length are checked first
if (!function_exists('sorter')) {
    function sorter($a, $b)
    {
        return strlen($b) - strlen($a);
    }
}
uksort($manifest, "sorter");

// Here I am sure that the URL is the one we need. Now lets search it for the actions.
$request = $discuss->url != '/' ? ltrim(substr($request, strlen($discuss->url)), '/') : ltrim($request);
if (!function_exists('url_parser')) {
    function url_parser($action, $requested, $furls, &$discuss)
    {
        foreach($furls as $furl) {
            if (array_key_exists('data', $furl)) {
                $paramnumber = count($furl['data']);
                if ($paramnumber > 0) {
                    $parameters = array();
                    $matched = 0;
                    $request = trim($requested, '/');
                    foreach ($furl['data'] as $param) {
                        $previousmatch = $matched;
                        switch ($param['type']) {
                            case 'action':
                                $position = strpos($request, $action);
                                if ($position===0) {
                                    $file = $discuss->request->getControllerFile($action);
                                    if (file_exists($file["file"]) && !is_dir($file["file"])) {
                                        if (!empty($action)) {
                                            $parameters['action'] = $action;
                                        }
                                        $request = trim(substr($request, strlen($action)), '/');
                                        $matched++;
                                    }
                                }
                                else if ($action === 'global') {
                                    $actionname = $request;
                                    while (!empty($actionname)) {
                                        $file = $discuss->request->getControllerFile($actionname);
                                        if (file_exists($file["file"]) && !is_dir($file["file"])) {
                                            if (!empty($actionname)) {
                                                $parameters['action'] = $actionname;
                                            }
                                            $request = trim(substr($request, strlen($actionname)), '/');
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
                                if (!empty($data)) {
                                    $parameters[$param['key']] = $data;
                                }
                                $request = trim(substr($request, $position), '/');
                                $matched++;
                                break;
                            case 'constant':
                                $position = strpos($request, $param['value']);
                                if ($position===0) {
                                    $request = trim(substr($request, strlen($param['value'])), '/');
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
                        if (!array_key_exists('action', $parameters)) {
                            $parameters['action'] = $action;
                        }
                        return $parameters;
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
        $parsed = url_parser($key, $request, $value['furl'], $discuss);
        if (is_array($parsed)) {
            break;
        }
    }
    elseif (strpos($request, $key)===0) {
        $parsed = url_parser($key, $request, $manifest['global']['furl'], $discuss);
        if (is_array($parsed)) {
            break;
        }
    }
}
if (!is_array($parsed)) {
    $parsed = url_parser('global', $request, $manifest['global']['furl'], $discuss);
}
if (!is_array($parsed)) {
    return;
}

foreach($parsed as $paramkey => $paramvalue) {
    $modx->request->parameters['GET'][$paramkey]=$paramvalue;
    if(empty($modx->request->parameters['POST'][$paramkey])) {
        $modx->request->parameters['REQUEST'][$paramkey]=$paramvalue;
    }
}
$modx->sendForward($modx->getOption('discuss.forums_resource_id'));
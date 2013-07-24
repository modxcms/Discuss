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
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* override with your own defines here (see build.config.sample.php) */
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
$c = $modx->newQuery('disBoard');
$c->select(array('disBoard.id', "map" => "GROUP_CONCAT(Descendants.ancestor ORDER BY Descendants.ancestor DESC SEPARATOR '.')"));
$c->innerJoin('disBoardClosure', 'Descendants', 'Descendants.descendant = disBoard.id');
$c->where(array(
    'Descendants.ancestor != disBoard.id'
));
$c->groupby('disBoard.id');

$results = $modx->getCollection('disBoard', $c);
foreach ($results as $res) {
    $modx->updateCollection('disBoard', array('map' => $res->map), array('id' => $res->id));
    $maps[$res->id] = array_merge(array($res->id), explode(".", $res->map));
}

foreach ($maps as $map) {

    for ($i = 1; $i < count($map); $i++) {
        if(count($map) == 0) continue;
        $modx->updateCollection('disBoardClosure', array('depth' => $i), array('ancestor' => $map[$i], 'descendant' => $map[0]));
    }
}
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

/* load Discuss */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return '';

/* setup mem limits */
ini_set('memory_limit','1024M');
set_time_limit(0);
@ob_end_clean();
echo '<pre>';

/* load and run importer */
if ($discuss->loadImporter('disSmfImport')) {

    /* fix num_topics */
    $sql = 'SELECT
      disBoard.id,
      disBoard.name,
      disBoard.num_topics,
      (SELECT COUNT(id) FROM '.$modx->getTableName('disThread').' AS `Threads`
    WHERE `Threads`.`board` = `disBoard`.`id`) AS `real_count`
    FROM '.$modx->getTableName('disBoard').' `disBoard`
    ORDER BY `disBoard`.`map` ASC';
    $stmt = $modx->query($sql);
    if ($stmt) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!empty($row['real_count']) && $row['real_count'] != $row['num_topics']) {
                $discuss->import->log('Setting "'.$row['name'].'" to '.$row['real_count'].' from '.$row['num_topics']);
                $modx->exec('UPDATE '.$modx->getTableName('disBoard').'
                    SET `num_topics` = '.$row['real_count'].'
                    WHERE `id` = '.$row['id']);
            }
        }
        $stmt->closeCursor();
    }

    /* fix num_replies */
    /* fix total_posts */

} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Failed to load Import class.');
}

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nExecution time: {$totalTime}\n");
@session_write_close();
die();
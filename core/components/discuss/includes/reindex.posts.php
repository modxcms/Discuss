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

$forumsResourceUrl = 'forums/';

/* override with your own defines here (see build.config.sample.php) */
include 'config.core.php';
include MODX_CORE_PATH . 'model/modx/modx.class.php';
//$modx = modX::getInstance('foo');
$modx = new modX;

if (version_compare($modx->version['full_version'], '2.2.1-pl', '>=')) {
    $modx->initialize('foo', array(
        'transient_context' => true,
        'foo_results' => array(
            'config' => array(
                'session_enabled' => false,
                'log_target' => XPDO_CLI_MODE ? 'ECHO' : 'HTML',
                'log_level' => xPDO::LOG_LEVEL_INFO,
                'debug' => -1,
            ),
            'policies' => array(),
        )
    ));
} else {
    $modx->initialize('foo');
    $modx->setLogLevel(xPDO::LOG_LEVEL_INFO);
    $modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
    $modx->setDebug(-1);
}

/* load Discuss */
$discuss = $modx->getService('discuss','Discuss',$modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/discuss/');
if (!($discuss instanceof Discuss)) return '';

/* setup mem limits */
ini_set('memory_limit','1024M');
set_time_limit(0);
@ob_end_clean();
echo '<pre>';

$discuss->loadRequest();
$discuss->url = $forumsResourceUrl;
if (!$discuss->loadSearch()) {
    die('No search class!');
}

/* grab all posts and reindex */
$c = $modx->newQuery('disPost');
$c->innerJoin('disBoard','Board');
$c->innerJoin('disThread','Thread');
$c->innerJoin('disUser','Author');
$c->where(array(
    //'Thread.private' => 0,
    'Board.status:!=' => 0,
    'Thread.answered:=' => 1,
));
$c->select($modx->getSelectColumns('disPost','disPost'));
$c->select(array(
    'Board.name AS board_name',
    'Author.username AS username',
    'Thread.replies AS replies',
    'Thread.users AS users',
    'Thread.private AS private',
));
$c->sortby('id','ASC');
$c->limit(100, 0);

$count = 0;
$posts = $modx->getIterator('disPost', $c);
/** @var disPost $post */
foreach ($posts as $post) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Indexing: ' . $count++ . ' ' . $post->get('title') . ' (' . $post->get('id') . ")\n");
    $response = $post->index();
    if($response instanceof SolrUpdateResponse) {
        $modx->log(modX::LOG_LEVEL_INFO, ' (result): ' . $response->getRawResponse() . "\n");
    }
}
$discuss->search->commit();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\nExecution time: {$totalTime}\n");

@session_write_close();
die();

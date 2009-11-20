<?php
/**
 * Discuss build script
 *
 * @package discuss
 * @subpackage build
 */
$mtime = microtime();
$mtime = explode(' ', $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* override with your own defines here (see build.config.sample.php) */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'lexicon' => $root . '_build/lexicon/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'chunks' => $root.'core/components/discuss/chunks/',
    'docs' => $root.'core/components/discuss/docs/',
    'source_assets' => $root.'assets/components/discuss',
    'source_core' => $root.'core/components/discuss',
);
unset($root);

$modx= new modX();
$modx->initialize('mgr');
echo '<pre>'; /* used for nice formatting of log messages */
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage('discuss','0.1','alpha1');
$builder->registerNamespace('gallery',false,true,'{core_path}components/discuss/');

/* create category */
$category= $modx->newObject('modCategory');
$category->set('id',1);
$category->set('category','Discuss');

/* add snippets */
$snippets = include $source['data'].'transport.snippets.php';
$category->addMany($snippets);

/* add chunks */
$chunks = include $source['data'].'transport.chunks.php';
$category->addMany($chunks);

/* create category vehicle */
$attr = array(
    xPDOTransport::_UNIQUE_KEY => 'category',
    xPDOTransport::_PRESERVE_KEYS => false,
    xPDOTransport::_UPDATE_OBJECT => true,
    xPDOTransport::_RELATED_OBJECTS => true,
    xPDOTransport::_RELATED_OBJECT_ATTRIBUTES => array (
        'Children' => array(
            xPDOTransport::_PRESERVE_KEYS => false,
            xPDOTransport::_UPDATE_OBJECT => true,
            xPDOTransport::_UNIQUE_KEY => 'category',
            xPDOTransport::_RELATED_OBJECTS => true,
            xPDOTransport::_RELATED_OBJECT_ATTRIBUTES => array (
                'Snippets' => array(
                    xPDOTransport::_PRESERVE_KEYS => false,
                    xPDOTransport::_UPDATE_OBJECT => true,
                    xPDOTransport::_UNIQUE_KEY => 'name',
                ),
                'Chunks' => array(
                    xPDOTransport::_PRESERVE_KEYS => false,
                    xPDOTransport::_UPDATE_OBJECT => true,
                    xPDOTransport::_UNIQUE_KEY => 'name',
                ),
            ),
        ),
        'Snippets' => array(
            xPDOTransport::_PRESERVE_KEYS => false,
            xPDOTransport::_UPDATE_OBJECT => true,
            xPDOTransport::_UNIQUE_KEY => 'name',
        ),
        'Chunks' => array (
            xPDOTransport::_PRESERVE_KEYS => false,
            xPDOTransport::_UPDATE_OBJECT => true,
            xPDOTransport::_UNIQUE_KEY => 'name',
        ),
    ),
);
$vehicle = $builder->createVehicle($category,$attr);

$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('file',array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));
$builder->putVehicle($vehicle);

/* load lexicon strings */
$builder->buildLexicon($sources['lexicon']);

/* load menu */
$menu= null;
include_once $sources['data'].'transport.menu.php';

$vehicle= $builder->createVehicle($menu,array (
    xPDOTransport::_PRESERVE_KEYS => false,
    xPDOTransport::_UPDATE_OBJECT => true,
    xPDOTransport::_UNIQUE_KEY => 'text',
    xPDOTransport::_RELATED_OBJECTS => true,
    xPDOTransport::_RELATED_OBJECT_ATTRIBUTES => array (
        'Action' => array (
            xPDOTransport::_PRESERVE_KEYS => false,
            xPDOTransport::_UPDATE_OBJECT => true,
            xPDOTransport::_UNIQUE_KEY => array ('namespace','controller'),
        ),
    ),
));
$builder->putVehicle($vehicle);
unset($vehicle,$menu);

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
));

/* zip up package */
$builder->pack();

$mtime= microtime();
$mtime= explode(" ", $mtime);
$mtime= $mtime[1] + $mtime[0];
$tend= $mtime;
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO,"\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

exit ();
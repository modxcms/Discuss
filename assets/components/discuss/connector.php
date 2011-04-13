<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$disCorePath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/');
require_once $disCorePath.'model/discuss/discuss.class.php';
$modx->discuss = new Discuss($modx);

$modx->lexicon->load('discuss:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->discuss->config,$disCorePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
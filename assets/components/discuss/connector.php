<?php
/**
 * Discuss Connector
 *
 * @package discuss
 */
/* Load custom defines and modx object */
if (empty($_REQUEST['ctx'])) $_REQUEST['ctx'] = 'web';
define('DIS_CONNECTOR',true);

require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

require_once MODX_CORE_PATH.'components/discuss/model/discuss/discuss.class.php';
$modx->discuss = new Discuss($modx);
$modx->lexicon->load('discuss:web');

if (isset($_REQUEST['resource'])) {
    $modx->resource = $modx->getObject('modResource',$_REQUEST['resource']);
}

/* handle request */
$path = $modx->getOption('processorsPath',$modx->discuss->config,$modx->getOption('core_path').'components/discuss/processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));
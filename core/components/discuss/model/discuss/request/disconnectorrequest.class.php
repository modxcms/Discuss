<?php
require_once MODX_CORE_PATH . 'model/modx/modconnectorresponse.class.php';
/**
 * @package discuss
 */
class disConnectorRequest extends modConnectorResponse {

    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        parent::__construct($discuss->modx,$config);
    }

    function handle($action = '') {
        if (empty($action) && !empty($_REQUEST['action'])) $action = $_REQUEST['action'];
        if (!isset($this->modx->error)) $this->loadErrorHandler();

        $path = $this->discuss->config['processorsPath'].strtolower($action).'.php';
        $processorOutput = false;
        if (file_exists($path)) {
            $this->modx->lexicon->load('discuss:default');
            $modx =& $this->modx;
            $discuss =& $this->discuss;

            $processorOutput = include $path;
        } else {
            $processorOutput = $this->modx->error->failure('No action specified.');
        }
        if (is_array($processorOutput)) {
            $processorOutput = $this->toJSON($processorOutput);
        }
        return $processorOutput;
    }
}
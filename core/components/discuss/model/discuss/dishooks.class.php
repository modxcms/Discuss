<?php
/**
 * Hooks loading class
 *
 * @package discuss
 * @subpackage hooks
 */
class disHooks {
    /**
     * @var modX $modx A reference to the modX instance.
     * @access public
     */
    public $modx = null;
    /**
     * @var Discuss $discuss A reference to the Discuss instance.
     * @access public
     */
    public $discuss = null;

    /**
     * The hubHooks constructor.
     *
     * @param modX &$modx A reference to a modX instance.
     * @param array $config An array of configuration options. May also pass in
     * a reference to the Discuss instance as 'discuss' which will be assigned
     * to the disHooks instance.
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
        if (!empty($config) && $config['discuss']) $this->discuss =& $config['discuss'];
    }

    /**
     * Load a hook.
     *
     * @access public
     * @param string $name The name of the hook to load
     * @param array $scriptProperties A configuration array of variables to run
     * the hook with
     * @return mixed The return value of the hook
     */
    public function load($name = '',array $scriptProperties = array()) {
        if (empty($name)) return false;

        $success = false;
        $hookFile = $this->discuss->config['hooksPath'].$name.'.php';
        if (file_exists($hookFile)) {
            $discuss =& $this->discuss;
            $modx =& $this->modx;

            $success = include $hookFile;
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Hook not found: '.$hookFile);
        }
        return $success;
    }
}
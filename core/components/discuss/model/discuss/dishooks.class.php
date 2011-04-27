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
     * The disHooks constructor.
     *
     * @param Discuss &$discuss A reference to the Discuss class
     * @param array $config An array of configuration options. May also pass in
     * a reference to the Discuss instance as 'discuss' which will be assigned
     * to the disHooks instance.
     */
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
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
        $hookFile = $this->discuss->config['hooksPath'].strtolower($name).'.php';
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
<?php
/**
 * @package discuss
 */
/**
 * Abstract class to be extended for handling rendering front-end controllers
 *
 * @abstract
 * @package discuss
 */
abstract class DiscussController {
    /**
     * @var modX
     */
    public $modx;
    /**
     * @var Discuss
     */
    public $discuss;
    /**
     * @var array
     */
    public $options = array();
    /**
     * @var array
     */
    public $placeholders = array();
    /**
     * @var array
     */
    public $scriptProperties = array();
    /**
     * @var boolean
     */
    public $useWrapper = true;

    /**
     * @param Discuss $discuss
     * @param array $config
     */
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        $this->config = array_merge(array(),$config);
    }

    /**
     * Can be used to provide custom methods prior to processing
     * @return void
     */
    public function initialize() {}


    /**
     * Return the proper instance of the derived class. This can be used to override how the manager loads a controller
     * class; for example, when handling derivative classes with class_key settings.
     *
     * @static
     * @param Discuss $discuss A reference to the Discuss object.
     * @param string $className The name of the class that is being requested.
     * @param array $config A configuration array of options related to this controller's action object.
     * @return The class specified by $className
     */
    public static function getInstance(Discuss &$discuss,$className,$config = array()) {
        /** @var DiscussController $controller */
        $controller = new $className($discuss,$config);
        return $controller;
    }

    /**
     * Render the controller.
     *
     * @return string
     */
    public function render() {
        if ($this->modx->getOption('discuss.debug',$this->config,true)) {
            $this->modx->setLogTarget('ECHO');
            $this->startDebugTimer();
        }
        $this->initialize();
        $this->handleActions();
        $this->process();

        $sessionPlace = $this->getSessionPlace();
        if (!empty($sessionPlace)) {
            $this->discuss->setSessionPlace($sessionPlace);
        }

        $title = $this->getPageTitle();
        if (!empty($title)) {
            $this->modx->setPlaceholder('discuss.pagetitle',$title);
        }
        
        $this->_renderBreadcrumbs();
        $output = $this->_renderTemplate($this->config['tpl'],$this->placeholders);

        return $this->_output($output);
    }

    protected function _renderTemplate($tpl,array $properties = array()) {
        $o = '';
        if (file_exists($tpl)) {
            $o = file_get_contents($tpl);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
            $o = $chunk->process($properties);
        }
        return $o;
    }

    protected function _output($output = '') {
        if (!empty($_REQUEST['print'])) {
            $output = $this->_renderTemplate($this->discuss->config['pagesPath'].'print-wrapper.tpl',array(
                'content' => $output,
            ));
            return $output;
        }
        $emptyTpl = in_array($this->config['controller'],array('thread/preview','messages/preview','board.xml','thread/recent.xml'));
        if ($this->modx->getOption('discuss.debug',null,false)) {
            if (!$emptyTpl && $this->debugTimer !== false) {
                $output .= "<br />\nExecution time: ".$this->endDebugTimer()."\n";
            }
        }
        $output = trim($output);
        if (!$emptyTpl && $this->useWrapper) {
            $output = $this->_renderTemplate($this->discuss->config['pagesPath'].'wrapper.tpl',array(
                'content' => $output,
            ));
        }
        return $output;
    }

    /**
     * Used for handling POST actions prior to rendering
     * @return void
     */
    public function handleActions() {}
    
    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setPlaceholder($key,$value) {
        $this->placeholders[$key] = $value;
    }
    /**
     * @param array $array
     * @return void
     */
    public function setPlaceholders(array $array) {
        if (!is_array($array)) return;
        $this->placeholders = array_merge($this->placeholders,$array);
    }
    /**
     * @return array
     */
    public function getPlaceholders() {
        return $this->placeholders;
    }

    /**
     * Get a REQUEST property
     * @param string $key
     * @param mixed $default
     * @return null
     */
    public function getProperty($key,$default = null) {
        return isset($this->scriptProperties[$key]) ? $this->scriptProperties[$key] : $default;
    }

    /**
     * @abstract
     * @return void
     */
    abstract public function process();
    /**
     * @abstract
     * @return string
     */
    abstract public function getSessionPlace();
    /**
     * @abstract
     * @return string
     */
    abstract public function getPageTitle();

    /**
     * Override and return an array to automatically render breadcrumbs
     * 
     * @return array
     */
    public function getBreadcrumbs() { return array(); }

    /**
     * Render any breadcrumbs for the page
     * @return void
     */
    protected function _renderBreadcrumbs() {
        if (!empty($this->options['showBreadcrumbs'])) {
            $trail = $this->getBreadcrumbs();
            if (!empty($trail)) {
                if (is_array($trail)) {
                    $phs = array_merge($this->scriptProperties,array(
                        'items' => &$trail,
                    ));
                    $trail = $this->discuss->hooks->load('breadcrumbs',$phs);
                }
                $this->setPlaceholder('trail',$trail);
            }
        }
    }

    /**
     * Starts the debug timer.
     *
     * @access protected
     * @return int The start time.
     */
    protected function startDebugTimer() {
        $mtime = microtime();
        $mtime = explode(' ', $mtime);
        $mtime = $mtime[1] + $mtime[0];
        $tstart = $mtime;
        $this->debugTimer = $tstart;
        return $this->debugTimer;
    }

    /**
     * Return the current debug time.
     * @return string
     */
    public function getDebugTime() {
        $mtime= microtime();
        $mtime= explode(" ", $mtime);
        $mtime= $mtime[1] + $mtime[0];
        $tend= $mtime;
        $totalTime= ($tend - $this->debugTimer);
        $totalTime= sprintf("%2.4f s", $totalTime);
        return $totalTime;
    }
    /**
     * Ends the debug timer and returns the total number of seconds Discuss took
     * to run.
     *
     * @access protected
     * @return int The end total time to execute the script.
     */
    protected function endDebugTimer() {
        $totalTime = $this->getDebugTime();
        $this->debugTimer = false;
        return $totalTime;
    }
}

/**
 * Used for old-style deprecated controllers
 * @package discuss
 */
class DiscussDeprecatedController extends DiscussController {
    public function getPageTitle() { return ''; }
    public function getSessionPlace() { return ''; }
    public function process() {
        $discuss =& $this->discuss;
        $modx =& $this->modx;
        $scriptProperties = $this->scriptProperties;
        $options = $this->options;

        $placeholders = require $this->config['file'];
        if (is_array($placeholders)) {
            $this->setPlaceholders($placeholders);
        }
    }
}

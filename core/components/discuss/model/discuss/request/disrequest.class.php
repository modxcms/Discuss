<?php
/**
 * Encapsulates the interaction of MODx manager with an HTTP request.
 *
 * {@inheritdoc}
 *
 * @package discuss
 * @extends modRequest
 */
class DisRequest {
    public $controller = 'home';

    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        $this->config = array_merge(array(
            'actionVar' => 'action',
        ),$config);
        if ($this->modx->getOption('discuss.debug',$this->config,true)) {
            $this->modx->setLogTarget('ECHO');
            $this->startDebugTimer();
        }
    }

    /**
     * Get the controller to load
     * 
     * @return string The controller to load
     */
    public function getControllerValue() {
        $controller = !empty($_REQUEST[$this->config['actionVar']]) ? $_REQUEST[$this->config['actionVar']] : 'home';
        $controller = str_replace(array('../','./'),'',$controller);
        $controller = strip_tags(trim($controller,'/'));
        $c = $this->getControllerFile($controller);
        $this->controller = $c;
        return $c['controller'];
    }

    /**
     * Handle the current Discuss request
     * 
     * @return string The output HTML
     */
    public function handle() {
        $this->getControllerValue();
        $this->loadThemeOptions();
        $placeholders = $this->loadController();
        $output = $this->getPage($this->controller,$placeholders);

        return $this->output($output,$placeholders);
    }

    /**
     * Load the appropriate controller file.
     * 
     * @param $controller The controller to load
     * @return array An array of placeholders
     */
    public function loadController($controller = array()) {
        if (empty($controller)) $controller = $this->controller;

        if (file_exists($controller['file'])) {
            $discuss =& $this->discuss;
            $modx =& $this->modx;
            $scriptProperties = $_REQUEST;

            $placeholders = require $controller['file'];
        } else {
            @session_write_close();
            die('Could not find: '.$controller['file']);
        }
        return is_array($placeholders) ? $placeholders : array();
    }

    /**
     * Get the current template page
     */
    public function getPage(array $controller = array(),array $properties = array()) {
        if (empty($controller)) $controller = $this->controller;
        
        $f = $controller['tpl'];
        $o = '';
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
            $o = $chunk->process($properties);
        }
        return $o;
    }

    public function getControllerFile($controller = 'home') {
        $controllerFile = $this->discuss->config['controllersPath'].'web/'.$controller;
        if (!file_exists($controllerFile.'.php') && file_exists($controllerFile.'/index.php')) {
            $controllerFile .= '/index';
            $controller .= '/index';
        }
        return array(
            'file' => $controllerFile.'.php',
            'tpl' => $this->discuss->config['pagesPath'].strtolower($controller).'.tpl',
            'controller' => $controller,
        );
    }

    /**
     * Output the final forum output and wrap in the disWrapper chunk, if in
     * debug mode. The wrapper code will need to be in the Template if not in
     * debug mode.
     *
     * @access public
     * @param string $output The output to process
     * @return string The final wrapped output, or blank if not in debug.
     */
    public function output($output = '',array $properties = array()) {
        if ($this->modx->getOption('discuss.debug',null,false)) {
            $emptyTpl = in_array($this->controller['controller'],array('thread/preview'));
            if (!$emptyTpl && $this->debugTimer !== false) {
                $output .= "<br />\nExecution time: ".$this->endDebugTimer()."\n";
            }
            $c = $this->getControllerFile('wrapper');
            $placeholders = $this->loadController($c);
            $placeholders['content'] = $output;
            return $emptyTpl ? $output : $this->getPage($c,$placeholders);
        }

        $this->modx->toPlaceholders($properties);
        return $output;
    }

	/**
     * Load current theme options for Front end
     *
     * @access public
     * @return void
     */
	public function loadThemeOptions() {
        $additional = $this->controller['controller'];
        
        $f = $this->discuss->config['pagesPath'].'manifest.php';
        if (file_exists($f)) {
            $manifest = require $f;

			if (is_array($manifest) && array_key_exists('global', $manifest)){
				$global = $manifest['global'];

				/* Load global forum CSS */
				if(array_key_exists('css', $global)){
					$css = $global['css'];
					foreach($css['header'] as $val){
						$this->modx->regClientCSS($this->discuss->config['cssUrl'].$val);
					}
				}

				/* Load global forum JS */
				if(array_key_exists('js', $global)){
					$js = $global['js'];
					foreach($js['header'] as $val){
						$this->modx->regClientStartupScript($this->discuss->config['jsUrl'].$val);
					}
					if(isset($js['inline'])){
						$this->modx->regClientStartupHTMLBlock('<script type="text/javascript">// <![CDATA['."\n".$js['inline']."\n".'// ]]></script>');
					}
				}
			}

			if (isset($additional) && is_array($manifest) && array_key_exists($additional, $manifest)){
				$specific = $manifest[$additional];

				/* Load specific forum CSS */
				if(array_key_exists('css', $specific)){
					$css = $specific['css'];
					foreach($css['header'] as $val){
						$this->modx->regClientCSS($this->discuss->config['cssUrl'].$val);
					}
				}

				/* Load specific forum JS */
				if(array_key_exists('js', $specific)){
					$js = $specific['js'];
					foreach($js['header'] as $val){
						$this->modx->regClientStartupScript($this->discuss->config['jsUrl'].$val);
					}
					if(isset($js['inline'])){
						$this->modx->regClientHTMLBlock('<script type="text/javascript">'.$js['inline'].'</script>');
					}
				}
			}
        } else {
            $this->modx->regClientCSS($this->discuss->config['cssUrl'].'index.css');
            $this->modx->regClientStartupScript($this->discuss->config['jsUrl'].'web/discuss.js');
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
     * Ends the debug timer and returns the total number of seconds Discuss took
     * to run.
     *
     * @access protected
     * @return int The end total time to execute the script.
     */
    protected function endDebugTimer() {
        $mtime= microtime();
        $mtime= explode(" ", $mtime);
        $mtime= $mtime[1] + $mtime[0];
        $tend= $mtime;
        $totalTime= ($tend - $this->debugTimer);
        $totalTime= sprintf("%2.4f s", $totalTime);
        $this->debugTimer = false;
        return $totalTime;
    }
}
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
/**
 * Handles all basic Discuss request handling within the forums.
 *
 * @package discuss
 * @subpackage request
 */
class DisRequest {
    /**
     * The default controller for the request
     * @var string $controller
     */
    public $controller = 'home';
    /**
     * Any page-specific options for the loaded controller
     * @var array $pageOptions
     */
    public $pageOptions = array();

    /**
     * @param Discuss $discuss A reference to the Discuss instance
     * @param array $config An array of configuration properties
     */
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
        $colon = strpos($controller,';');
        if ($colon !== false) {
            $controller = substr($controller,0,$colon);
        }
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
     * @param array $controller The controller to load
     * @return array An array of placeholders
     */
    public function loadController($controller = array()) {
        if (empty($controller)) $controller = $this->controller;

        if (file_exists($controller['file'])) {
            $discuss =& $this->discuss;
            $modx =& $this->modx;
            $scriptProperties = array_merge($_REQUEST,$_GET,$_POST);
            $options = $this->pageOptions;

            $placeholders = require $controller['file'];
        } else {
            $this->discuss->sendErrorPage();
        }
        return is_array($placeholders) ? $placeholders : array();
    }

    /**
     * Get the current template page
     *
     * @param array $controller
     * @param array $properties
     * @return string
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

    /**
     * Return the file path to the specified controller
     * @param string $controller
     * @return array An array of file location, template location, and controller name information
     */
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
     * @param array $properties
     * @return string The final wrapped output, or blank if not in debug.
     */
    public function output($output = '',array $properties = array()) {
        if (!empty($_REQUEST['print'])) {
            $c = $this->getControllerFile('print-wrapper');
            return $this->getPage($c,array('content' => $output));
        }
        $emptyTpl = in_array($this->controller['controller'],array('thread/preview','messages/preview','board.xml'));
        if ($this->modx->getOption('discuss.debug',null,false)) {
            if (!$emptyTpl && $this->debugTimer !== false) {
                $output .= "<br />\nExecution time: ".$this->endDebugTimer()."\n";
            }
        }
        $c = $this->getControllerFile('wrapper');
        $placeholders = $this->loadController($c);
        $placeholders['content'] = $output;
        return $emptyTpl ? $output : $this->getPage($c,$placeholders);
    }

	/**
     * Load current theme options for Front end
     *
     * @access public
     * @return void
     */
	public function loadThemeOptions() {
        $additional = $this->controller['controller'];
        
        $f = $this->discuss->config['themePath'].'manifest.php';
        if (file_exists($f)) {
            $manifest = require $f;

            if (is_array($manifest) && array_key_exists('print', $manifest) && !empty($_REQUEST['print'])) {
                $print = $manifest['print'];

				/* Load global print CSS */
				if (array_key_exists('css', $print)){
					$css = $print['css'];
					foreach($css['header'] as $val){
						$this->modx->regClientCSS($this->discuss->config['cssUrl'].$val);
					}
				}

				/* load global print page options */
				if (array_key_exists('options', $print)){
					$this->pageOptions = array_merge($this->pageOptions,$print['options']);
                }

            } else if (is_array($manifest) && array_key_exists('global', $manifest)){
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

				/* load global page options */
				if (array_key_exists('options', $global)){
					$this->pageOptions = array_merge($this->pageOptions,$global['options']);
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

				/* load page-specific options */
				if (array_key_exists('options', $specific)){
					$this->pageOptions = array_merge($this->pageOptions,$specific['options']);
                }
			}
        } else {
            $this->modx->regClientCSS($this->discuss->config['cssUrl'].'index.css');
            $this->modx->regClientStartupScript($this->discuss->config['jsUrl'].'web/discuss.js');
        }
	}

    /**
     * Makes a proper URL for the Discuss system
     *
     * @param string $action
     * @param array $params
     * @return string
     */
    public function makeUrl($action = '',array $params = array()) {
        if (is_array($params)) {
            $params = http_build_query($params);
            if (!empty($params)) $params = '?'.$params;
        }
        $url = $this->discuss->url.$action.$params;
        if ($this->modx->getOption('discuss.absolute_urls',null,true)) {
            $url = $this->modx->getOption('site_url',null,MODX_SITE_URL).ltrim($url,'/');
        }
        return $url;
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
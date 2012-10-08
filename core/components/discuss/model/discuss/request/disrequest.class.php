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
        return $this->render();
    }

    /**
     * Render the request using the loaded controller
     * @return string
     */
    public function render() {
        $controller = $this->controller;

        if (!$this->modx->loadClass('DiscussController',$this->discuss->config['modelPath'].'discuss/',true,true)) {
            return '';
        }

        if (empty($controller['isClass'])) {
            $className = 'DiscussDeprecatedController';
        } else {
            $className = $this->getControllerClassName();
        }

        $output = '';
        if (file_exists($controller['file'])) {
            if (!empty($controller['isClass'])) {
                require_once $controller['file'];
            }
            if (!class_exists($className)) {
            	$this->modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not find class: '.$className);
            	$this->discuss->sendErrorPage();
            }
            try {
	            $c = new $className($this->discuss,$this->controller);
	    	} catch (Exception $e) { $this->discuss->sendErrorPage(); }
            
            $this->discuss->controller = call_user_func_array(array($c,'getInstance'),array($this->discuss,$className,$this->controller));

            $this->discuss->controller->scriptProperties = array_merge($_REQUEST,$_GET,$_POST);
            $this->discuss->controller->setOptions($this->pageOptions);
            $output = $this->discuss->controller->render();
            
        } else {
            $this->discuss->sendErrorPage();
        }
        return $output;
    }

    /**
     * Get the processed name of the controller to load
     * @return string
     */
    public function getControllerClassName() {
        $className = 'Discuss'.ucfirst(strtolower($this->controller['controller'])).'Controller';
        $className = explode('/',$className);
        $o = array();
        foreach ($className as $k) {
            if (strpos($k,'_')) {
                $substr = '';
                $e = explode('_',$k);
                foreach ($e as $ex) {
                    $substr[] = ucfirst($ex);
                }
                $k = implode('',$substr);
            }
            $o[] = ucfirst(str_replace(array('.','_','-'),'',$k));
        }
        return implode('',$o);
    }
    /**
     * Return the file path to the specified controller
     * @param string $controller
     * @return array An array of file location, template location, and controller name information
     */
    public function getControllerFile($controller = 'home') {
        $controllerFile = $this->discuss->config['controllersPath'].'web/'.$controller;
        $controllerArray = array(
            'isClass' => false,
            'tpl' => $this->discuss->config['pagesPath'].strtolower($controller).'.tpl',
            'controller' => $controller,
        );

        if (file_exists($controllerFile.'.class.php')) {
            $controllerArray['isClass'] = true;
            $controllerArray['file'] = $controllerFile.'.class.php';
        } else if (file_exists($controllerFile.'/index.class.php')) {
            $controllerArray['isClass'] = true;
            $controllerArray['file'] = $controllerFile.'/index.class.php';
            $controllerArray['tpl'] = $this->discuss->config['pagesPath'].strtolower($controller).'/index.tpl';
        } else if (!file_exists($controllerFile.'.php') && file_exists($controllerFile.'/index.php')) {
            $controllerArray['file'] = $controllerFile.'/index.php';
            $controllerArray['controller'] .= '/index';
            $controllerArray['tpl'] = $this->discuss->config['pagesPath'].strtolower($controller).'/index.tpl';
        } else {
            $controllerArray['file'] = $controllerFile.'.php';
        }
        return $controllerArray;
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
            $registerJs = array('header' => array(), 'footer' => array());

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
                    if (isset($js['header']) && !empty($js['header'])) {
                        foreach($js['header'] as $val){
                            $registerJs['header'][] = $this->discuss->config['jsUrl'].$val;
                        }
                    }
                    if (isset($js['footer']) && !empty($js['footer'])) {
                        foreach($js['footer'] as $val){
                            $registerJs['footer'][] = $this->discuss->config['jsUrl'].$val;
                        }
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
                    if (isset($js['header'])  && !empty($js['header'])) {
                        foreach($js['header'] as $val){
                            $registerJs['header'][] = $this->discuss->config['jsUrl'].$val;
                        }
                    }
                    if (isset($js['footer'])  && !empty($js['footer'])) {
                        foreach($js['footer'] as $val){
                            $registerJs['footer'][] = $this->discuss->config['jsUrl'].$val;
                        }
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

            $registerToScript = (isset($manifest['global']['options']['registerJsToScriptTags'])) ? $manifest['global']['options']['registerJsToScriptTags'] : true;
            if ($registerToScript) {
                foreach ($registerJs['header'] as $script) {
                    $this->modx->regClientStartupScript($script);
                }
                foreach ($registerJs['footer'] as $script) {
                    $this->modx->regClientScript($script);
                }
            } else {
                $this->modx->setPlaceholders(array(
                    'discuss.js.header' => (!empty($registerJs['header'])) ? $this->modx->toJSON($registerJs['header']) : '',
                    'discuss.js.footer' => (!empty($registerJs['footer'])) ? $this->modx->toJSON($registerJs['footer']) : '',
                ));
            }
        } else {
            $this->modx->regClientCSS($this->discuss->config['cssUrl'].'index.css');
            $this->modx->regClientStartupScript($this->discuss->config['jsUrl'].'web/discuss.js');
        }
	}
    
    private function urlManifestParse($action, $params, $manifest) {
        $bestmatch = null;
        $result = null;
        if ($action === '' && empty($params)) {
            return $this->discuss->url; // Fast check for makeUrl without parameters
        }
        if (count($manifest[$action]['furl'])>0) {
            foreach ($manifest[$action]['furl'] as $value) {
                if (count($value['condition'])>0) {
                    $conditionscount = 0;
                    $conditionsmatched = 0;
                    $conditionsparams = array();
                    foreach ($value['condition'] as $paramname => $paramvalue) {
                        $conditionscount++;
                        if (!empty($params[$paramname]) && $params[$paramname] === $paramvalue) {
                            $conditionsparams[] = $paramname;
                            $conditionsmatched++;
                        }
                    }
                    if ($conditionsmatched === $conditionscount) {
                        $haveneededparams = true;
                        foreach ($value['data'] as $data) {
                            if (($data['type'] === 'variable-required' || $data['type'] === 'parameter-required') && empty($params[$data['key']])) {
                                $haveneededparams = false;
                                break;
                            }
                        }
                        if (!$haveneededparams) {
                            continue; // The match isn't valid because the match requires having more required parameters.
                        }
                        foreach ($conditionsparams as $paramname) {
                            unset($params[$paramname]);
                        }
                        $bestmatch = $value;
                        break; // If we found the match from checking conditionals assume it the best
                    }
                }
                else {
                    $haveneededparams = true;
                    foreach ($value['data'] as $data) {
                        if (($data['type'] === 'variable-required' || $data['type'] === 'parameter-required') && empty($params[$data['key']])) {
                            $haveneededparams = false;
                            break;
                        }
                    }
                    if (!$haveneededparams) {
                        continue; // The match isn't valid because the match requires having more required parameters.
                    }
                    $bestmatch = $value; // If we found a match without conditionals consider it a "weak" match (and try to search other matches, for example with conditionals)
                }
            }
        }
        if ($bestmatch === null && $action != 'global') {
            $result = urlManifestParse('global', $params, $manifest); // check in global space if not found in action space
        }
        if ($bestmatch === null && $result === null) {
            return $this->makeUrl($action, $params, true); // Fallback to nonFURL generation in case if FURL generation failed
        }
        if ($bestmatch === null) {
            return $result;
        }
        $path = '';
        $request = array();
        foreach ($bestmatch['data'] as $data) {
            switch ($data['type']) {
                case 'action':
                    $path.= $action . '/';
                    break;
                case 'variable-required':
                    if (empty($params[$data['key']])) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not find required parameter when creating URL: '.$data['key']);
                        return $this->makeUrl($action, $params, true); // Fallback to nonFURL generation in case if FURL generation failed
                    } // No break here is intentional. The assignement itself is like the non-required variable.
                case 'variable':
                    if (!empty($params[$data['key']])) {
                        $path.= $params[$data['key']] . '/';
                        unset($params[$data['key']]);
                    }
                    break;
                case 'constant':
                    $path.= $data['value'] . '/';
                    break;
                case 'parameter-required':
                    if  (empty($params[$data['key']])) {
                        $this->modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not find required parameter when creating URL: '.$data['key']);
                        return $this->makeUrl($action, $params, true); // Fallback to nonFURL generation in case if FURL generation failed
                    } // No break here is intentional. The assignement itself is like the non-required parameter.
                case 'parameter':
                    if  (!empty($params[$data['key']])) {
                        $request[$data['key']] = $params[$data['key']];
                        unset($params[$data['key']]);
                    }
                    break;
                case 'parameter-constant':
                    $param = $data['value'];
                    if (!is_array($data['value'])) {
                        $paramexpl = explode("=", $data, 2);
                        if (count($paramexpl)>1) {
                            $param['key'] = $paramexpl[0];
                            $param['value'] = $paramexpl[1];
                        }
                        else {
                            $param['key'] = $data['value'];
                            $param['value'] = $data['value'];
                        }
                    }
                    $request[$param['key']] = $param['value'];
                    break;
                case 'allparameters':
                    $request = array_merge($params, $request);
                    $params = array(); // Because we passed all parameters to the $request array, $params is now void
                    break;
            }
        }
        trim($path, '/');
        $urlparts = explode('?', $this->discuss->url, 2);
        if (count($urlparts)>1) {
            $urlrequest = array();
            parse_str($urlparts[1], $urlrequest);
            $request = array_merge($urlrequest, $request);
        }
        $result = rtrim($urlparts[0], '/') . '/' . $path;
        if (!empty($request))
            $result .= '?' . http_build_query($request);
        return $result;
    }

    /**
     * Makes a proper URL for the Discuss system
     *
     * @param string $action
     * @param array $params
     * @return string
     */
    public function makeUrl($action = '',array $params = array(), $forcenofurls = false) {
        if (is_string($action) && !empty($action)) {
            $controller = $this->getControllerFile($action);
            if (!file_exists($controller["file"]) || is_dir($controller["file"])) {
                $action = ''; // Reset the action if we don't have the related controller
            }
        }
        else {
            $action = '';
        }
        $url = '';
        $nofurls = ($forcenofurls || $this->modx->request->getResourceMethod() != 'alias');
        if ($nofurls) {
            $url = $this->discuss->url;
            if(!empty($action))
                $params['action'] = $action;
            $urlparts = explode('?', $url, 2);
            if (count($urlparts)>1) {
                $urlrequest = array();
                parse_str($urlparts[1], $urlrequest);
                $params = array_merge($urlrequest, $params);
            }
            $url = rtrim($urlparts[0], '/');
            if (!empty($params))
                $url .= '?' . http_build_query($params);
        }
        else {
            
            /* BEGIN example manifest */
            $manifestexmpl = array(
                'global' => array(
                    'furl' => array(
                        array(
                            'condition' => array(
                                'type' => 'category'
                            ),
                            'data' => array(
                                array('type' => 'constant', 'value' => 'category'),
                                array('type' => 'variable-required', 'key' => 'category'),
                                array('type' => 'variable', 'key' => 'category_name'),
                                array('type' => 'allparameters')
                            )
                        ),
                        array(
                            'condition' => array(),
                            'data' => array(
                                array('type' => 'action'),
                                array('type' => 'allparameters')
                            )
                        )
                    )
                ),
                'thread' => array(
                    'furl' => array(
                        array(
                            'condition' => array(),
                            'data' => array(
                                array('type' => 'constant', 'value' => 'thread'),
                                array('type' => 'variable-required', 'key' => 'thread'),
                                array('type' => 'variable', 'key' => 'thread_name'),
                                array('type' => 'allparameters')
                            )
                        )
                    )
                ),
                'board' => array(
                    'furl' => array(
                        array(
                            'condition' => array(),
                            'data' => array(
                                array('type' => 'constant', 'value' => 'board'),
                                array('type' => 'variable-required', 'key' => 'board'),
                                array('type' => 'variable', 'key' => 'board_name'),
                                array('type' => 'allparameters')
                            )
                        )
                    )
                ),
                'user' => array(
                    'furl' => array(
                        array(
                            'condition' => array(
                                'type' => 'username'
                            ),
                            'data' => array(
                                array('type' => 'constant', 'value' => 'u'),
                                array('type' => 'variable-required', 'key' => 'user'),
                                array('type' => 'allparameters')
                            )
                        ),
                        array(
                            'condition' => array(
                                'type' => 'userid'
                            ),
                            'data' => array(
                                array('type' => 'constant', 'value' => 'user'),
                                array('type' => 'parameter', 'key' => 'user'),
                                array('type' => 'allparameters')
                            )
                        )
                    )
                )
            );
            /* END example manifest */
            
            /* Now parsing the manifest for FURLs rules */
            //$f = $this->discuss->config['themePath'].'manifest.php';
            //if (file_exists($f)) {
            //    $manifest = require $f;
                $manifest = $manifestexmpl;
                $url = $this->urlManifestParse($action, $params, $manifest);
            //}
            //else {
            //    return $this->makeUrl($action, $params, true); // Fallback to nonFURL generaton if we couldn't load manifest
            //}
            
        }
        if ($this->modx->getOption('discuss.absolute_urls',null,true)) {
            $url = $this->modx->getOption('site_url',null,MODX_SITE_URL).ltrim($url,'/');
        }
        return $url;
    }
    
}

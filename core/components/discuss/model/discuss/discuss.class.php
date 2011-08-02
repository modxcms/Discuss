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
 * The base class for Discuss.
 *
 * @package discuss
 */
class Discuss {
    /**
     * The MySQL datetime format
     * @const DATETIME_FORMATTED
     */
    const DATETIME_FORMATTED = '%Y-%m-%d %H:%M:%S';
    /**
     * The starting value of the execution time.
     * @var int/boolean $debugTimer
     */
    public $debugTimer = false;
    /**
     * The URL of the current Discuss home page
     * @var string $url
     */
    public $url = '';
    /**
     * The current active disUser in Discuss
     * @var disUser $user
     */
    public $user;
    /**
     * Whether or not the active user is logged in
     * @var bool $isLoggedIn
     * @deprecated Use user->isLoggedIn
     */
    public $isLoggedIn = false;
    /**
     * The Search class
     * @var disSearch $search
     */
    public $search;
    /**
     * The hooks class for loading application-specific hooks
     * @var disHooks $hooks
     */
    public $hooks;
    /**
     * @var disTreeParser $treeParser
     */
    public $treeParser;
    /**
     * @var disRequest $request
     */
    public $request;
    /**
     * @var DiscussController $controller
     */
    public $controller;
    /**
     * @var array $chunks
     */
    public $chunks = array();
    /**
     * @var disSession $session
     */
    public $session;
    /**
     * @var disImport $import
     */
    public $import;

    /**
     * @param modX $modx A reference to the modX instance
     * @param array $config An array of configuration properties
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('discuss.core_path',$config,$this->modx->getOption('core_path').'components/discuss/');
        $assetsPath = $this->modx->getOption('discuss.assets_path',$config,$this->modx->getOption('assets_path').'components/discuss/');
        $assetsUrl = $this->modx->getOption('discuss.assets_url',$config,$this->modx->getOption('assets_url').'components/discuss/');
		$themesUrl = $this->modx->getOption('discuss.themes_url',$config,$assetsUrl.'themes/');
        $theme = $this->modx->getOption('discuss.theme',$config,'default');

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
			'themesUrl' => $themesUrl,
            'theme' => $theme,
            'cssUrl' => $themesUrl.$theme.'/css/',
            'jsUrl' => $themesUrl.$theme.'/js/',
            'mgrCssUrl' => $assetsUrl.'mgr/css/',
            'mgrJsUrl' => $assetsUrl.'mgr/js/',
            'imagesUrl' => $themesUrl.$theme.'/images/',

            'connectorUrl' => $assetsUrl.'connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'themePath' => $corePath.'themes/'.$theme.'/',
            'chunksPath' => $corePath.'themes/'.$theme.'/chunks/',
            'pagesPath' => $corePath.'themes/'.$theme.'/pages/',
            'controllersPath' => $corePath.'controllers/',
            'snippetsPath' => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',
            'templatesPath' => $corePath.'templates/',
            'hooksPath' => $corePath.'hooks/',
            'useCss' => true,
            'loadJQuery' => true,
        ),$config);

        $this->modx->addPackage('discuss',$this->config['modelPath']);
        $this->ssoMode = $this->modx->getOption('discuss.sso_mode',$config,false);
        $this->dateFormat = $this->modx->getOption('discuss.date_format',$config,'%b %d, %Y, %I:%M %p');
    }

    /**
     * Initializes Discuss into different contexts.
     *
     * @TODO: Refactor to use derivative classes for different contexts, or
     * loader includes.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     * @return void|string
     */
    public function initialize($ctx = 'web') {
        $this->loadHooks();

        switch ($ctx) {
            case 'mgr':
            break;
            case 'connector':
                if (!$this->modx->loadClass('discuss.request.DisConnectorRequest',$this->config['modelPath'],true,true)) {
                    return 'Could not load connector request handler.';
                }
                $this->request = new DisConnectorRequest($this);
                return $this->request->handle();
            break;
            default:
                $this->modx->lexicon->load('discuss:web');

                $this->url = $this->modx->makeUrl($this->modx->resource->get('id'));
                $this->_initUser();
                $this->_initSession();
                $this->loadRequest();
            break;
        }
    }

    /**
     * Load the hooks class
     * 
     * @param string $class The Hooks class to load
     * @param string $path The path of the class to load
     * @return disHooks|null
     */
    public function loadHooks($class = 'discuss.disHooks',$path = '') {
        if (empty($path)) $path = $this->config['modelPath'];
        if ($className = $this->modx->loadClass($class,$path,true,true)) {
            $this->hooks = new $className($this);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not load '.$class.' from '.$path);
        }
        return $this->hooks;
    }

    /**
     * Load the request class
     * 
     * @param string $class The Request class to load
     * @param string $path The path of the request class.
     * @return DisRequest
     */
    public function loadRequest($class = 'discuss.request.DisRequest',$path = '') {
        if (empty($path)) $path = $this->config['modelPath'];
        if ($className = $this->modx->loadClass($class,$path,true,true)) {
            $this->request = new $className($this);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not load '.$class.' from '.$path);
        }
        return $this->request;
        
    }

    /**
     * Load the selected search class
     * @return disSearch
     */
    public function loadSearch() {
        $searchClass = $this->modx->getOption('discuss.search_class',null,'disSearch');
        $searchClassPath = $this->modx->getOption('discuss.search_class_path',null,$this->config['modelPath'].'discuss/search/');
        if (empty($searchClassPath)) $searchClassPath = $this->config['modelPath'].'discuss/search/';
        
        if ($className = $this->modx->loadClass($searchClass,$searchClassPath,true,true)) {
            $this->search = new $className($this);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not load '.$searchClass.' from '.$searchClassPath);
        }
        return $this->search;
    }

    /**
     * Return an import class
     * 
     * @param string $class
     * @param string $path
     * @return disImport
     */
    public function loadImporter($class = 'DisSmfImport',$path = '') {
        if (empty($path)) $path = $this->config['modelPath'];
        if ($className = $this->modx->loadClass('discuss.import.'.$class,$path,true,true)) {
            $this->import = new $className($this);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not load '.$class.' from '.$path);
        }
        return $this->import;

    }

    /**
     * Initializes the user, tracks their ip and activity, and loads their
     * profile. Also loads topbar information and links.
     *
     * @access private
     */
    private function _initUser() {
        /* if no user, set id to 0 */
        $isLoggedIn = $this->modx->user->hasSessionContext($this->modx->context->get('key'));
        if (!$isLoggedIn) {
            $this->user =& $this->modx->newObject('disUser');
            $this->user->set('id',0);
            $this->user->set('user',0);
            $this->user->set('username','(anonymous)');
            $this->user->isLoggedIn = false;
        } else {
            /* we are logged into MODX, check for user in Discuss */
            $this->user = $this->modx->getObject('disUser',array('user' => $this->modx->user->get('id')));
            if (empty($this->user)) {
                /* if no disUser exists but there is a modUser, import! */
                $profile = $this->modx->user->getOne('Profile');

                $this->user = $this->modx->newObject('disUser');
                $this->user->fromArray(array(
                    'user' => $this->modx->user->get('id'),
                    'username' => $this->modx->user->get('username'),
                    'password' => $this->modx->user->get('password'),
                    'salt' => $this->modx->user->get('salt'),
                    'synced' => true,
                    'syncedat' => date('Y-m-d H:I:S'),
                    'confirmed' => true,
                    'confirmedon' => date('Y-m-d H:I:S'),
                    'source' => 'internal',
                    'ip' => $this->getIp(),
                ));
                if ($profile) {
                    $this->user->fromArray($profile->toArray());
                    $name = $profile->get('fullname');
                    $name = explode(' ',$name);
                    $this->user->fromArray(array(
                        'name_first' => $name[0],
                        'name_last' => isset($name[1]) ? $name[1] : '',
                    ));
                }
                $this->user->save();
            }
            $this->user->init();
        }

        /* topbar profile links. @TODO: Move this somewhere else. */
        if ($this->user->isLoggedIn) {
            $authphs = array(
                'authLink' => '<a href="'.$this->url.'logout">Logout</a>',
            );
            $authphs = array_merge($this->user->toArray('user.'),$authphs);
            $authphs['user.avatar_url'] = $this->user->getAvatarUrl();
            $authphs['user.unread_messages'] = $this->user->countUnreadMessages();
        } else {
            $authphs = array(
                'authLink' => '<a href="'.$this->url.'login">Login</a>',
                'user.avatar_url' => '',
                'user.unread_messages' => '',
            );
        }
        $this->modx->toPlaceholders($authphs,'discuss');
    }

    /**
     * Initializes the user session and updates forum activity
     *
     * @access private
     * @return void
     */
    private function _initSession() {
        if (defined('DIS_CONNECTOR') && DIS_CONNECTOR) return;
        $sessionId = session_id();
        if ($this->user->isLoggedIn) {
            $session = $this->modx->getObject('disSession',array('user' => $this->user->get('id')));
            if (!empty($session)) {
                $this->modx->removeCollection('disSession',array(
                    'id:!=' => $session->get('id'),
                    'user' => $this->user->get('id'),
                ));
            }
        }
        if (empty($session)) {
            $session = $this->modx->getObject('disSession',$sessionId);
        }
        /* only log activity if first visit */
        if ($session == null) {
            $now = date('Y-m-d');
            /** @var disForumActivity $activity */
            $activity = $this->modx->getObject('disForumActivity',array(
                'day' => $now,
            ));
            if ($activity == null) {
                $activity = $this->modx->newObject('disForumActivity');
                $activity->set('day',$now);
            }
            /** @var disSession $session */
            $session = $this->modx->newObject('disSession');
            $session->set('id',$sessionId);
            $session->set('startedon',time());
            if ($this->modx->user->get('id') == 0) {
                /* if is a visitor, up the visitor count */
                $activity->set('visitors',($activity->get('visitors')+1));
            }
            $activity->set('hits',($activity->get('hits')+1));
            $activity->save();
        }
        $session->set('user',$this->user->get('id') < 1 ? 0 : $this->user->get('id'));
        $session->set('access',time());
        $session->set('data','');
        $session->save();

        $this->session =& $session;

        /* remove old sessions */
        $this->modx->query('DELETE FROM '.$this->modx->getTableName('disSession').' WHERE (access + ttl) < '.time());
    }

    /**
     * Sets the current "place" the user is browsing. Used for user tracking
     * purposes.
     *
     * @access public
     * @param string $place The place the user is currently at.
     */
    public function setSessionPlace($place) {
        $this->session->set('place',$place);
        $this->session->save();
    }

    /**
     * Loads the Tree Parser to handle naive tree parsing into row-based HTML
     *
     * @access public
     * @param string $class The name of the class to load.
     * @param string $path The path by which to load the class.
     * @return disTreeParser The instantiated disTreeParser object.
     */
    public function loadTreeParser($class = 'discuss.disTreeParser',$path = '') {
        if (empty($path)) $path = $this->config['modelPath'];

        if ($className = $this->modx->loadClass($class,$path,true,true)) {
            $this->treeParser= new $className($this);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Could not load '.$class.' from '.$path);
        }
        return $this->treeParser;
    }

    /**
     * Load a processor
     * 
     * @param string $name
     * @param array $scriptProperties
     * @return bool|mixed
     */
    public function loadProcessor($name,array $scriptProperties = array()) {
        if (!isset($this->modx->error)) $this->modx->request->loadErrorHandler();

        $path = $this->config['processorsPath'].$name.'.php';
        $processorOutput = false;
        if (file_exists($path)) {
            $modx =& $this->modx;
            $discuss =& $this;

            $processorOutput = include $path;
        } else {
            $processorOutput = $this->modx->error->failure('No action specified.');
        }
        return $processorOutput;
    }

    /**
     * Gets a Chunk and caches it; also falls back to file-based templates
     * for easier debugging.
     *
     * @access public
     * @param string $name The name of the Chunk
     * @param array $properties The properties for the Chunk
     * @return string The processed content of the Chunk
     */
    public function getChunk($name,array $properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            /*$chunk = $this->modx->getObject('modChunk',array('name' => $name),true);*/
            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
     * Returns a modChunk object from a template file.
     *
     * @access private
     * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
     * @param string $postFix
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name,$postFix = '.chunk.tpl') {
        $chunk = false;
        $f = $this->config['chunksPath'].strtolower($name).$postFix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            /** @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    /**
     * Builds action button HTML.
     *
     * @access public
     * @param array $btns
     * @param string $cls
     * @return string The HTML for the action buttons
     */
    public function buildActionButtons($btns,$cls) {
        $abs = array();
        foreach ($btns as $ar) {
            $abs[] = $this->getChunk('disActionButton',$ar);
        }
        return $this->getChunk('disActionButtons',array('buttons' => implode("\n",$abs)));
    }

    /**
     * Sends an email based on the specified information and templates.
     *
     * @access public
     * @param string $email The email to send to.
     * @param string $name The name of the user to send to.
     * @param string $subject The subject of the email.
     * @param array $properties A collection of properties.
     * @return array
     */
    public function sendEmail($email,$name,$subject,array $properties = array()) {
        if (empty($properties['tpl'])) return false;
        if (empty($properties['tplType'])) $properties['tplType'] = 'modChunk';

        $msg = $this->getChunk($properties['tpl'],$properties,$properties['tplType']);

        $this->modx->getService('mail', 'mail.modPHPMailer');
        $this->modx->mail->set(modMail::MAIL_BODY, $msg);
        $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('discuss.admin_email'));
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('discuss.admin_email'));
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $subject);
        $this->modx->mail->address('to', $email, $name);
        $this->modx->mail->address('reply-to', $this->modx->getOption('discuss.admin_email'));
        $this->modx->mail->setHTML(true);
        $sent = $this->modx->mail->send();
        $this->modx->mail->reset();

        return $sent;
    }

    /**
     * Processes results from the error handler
     *
     * @access public
     * @param array $result The result from the processor
     * @return boolean The success of the processor
     */
    public function processResult(array $result = array()) {
        if (!is_array($result) || !isset($result['success'])) return false;

        if ($result['success'] == false) {
            foreach ($result['errors'] as $error) {
                $this->modx->toPlaceholder($error['id'],$error['msg'],'error');
            }
        }
        if (!empty($_POST)) $this->modx->toPlaceholders($_POST,'post');
        return $result['success'];
    }

    /**
     * Process MODx event results
     * @param array $rs
     * @return string
     */
    public function getEventResult($rs) {
        $success = '';
        if (is_array($rs)) {
            foreach ($rs as $msg) {
                if (!empty($msg)) {
                    $success .= $msg."\n";
                }
            }
        } else {
            $success = $rs;
        }
        return $success;
    }

    /**
     * Process MODx event results as a list of objs
     * @param array $rs
     * @return string
     */
    public function getEventRenderResult($rs) {
        $objs = array();
        if (is_array($rs)) {
            foreach ($rs as $key => $value) {
                if (is_array($value)) {
                    $objs = array_merge($objs,$value);
                } elseif (!empty($value)) {
                    $objs[$key] = $value;
                }
            }
        } else {
            $objs = $rs;
        }
        return $objs;
    }

    /**
     * Invoke an event that is supposed to return an array of placeholders to merge with the current set
     * 
     * @param string $name
     * @param array $placeholders
     * @param array $otherProperties
     * @return array
     */
    public function invokeRenderEvent($name,array $placeholders = array(),array $otherProperties = array()) {
        $otherProperties['placeholders'] = $placeholders;
        $rs = $this->modx->invokeEvent($name,$otherProperties);
        $rs = $this->getEventRenderResult($rs);
        if (!empty($rs) && is_array($rs)) {
            $rs = array_merge($placeholders,$rs);
        } else {
            $rs = $placeholders;
        }
        return $rs;
    }

    /**
     * Return the current time.
     * 
     * @return string
     */
    public function now() {
        return strftime(Discuss::DATETIME_FORMATTED);
    }

    /**
     * Get the IP of the current user
     * 
     * @return string
     */
    public function getIp() {
        $ip = '';
        $ipAll = array(); // networks IP
        $ipSus = array(); // suspected IP

        $serverVariables = array(
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_X_COMING_FROM',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_COMING_FROM',
            'HTTP_CLIENT_IP',
            'HTTP_FROM',
            'HTTP_VIA',
            'REMOTE_ADDR',
        );

        foreach ($serverVariables as $serverVariable) {
            $value = '';
            if (isset($_SERVER[$serverVariable])) {
                $value = $_SERVER[$serverVariable];
            } elseif (getenv($serverVariable)) {
                $value = getenv($serverVariable);
            }

            if (!empty($value)) {
                $tmp = explode(',', $value);
                $ipSus[] = $tmp[0];
                $ipAll = array_merge($ipAll,$tmp);
            }
        }

        $ipSus = array_unique($ipSus);
        $ip = (sizeof($ipSus) > 0) ? $ipSus[0] : $ip;

        if ($ip == '::1') $ip = '127.0.0.1'; /* ipv6->4 conv for now */
        return $ip;
    }

    /**
     * Set the current pagetitle for the controller
     * 
     * @param string $title
     * @return void
     */
    public function setPageTitle($title) {
        $this->modx->setPlaceholder('discuss.pagetitle',$title);
    }

    /**
     * Format a date according to the default datetime
     *
     * @param string $datetime
     * @return string
     */
    public function formatDate($datetime) {
        $datetime = is_int($datetime) ? $datetime : strtotime($datetime);
        return !empty($datetime) && $datetime != '0000-00-00 00:00:00' ? strftime($this->dateFormat,$datetime) : '';
    }

    /**
     * Send user to specific unauthorized page
     * @return void
     */
    public function sendUnauthorizedPage() {
        $loginPage = $this->modx->getOption('discuss.login_resource_id',null,0);
        if (!empty($loginPage) && $this->ssoMode) {
            $url = $this->modx->makeUrl($loginPage,'','?discuss=1','full');
            $this->modx->sendRedirect($url);
        } else {
            $this->modx->sendUnauthorizedPage();
        }
    }

    /**
     * Send user to the appropriate error page
     * @return void
     */
    public function sendErrorPage() {
        $errorPage = $this->modx->getOption('discuss.error_page',null,0);
        if (!empty($errorPage)) {
            $url = $this->modx->makeUrl($errorPage,'','?discuss=1','full');
            $this->modx->sendRedirect($url);
        } else if ($this->modx->getOption('discuss.error_page_to_index',null,true)) {
            $this->modx->sendRedirect($this->url);
        } else {
            $this->modx->sendErrorPage();
        }
    }

    /**
     * Convert all MODX tags to html entities to prevent injection
     *
     * @param string $message
     * @return mixed
     */
    public function convertMODXTags($message) {
        return str_replace(array('[',']'),array('&#91;','&#93;'),$message);
    }

    /**
     * Strips all MODX tags and converts HTML tags
     * @param string $message
     * @return string
     */
    public function stripAllTags($message) {
        $message = preg_replace('@\[\[(.[^\[\[]*?)\]\]@si','',$message);
        $message = htmlentities($message,null,'UTF-8');
        return $message;
    }

    /**
     * A faster array_diff
     * 
     * @param array $data1
     * @param array $data2
     * @return array
     */
    public function arrayDiffFast(array $data1,array $data2) {
        $data1 = array_flip($data1);
        $data2 = array_flip($data2);

        foreach($data2 as $hash => $key) {
           if (isset($data1[$hash])) unset($data1[$hash]);
        }

        return array_flip($data1);
    }

    /**
     * Log forum activity (destructive/creative only, ie, delete_post, delete_thread, create_post, etc)
     * @param string $action The action key that happened
     * @param array $data An array of extra data to store with the activity
     * @param string $url An optional URL to store
     * @return boolean
     */
    public function logActivity($action,array $data = array(),$url = '') {
        if (empty($url)) {
            $url = (!empty($_SERVER['HTTPS'])) ? "https://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] : "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        }
        /** @var disLogActivity $activity */
        $activity = $this->modx->newObject('disLogActivity');
        $activity->set('createdon',$this->now());
        $activity->set('user',$this->user->get('id'));
        $activity->set('ip',$this->getIp());
        $activity->set('action',$action);
        $activity->set('data',$this->modx->toJSON($data));
        $activity->set('url',$url);
        return $activity->save();
    }
}
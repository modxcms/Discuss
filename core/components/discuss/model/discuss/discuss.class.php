<?php
/**
 * @package discuss
 */
/**
 * The base class for Discuss.
 *
 * @package discuss
 */
class Discuss {
    /**
     * @var int/boolean $debugTimer The starting value of the execution time.
     * @access public
     */
    public $debugTimer = false;

    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('discuss.core_path',$config,$this->modx->getOption('core_path').'components/discuss/');
		if ($this->modx->getOption('discuss.debug',null,false)) {
			$corePath = $this->modx->getOption('discuss.core_path');
		}
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
            'imagesUrl' => $themesUrl.$theme.'/images/',

            'connectorUrl' => $assetsUrl.'connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'chunksPath' => $corePath.'elements/chunks/'.$theme.'/',
            'controllersPath' => $corePath.'controllers/',
            'pagesPath' => $corePath.'elements/pages/'.$theme.'/',
            'snippetsPath' => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',
            'hooksPath' => $corePath.'hooks/',
            'useCss' => true,
            'loadJQuery' => true,
        ),$config);

        $this->modx->addPackage('discuss',$this->config['modelPath']);
    }

    /**
     * Initializes Discuss into different contexts.
     *
     * @TODO: Refactor to use derivative classes for different contexts, or
     * loader includes.
     *
     * @access public
     * @param string $ctx The context to load. Defaults to web.
     */
    public function initialize($ctx = 'web') {
        $this->modx->getService('hooks','discuss.disHooks',$this->config['modelPath'],array(
            'discuss' => &$this,
        ));

        switch ($ctx) {
            case 'mgr':
                if (!$this->modx->loadClass('discuss.request.DisControllerRequest',$this->config['modelPath'],true,true)) {
                    return 'Could not load controller request handler.';
                }
                $this->request = new DisControllerRequest($this);
                return $this->request->handleRequest();
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

                $this->_initUser();
                $this->_initSession();
                $this->loadRequest();
            break;
        }
    }

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
     * Initializes the user, tracks their ip and activity, and loads their
     * profile. Also loads topbar information and links.
     *
     * @access private
     */
    private function _initUser() {
        /* if no user, set id to 0 */
        $userId = $this->modx->user->get('id');
        if ($userId == null) {
            $this->modx->user->set('id',0);
        }
        /* assign user */
        $this->user =& $this->modx->user;
        $this->user->profile = $this->modx->getObject('disUserProfile',array('user' => $this->user->get('id')));
        if ($this->user->profile == null) {
            /* @TODO: auto-create profile if not exists as fallback */
        } else {
            $this->user->profile->set('last_active',strftime('%Y-%m-%d %H:%M:%S'));
            $this->user->profile->set('ip',$_SERVER['REMOTE_ADDR']);
            $this->user->profile->save();
        }

        /* topbar profile links. @TODO: Move this somewhere else. */
        if ($this->modx->user->isAuthenticated()) {
            $authphs = array(
                'user' => $userId,
                'username' => $this->user->get('username'),
                'loggedInAs' => 'logged in as <a href="[[~[[++discuss.user_resource]]]]?user=1">[[+discuss.username]]</a> - ',
                'homeLink' => '<a href="[[~[[++discuss.board_list_resource]]]]">Home</a>',
                'authLink' => '<a href="[[~[[++discuss.board_list_resource]]? &logout=`1`]]">Logout</a>',
                'profileLink' => '<a href="[[~[[++discuss.user_resource]]? &user=`'.$userId.'`]]">Profile</a>',
                'searchLink' => '<a href="[[~[[++discuss.search_resource]]]]">Search</a>',
                'unreadLink' => '<a href="[[~[[++discuss.unread_posts_resource]]]]">Unread Posts</a>',
            );
        } else {
            $authphs = array(
                'authLink' => '<a href="[[~[[++discuss.login_resource]]]]">Login</a>',
            );
        }
        $this->modx->toPlaceholders($authphs,'discuss');
    }

    /**
     * Initializes the user session and updates forum activity
     *
     * @access private
     */
    private function _initSession() {
        if (defined('DIS_CONNECTOR') && DIS_CONNECTOR) return false;
        $sessionId = session_id();
        $session = $this->modx->getObject('disSession',$sessionId);
        /* only log activity if first visit */
        if ($session == null) {
            $now = date('Y-m-d');
            $activity = $this->modx->getObject('disForumActivity',array(
                'day' => $now,
            ));
            if ($activity == null) {
                $activity = $this->modx->newObject('disForumActivity');
                $activity->set('day',$now);
            }

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
        $session->set('user',$this->user->get('id'));
        $session->set('access',time());
        $session->set('data','');
        $session->save();

        $this->session =& $session;

        /* remove old sessions */
        $ss = $this->modx->removeCollection('disSession',array(
            '(access + ttl) > NOW()'
        ));
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
     * @return modChunk/boolean Returns the modChunk object if found, otherwise
     * false.
     */
    private function _getTplChunk($name,$postFix = '.chunk.tpl') {
        $chunk = false;
        $f = $this->config['chunksPath'].strtolower($name).$postFix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    /**
     * Builds pagination
     *
     * @access public
     * @param int $count The total number of records
     * @param int $limit The # of records per page
     * @param int $start The current starting record
     * @param string $url The URL for the links to go to.
     * @return string
     */
    public function buildPagination($count,$limit,$start,$url) {
        $pageCount = $count / $limit;
        $curPage = $start / $limit;
        $pages = '';

        $params = $_GET;
        unset($params['q']);

        for ($i=0;$i<$pageCount;$i++) {
            $newStart = $i*$limit;
            $u = $url.'?'.http_build_query(array_merge($params,array(
                'start' => $newStart,
                'limit' => $limit,
            )));
            if ($i != $curPage) {
                $pages .= '<li class="dis-page-number"><a href="'.$u.'">'.($i+1).'</a></li>';
            } else {
                $pages .= '<li class="dis-page-number dis-page-current">'.($i+1).'</li>';
            }
        }
        if (empty($pages)) $pages = '<li class="dis-page-number dis-page-current">1</li>';
        return $pages;
    }

    /**
     * Builds action button HTML.
     *
     * TODO: chunk/tpl-ize this
     *
     * @access public
     * @return string The HTML for the action buttons
     */
    public function buildActionButtons($btns,$cls) {
        $abs = '<ul class="dis-action-btns right">';
        foreach ($btns as $ar) {
            $abs .= '<li><a href="'.$ar['url'].'">'.$ar['text'].'</a></li>';
        }
        $abs .= '</ul>';
        return $abs;
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
        $this->modx->mail->set(modMail::MAIL_FROM, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(modMail::MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $this->modx->mail->set(modMail::MAIL_SENDER, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(modMail::MAIL_SUBJECT, $subject);
        $this->modx->mail->address('to', $email, $name);
        $this->modx->mail->address('reply-to', $this->modx->getOption('emailsender'));
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
	
}
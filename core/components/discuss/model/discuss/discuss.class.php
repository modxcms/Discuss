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
        $assetsPath = $this->modx->getOption('discuss.assets_path',$config,$this->modx->getOption('assets_path').'components/discuss/');
        $assetsUrl = $this->modx->getOption('discuss.assets_url',$config,$this->modx->getOption('assets_url').'components/discuss/');

        $connectorId = $this->modx->getOption('discuss.connector_resource_id',$config,1);
        $connectorUrl = $this->modx->makeUrl($connectorId);

        $this->config = array_merge(array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl.'css/',
            'jsUrl' => $assetsUrl.'js/',
            'imagesUrl' => $assetsUrl.'images/',

            'connectorUrl' => $connectorUrl,

            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'chunksPath' => $corePath.'elements/chunks/',
            'pagesPath' => $corePath.'elements/pages/',
            'snippetsPath' => $corePath.'elements/snippets/',
            'processorsPath' => $corePath.'processors/',
            'hooksPath' => $corePath.'hooks/',
            'useCss' => true,
            'loadJQuery' => true,
        ),$config);

        $this->modx->addPackage('discuss',$this->config['modelPath'],'discuss_');
        if ($this->modx->getOption('discuss.debug',$this->config,true)) {
            $this->startDebugTimer();
        }
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

                if ($this->config['useCss']) {
                    $this->modx->regClientCSS($this->config['cssUrl'].'index.css');
                }
                if ($this->config['loadJQuery']) {
                    $this->modx->regClientStartupScript($this->config['jsUrl'].'web/jquery-1.3.2.min.js');
                }
                $this->modx->regClientStartupScript($this->config['jsUrl'].'web/discuss.js');
                $this->modx->regClientStartupScript('<script type="text/javascript">
    $(function() {
        DIS.config.connector = "'.$this->config['connectorUrl'].'";
        DIS.config.context = "'.$this->modx->context->get('key').'?ctx=mgr";
        DIS.config.pollingInterval = "30000";
    });</script>
                ');
                $this->_initUser();
                $this->_initSession();
            break;
        }
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
                'authLink' => '<a href="[[~[[++discuss.board_list_resource]]]]?logout=1">Logout</a>',
                'profileLink' => '<a href="[[~[[++discuss.user_resource]]]]?user='.$userId.'">Profile</a>',
                'searchLink' => '<a href="[[~[[++discuss.search_resource]]]]">Search</a>',
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
            $this->modx->log(MODX_LOG_LEVEL_ERROR,'Could not load '.$class.' from '.$path);
        }
        return $this->treeParser;
    }

    public function loadProcessor($name,$scriptProperties = array()) {
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
    public function getChunk($name,$properties = array()) {
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
    private function _getTplChunk($name) {
        $chunk = false;
        $f = $this->config['chunksPath'].strtolower($name).'.chunk.tpl';
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    /**
     * Used for development and debugging
     */
    public function getPage($name,$properties = array()) {
        $name = str_replace('.','/',$name);
        $f = $this->config['pagesPath'].strtolower($name).'.tpl';
        $o = '';
        if (file_exists($f)) {
            $o = file_get_contents($f);
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
            return $chunk->process($properties);
        }
        return false;
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
    public function output($page = '',$properties = array()) {
        if ($this->modx->getOption('discuss.debug',null,false)) {
            $output = $this->getChunk('disWrapper',array(
                'discuss.output' => $this->getPage($page,$properties),
            ));

            if ($this->debugTimer !== false) {
                $output .= "<br />\nExecution time: ".$this->endDebugTimer()."\n";
            }

            return $output;
        }

        $modx->toPlaceholders($properties);
        return '';
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
     * @todo chunk/tpl-ize this
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
    public function sendEmail($email,$name,$subject,$properties = array()) {
        if (empty($properties['tpl'])) return false;
        if (empty($properties['tplType'])) $properties['tplType'] = 'modChunk';

        $msg = $this->getChunk($properties['tpl'],$properties,$properties['tplType']);

        $this->modx->getService('mail', 'mail.modPHPMailer');
        $this->modx->mail->set(MODX_MAIL_BODY, $msg);
        $this->modx->mail->set(MODX_MAIL_FROM, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(MODX_MAIL_FROM_NAME, $this->modx->getOption('site_name'));
        $this->modx->mail->set(MODX_MAIL_SENDER, $this->modx->getOption('emailsender'));
        $this->modx->mail->set(MODX_MAIL_SUBJECT, $subject);
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
    public function processResult($result = array()) {
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
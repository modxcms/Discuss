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
     * A reference to the modX instance
     * @var modX $modx
     */
    public $modx;
    /**
     * A reference to the Discuss instance
     * @var Discuss $discuss
     */
    public $discuss;
    /**
     * @var array
     */
    public $config = array();
    /**
     * An array of theme-specific options for this controller
     * @var array $options
     */
    public $options = array();
    /**
     * An array of theme-specific modules for this controller
     * @var array $options
     */
    public $modules = array();
    /**
     * An array of set placeholders for this controller to load into the template
     * @var array $placeholders
     */
    public $placeholders = array();
    /**
     * An array of REQUEST or CLI properties to pass into the controller
     * @var array $scriptProperties
     */
    public $scriptProperties = array();
    /**
     * Whether or not to load the wrapper template after gathering this controller's output
     * @var boolean $useWrapper
     */
    public $useWrapper = true;
    /**
     * Used for displaying the amount of time it took to render the page
     * @var int $debugTime
     */
    public $debugTimer = 0;

    /**
     * @param Discuss $discuss A reference to the Discuss instance
     * @param array $config An array of configuration properties about this controller
     */
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        $this->config = array_merge($this->config,$config);
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
        $controller->setOptions($controller->getDefaultOptions());
        return $controller;
    }

    /**
     * Sets up default options for this controller.
     * @return array
     */
    protected function getDefaultOptions() {
        return array();
    }

    /**
     * Sets an array of options
     * 
     * @param array $options
     * @return void
     */
    public function setOptions(array $options = array()) {
        $this->options = array_merge($this->options,$options);
    }

    /**
     * Render the controller.
     *
     * @return string
     */
    public function render() {
        if ($this->modx->getOption('discuss.debug', null, true)) {
            $this->modx->setLogTarget('ECHO');
            $this->startDebugTimer();
        }

        foreach ($this->config as $k => $v) {
            $this->setPlaceholder('controller.'.$k,$v);
        }
        foreach ($this->discuss->config as $k => $v) {
            $this->setPlaceholder('discuss.config.'.$k,$v);
        }
        
        $this->initialize();

        $allowed = $this->checkPermissions();
        if ($allowed !== true) {
            if (is_string($allowed)) {
                $this->modx->sendRedirect($allowed);
            } else {
                $this->discuss->sendUnauthorizedPage();
            }
        }

        $sessionPlace = $this->getSessionPlace();
        if (!empty($sessionPlace)) {
            $this->discuss->setSessionPlace($sessionPlace);
        }

        $this->handleActions();
        if ($this->discuss->user->isLoggedIn) {
            $this->getUserTop();
        }
        $this->process();

        if ($this->getOption('showStatistics', true)) {
            $this->getStatistics();
        }

        $title = $this->getPageTitle();
        if (!empty($title)) {
            $this->modx->setPlaceholder('discuss.pagetitle',$title);
        }

        $this->_renderBreadcrumbs();
        $this->_renderModules();

        $tpl = $this->getOption('pageTpl',false);
        $tpl = ($tpl) ? $this->discuss->config['pagesPath'] . $tpl . '.tpl' : $this->config['tpl'];
        $output = $this->_renderTemplate($tpl,$this->placeholders);

        $output = $this->afterRender($output);

        return $this->_output($output);
    }

    /**
     * Used for custom post-processing after normal process, meta loading, and breadcrumb generation
     * @return void
     */
    public function postProcess() {}

    /**
     * Used to alter the output in a controller after rendering the template
     * @param string $output
     * @return string
     */
    public function afterRender($output) { return $output; }

    /**
     * Render a template file using the given properties
     * @param string $tpl
     * @param array $properties
     * @return string
     */
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

    /**
     * Render the output by wrapping, if desired, in the wrapper.tpl
     * @param string $output
     * @return string
     */
    protected function _output($output = '') {
        $placeholders = $this->getPlaceholders();
        $placeholders['content'] = $output;

        if (!empty($_REQUEST['print'])) {
            $output = $this->_renderTemplate($this->discuss->config['pagesPath'].'print-wrapper.tpl',$placeholders);
            return $output;
        }
        $emptyTpl = in_array($this->config['controller'],array('messages/preview'));
        if ($this->modx->getOption('discuss.debug',null,false) && $this->useWrapper) {
            if (!$emptyTpl && $this->debugTimer !== false) {
                $output .= "<br />\nExecution time: ".$this->endDebugTimer()."\n";
            }
        }
        $output = trim($output);
        if (!$emptyTpl && $this->useWrapper) {
            $output = $this->_renderTemplate($this->discuss->config['pagesPath'].'wrapper.tpl',$placeholders);
        }
        return trim(trim($output),"\n");
    }

    /**
     * Used for handling POST actions prior to rendering
     * @return void
     */
    public function handleActions() {}

    /**
     * Check to see if the user can do the page's action. Return true to pass, otherwise return either a URL to
     * redirect to on an unsuccessful validation, or false to redirect to the unauthorized page.
     *
     * @return boolean
     */
    public function checkPermissions() { return true; }

    /**
     * Set a placeholder for the controller to render into the template
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setPlaceholder($key,$value) {
        $this->placeholders[$key] = $value;
    }
    /**
     * Set an array of placeholders
     * @param array $array
     * @return void
     */
    public function setPlaceholders(array $array) {
        if (!is_array($array)) return;
        $this->placeholders = array_merge($this->placeholders,$array);
    }
    /**
     * Return an array of all the set placeholders
     * @return array
     */
    public function getPlaceholders() {
        return $this->placeholders;
    }

    /**
     * Get a specific placeholder
     * @param string $key
     * @param null $default
     * @return mixed|null
     */
    public function getPlaceholder($key,$default = null) {
        return isset($this->placeholders[$key]) ? $this->placeholders[$key] : $default;
    }

    /**
     * Get a REQUEST property
     * @param string $key
     * @param mixed $default
     * @param string $checkType
     * @return mixed
     */
    public function getProperty($key,$default = null,$checkType = 'isset') {
        switch ($checkType) {
            case 'empty':
            case '!empty':
                $pass = !empty($this->scriptProperties[$key]); break;
            default:
                $pass = isset($this->scriptProperties[$key]); break;
        }
        return $pass ? $this->scriptProperties[$key] : $default;
    }

    /**
     * Get a specific page option
     *
     * @param string $key
     * @param mixed $default
     * @param string $checkType
     * @return mixed
     */
    public function getOption($key,$default = null,$checkType = '!empty') {
        switch ($checkType) {
            case 'empty':
            case '!empty':
                $pass = !empty($this->options[$key]); break;
            default:
                $pass = isset($this->options[$key]); break;
        }
        return $pass ? $this->options[$key] : $default;
    }

    /**
     * Set a specific page option
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setOption($key,$value) {
        $this->options[$key] = $value;
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
     * @return array|string
     */
    public function getBreadcrumbs() { return array(); }

    /**
     * Render any breadcrumbs for the page
     * @return void
     */
    protected function _renderBreadcrumbs() {
        if (!isset($this->options['showBreadcrumbs']) || !empty($this->options['showBreadcrumbs'])) {
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
     * Get the statistics for the bottom area of the forums
     * @return void
     */
    protected function getStatistics() {
        $this->setPlaceholder('totalPosts',number_format((int)$this->getPostCount()));
        $this->setPlaceholder('totalTopics',number_format((int)$this->getThreadCount()));
        $this->setPlaceholder('totalMembers',number_format((int)$this->modx->getCount('disUser')));

        /* active in last 40 */
        if ($this->modx->getOption('discuss.show_whos_online',null,true)) {
            $this->setPlaceholder('activeUsers', $this->discuss->hooks->load('user/active_in_last'));
        } else {
            $this->setPlaceholder('activeUsers', '');
        }

        /* total active */
        $this->setPlaceholder('totalMembersActive',number_format((int)$this->modx->getCount('disSession',array('user:!=' => 0))));
        $this->setPlaceholder('totalVisitorsActive',number_format((int)$this->modx->getCount('disSession',array('user' => 0))));

        /**
         * forum activity
         * @var disForumActivity $activity
         */
        $activity = $this->modx->getObject('disForumActivity',array(
            'day' => date('Y-m-d'),
        ));
        if (!$activity) {
            $activity = $this->modx->newObject('disForumActivity');
            $activity->set('day',date('Y-m-d'));
            $activity->save();
        }
        $this->setPlaceholders($activity->toArray('activity.'));
    }

	/**
	 * Get thread count for all boards or $boardId
     * @param int $boardId
	 * return int
	*/

	public function getThreadCount($boardId = 0) {
		$c = $this->modx->newQuery('disBoard');
		$c->select(array('thread_count' => "SUM({$this->modx->escape('disBoard')}.{$this->modx->escape('num_topics')})"));
		if ($boardId) {
			$c->where(array('id' => (int)$boardId));
		}
		if ($stmt= $c->prepare()) {
			if ($stmt->execute()) {
				if ($results= $stmt->fetchAll(PDO::FETCH_COLUMN)) {
					$count= reset($results);
					$count= intval($count);
				}
			}
		}
		return $count ? $count : 0;
	}

	/**
	 * Get total post count
     * @param string $class
     * @param int $id
	 * return int
	*/

	public function getPostCount($className = 'disBoard', $id = 0) {
		$c = $this->modx->newQuery($className);
		if ($className == 'disBoard') {
			if (!$id) {
				$c->select(array('post_count' => "SUM({$this->modx->escape('disBoard')}.{$this->modx->escape('total_posts')})"));
			} else {
				$c->select(array($this->modx->getSelectColumns('disBoard', 'disBoard', '', array('post_count'))));
				$c->where(array('id' => $id));
			}
		} else if ($className == 'disThread') {
			$c->select(array($this->modx->getSelectColumns('disThread', 'disThread', '', array('replies'))));
			$c->where(array('id' => $id));
		}
		if ($stmt= $c->prepare()) {
			if ($stmt->execute()) {
				if ($results= $stmt->fetchAll(PDO::FETCH_COLUMN)) {
					$count= reset($results);
					$count= intval($count);
				}
			}
		}
		return (!$results) ? 0 : ($className == 'disBoard') ? $count : ($count + 1); // +1 for original thread start post
	}

    public function getUserTop() {
        /* topbar profile links. Moved from class Discuss */
        if ($this->discuss->user->isLoggedIn) {
            $authphs = array(
                'authLink' => '<a href="'.$this->discuss->url.'logout">Logout</a>',
            );
            $authphs = array_merge($this->discuss->user->toArray('user.'),$authphs);
            $authphs['user.avatar_url'] = $this->discuss->user->getAvatarUrl();

            /* Get counts */
            $authphs['user.unread_messages_count'] = $newMessages = $this->discuss->user->countUnreadMessages();

            $authphs['user.unread_posts_count'] = $unreadPosts = $this->discuss->user->countUnreadPosts();
            $authphs['user.new_replies_count'] = $newReplies = $this->discuss->user->countNewReplies();
            $authphs['user.unanswered_questions_count'] = $unansweredQuestions = $this->discuss->user->countUnansweredQuestions();
            $authphs['user.no_replies_count'] = $noReplies = $this->discuss->user->countWithoutReplies();

            /* Format counts nicely */
            $authphs['user.unread_messages'] = ($newMessages > 1) ?
                $this->modx->lexicon('discuss.user.new_messages',array('total' => $newMessages)) : (
                ($newMessages == 1) ?
                    $this->modx->lexicon('discuss.user.one_new_message') :
                    $this->modx->lexicon('discuss.user.no_new_messages')
                );
            $authphs['user.unread_posts'] = ($unreadPosts > 1) ?
                $this->modx->lexicon('discuss.user.new_posts', array('total' => $unreadPosts)) : (
                ($unreadPosts == 1) ?
                    $this->modx->lexicon('discuss.user.one_new_post') :
                    $this->modx->lexicon('discuss.user.no_new_posts')
                );
            $authphs['user.new_replies'] = ($newReplies > 1) ?
                $this->modx->lexicon('discuss.user.new_replies',array('total' => $newReplies)) : (
                ($newReplies == 1) ?
                    $this->modx->lexicon('discuss.user.one_new_reply') :
                    $this->modx->lexicon('discuss.user.no_new_replies')
                );
            $authphs['user.unanswered_questions'] = ($unansweredQuestions > 1) ?
                $this->modx->lexicon('discuss.user.unanswered_questions',array('total' => $unansweredQuestions)) : (
                ($unansweredQuestions == 1) ?
                    $this->modx->lexicon('discuss.user.one_unanswered_question') :
                    $this->modx->lexicon('discuss.user.no_unanswered_questions')
                );
            $authphs['user.no_replies'] = ($noReplies > 1) ?
                $this->modx->lexicon('discuss.user.no_replies',array('total' => $noReplies)) : (
                ($noReplies == 1) ?
                    $this->modx->lexicon('discuss.user.one_no_reply') :
                    $this->modx->lexicon('discuss.user.no_no_replies')
                );
            $this->discuss->user->isGlobalModerator();
            $this->discuss->user->isAdmin();
        } else {
            $authphs = array(
                'authLink' => '<a href="'.$this->discuss->url.'login">Login</a>',
                'user.avatar_url' => '',
                'user.unread_messages' => '',
            );
        }
        $this->modx->toPlaceholders($authphs,'discuss');
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

    /**
     * @return array
     */
    public function getModules() {
        return $this->modules;
    }

    /**
     * @param array $modules
     */
    public function setModules(array $modules = array()) {
        $this->modules = $modules;
    }

    /**
     * Renders modules defined in the manifest
     *
     * @access private
     * @return void
     */
    private function _renderModules() {
        $modules = $this->getModules();
        if (!empty($modules) && is_array($modules)) {
            $placeholders = $this->getPlaceholders();
            foreach ($modules as $key => $config) {
                $tpl = (isset($config['tpl']) && !empty($config['tpl'])) ? $config['tpl'] : false;
                if (!$tpl) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR,"[DiscussController] Error: no tpl passed for module {$key}.",'','_renderModules',__FILE__,__LINE__);
                    continue;
                }
                $phs = (isset($config['options']) && is_array($config['options'])) ? array_merge($placeholders, $config['options']) : $placeholders;
                $tpl = $this->discuss->getChunk($tpl, $phs, 'modulesPath');
                if (!$tpl) {
                    $this->modx->log(modX::LOG_LEVEL_ERROR,"[DiscussController] Error: tpl for module {$key} is empty.",'','_renderModules',__FILE__,__LINE__);
                    continue;
                }
                $this->setPlaceholder($key, $tpl);
            }
        }
    }
}

/**
 * Used for old-style deprecated controllers
 * 
 * @package discuss
 * @subpackage controllers
 */
class DiscussDeprecatedController extends DiscussController {
    /**
     * {@inheritDoc}
     */
    public function getPageTitle() { return ''; }
    /**
     * {@inheritDoc}
     */
    public function getSessionPlace() { return ''; }
    /**
     * {@inheritDoc}
     */
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

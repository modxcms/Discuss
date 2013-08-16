<?php
/**
 * @package discuss
 * [+phpdoc-subpackage+]
 */
class disModUser extends modUser {
    /**
     * If the user is inactive
     * @const INACTIVE
     */
    const INACTIVE = 0;
    /**
     * If the user is active
     * @const ACTIVE
     */
    const ACTIVE = 1;
    /**
     * If the user is unconfirmed
     * @const UNCONFIRMED
     */
    const UNCONFIRMED = 2;
    /**
     * If the user is banned
     * @const BANNED
     */
    const BANNED = 3;
    /**
     * If the user is awaiting moderation
     * @const AWAITING_MODERATION
     */
    const AWAITING_MODERATION = 4;

    /**
     * If the user is in an Administrator group
     * @var boolean
     */
    public $isAdmin = null;
    /**
     * If the user is in a global Moderator group
     * @var boolean
     */
    public $isGlobalModerator = null;
    /**
     * If the user is logged in
     * @var boolean
     */
    public $isLoggedIn = false;
    /**
     * An array of read boards for the user
     * @var array
     */
    public $readBoards = array();
    /**
     * An array of prepared, cached read boards for the user
     * @var boolean
     */
    public $readBoardsPrepared = false;

    /**
     * Whether or not a user is a moderator for a given board
     * @var array
     */
    public $moderatorships = array();

    /**
     * @var disParser $parser
     */
    public $parser;
    /**
     * @var array $unreadBoardsCount
     */
    public $unreadBoardsCount = array();

    // Better debugging when query fails
    public static function & _loadRows(& $xpdo, $className, $criteria) {
        // Modify criteria partially. Loads all extra fields for disModUser
        $criteria->query['columns'] = array();
        $criteria->select(array(
            $xpdo->getSelectColumns('disModUser', 'disModUser'),
            $xpdo->getSelectColumns('modUserProfile', 'Profile'),
            $xpdo->getSelectColumns('disProfile', 'disProfile', '', array('internalKey'), true)
        ));
        $criteria->leftJoin('modUserProfile', 'Profile');
        $criteria->leftJoin('disProfile', 'disProfile');
        $rows= null;
        if ($criteria->prepare()) {
            if ($xpdo->getDebug() === true) $xpdo->log(xPDO::LOG_LEVEL_DEBUG, "Attempting to execute query using PDO statement object: " . print_r($criteria->sql, true) . print_r($criteria->bindings, true));
            $tstart= $xpdo->getMicroTime();
            if (!$criteria->stmt->execute()) {
                $tend= $xpdo->getMicroTime();
                $totaltime= $tend - $tstart;
                $xpdo->queryTime= $xpdo->queryTime + $totaltime;
                $xpdo->executedQueries= $xpdo->executedQueries + 1;
                $errorInfo= $criteria->stmt->errorInfo();
                $xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Error ' . $criteria->stmt->errorCode() . " executing statement: \n" . print_r($errorInfo, true). "\nUsing Query:\n" . $criteria->toSQL() . "\n");
                if (($errorInfo[1] == '1146' || $errorInfo[1] == '1') && $xpdo->getOption(xPDO::OPT_AUTO_CREATE_TABLES)) {
                    if ($xpdo->getManager() && $xpdo->manager->createObjectContainer($className)) {
                        $tstart= $xpdo->getMicroTime();
                        if (!$criteria->stmt->execute()) {
                            $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $criteria->stmt->errorCode() . " executing statement: \n" . print_r($criteria->stmt->errorInfo(), true) . "\nUsing Query:\n" . $criteria->toSQL() . "\n");
                        }
                        $tend= $xpdo->getMicroTime();
                        $totaltime= $tend - $tstart;
                        $xpdo->queryTime= $xpdo->queryTime + $totaltime;
                        $xpdo->executedQueries= $xpdo->executedQueries + 1;
                    } else {
                        $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $xpdo->errorCode() . " attempting to create object container for class {$className}:\n" . print_r($xpdo->errorInfo(), true));
                    }
                }
            }
            $rows= & $criteria->stmt;
        } else {
            $errorInfo = $xpdo->errorInfo();
            $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error preparing statement for query: {$criteria->sql} - " . print_r($errorInfo, true));
            if (($errorInfo[1] == '1146' || $errorInfo[1] == '1') && $xpdo->getOption(xPDO::OPT_AUTO_CREATE_TABLES)) {
                if ($xpdo->getManager() && $xpdo->manager->createObjectContainer($className)) {
                    if (!$criteria->prepare()) {
                        $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error preparing statement for query: {$criteria->sql} - " . print_r($errorInfo, true));
                    } else {
                        $tstart= $xpdo->getMicroTime();
                        if (!$criteria->stmt->execute()) {
                            $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $criteria->stmt->errorCode() . " executing statement: \n" . print_r($criteria->stmt->errorInfo(), true));
                        }
                        $tend= $xpdo->getMicroTime();
                        $totaltime= $tend - $tstart;
                        $xpdo->queryTime= $xpdo->queryTime + $totaltime;
                        $xpdo->executedQueries= $xpdo->executedQueries + 1;
                    }
                } else {
                    $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error " . $xpdo->errorCode() . " attempting to create object container for class {$className}:\n" . print_r($xpdo->errorInfo(), true));
                }
            }
        }
        return $rows;
    }

    /**
     * Load an instance of an modDisUser or derivative class.
     */
    public static function load(xPDO & $xpdo, $className, $criteria, $cacheFlag= true) {
        $instance= null;
        $fromCache= false;
        if ($className= $xpdo->loadClass($className)) {
            if (!is_object($criteria)) {
                $criteria= $xpdo->getCriteria($className, $criteria, $cacheFlag);
                $criteria->prepare();
            }
            if (is_object($criteria)) {
                //$criteria = $xpdo->addDerivativeCriteria($className, $criteria); Do not want to assign class_key
                $row= null;
                if ($xpdo->_cacheEnabled && $criteria->cacheFlag && $cacheFlag) {
                    $row= $xpdo->fromCache($criteria, $className);
                }
                if ($row === null || !is_array($row)) {
                    if ($rows= disModUser :: _loadRows($xpdo, $className, $criteria)) {
                        $row= $rows->fetch(PDO::FETCH_ASSOC);
                        $rows->closeCursor();
                    }
                } else {
                    $fromCache= true;
                }
                if (!is_array($row)) {
                    if ($xpdo->getDebug() === true) $xpdo->log(xPDO::LOG_LEVEL_DEBUG, "Fetched empty result set from statement: " . print_r($criteria->sql, true) . " with bindings: " . print_r($criteria->bindings, true));
                } else {
                    $instance= disModUser :: _loadInstance($xpdo, $className, $criteria, $row);
                    if (is_object($instance)) {
                        if (!$fromCache && $cacheFlag && $xpdo->_cacheEnabled) {
                            $xpdo->toCache($criteria, $instance, $cacheFlag);
                            if ($xpdo->getOption(xPDO::OPT_CACHE_DB_OBJECTS_BY_PK) && ($cacheKey= $instance->getPrimaryKey()) && !$instance->isLazy()) {
                                $pkCriteria = $xpdo->newQuery($className, $cacheKey, $cacheFlag);
                                $xpdo->toCache($pkCriteria, $instance, $cacheFlag);
                            }
                        }
                        if ($xpdo->getDebug() === true) $xpdo->log(xPDO::LOG_LEVEL_DEBUG, "Loaded object instance: " . print_r($instance->toArray('', true), true));
                    }
                }
            } else {
                $xpdo->log(xPDO::LOG_LEVEL_ERROR, 'No valid statement could be found in or generated from the given criteria.');
            }
        } else {
            $xpdo->log(xPDO::LOG_LEVEL_ERROR, 'Invalid class specified: ' . $className);
        }
        return $instance;
    }

    /**
     * Load a collection of modDisUser instances.
     */
    public static function loadCollection(xPDO & $xpdo, $className, $criteria= null, $cacheFlag= true) {
        $objCollection= array ();
        $fromCache = false;
        if (!$className= $xpdo->loadClass($className)) return $objCollection;
        $rows= false;
        $fromCache= false;
        $collectionCaching = (integer) $xpdo->getOption(xPDO::OPT_CACHE_DB_COLLECTIONS, array(), 1);
        if (!is_object($criteria)) {
            $criteria= $xpdo->getCriteria($className, $criteria, $cacheFlag);
        }
        // No derivative wanted
        if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag) {
            $rows= $xpdo->fromCache($criteria);
            $fromCache = (is_array($rows) && !empty($rows));
        }
        if (!$fromCache && is_object($criteria)) {
            $rows= disModUser :: _loadRows($xpdo, $className, $criteria);
        }
        if (is_array ($rows)) {
            foreach ($rows as $row) {
                disModUser :: _loadCollectionInstance($xpdo, $objCollection, $className, $criteria, $row, $fromCache, $cacheFlag);
            }
        } elseif (is_object($rows)) {
            $cacheRows = array();
            while ($row = $rows->fetch(PDO::FETCH_ASSOC)) {
                xPDOObject :: _loadCollectionInstance($xpdo, $objCollection, $className, $criteria, $row, $fromCache, $cacheFlag);
                if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag && !$fromCache) $cacheRows[] = $row;
            }
            if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag && !$fromCache) $rows =& $cacheRows;
        }
        if (!$fromCache && $xpdo->_cacheEnabled && $collectionCaching > 0 && $cacheFlag && !empty($rows)) {
            $xpdo->toCache($criteria, $rows, $cacheFlag);
        }
        return $objCollection;
    }

    /**
     * Load a collection of modDisUser instances and a graph of related objects.
     */
    public static function loadCollectionGraph(xPDO & $xpdo, $className, $graph, $criteria, $cacheFlag) {
        $objCollection = array();
        if ($query= $xpdo->newQuery($className, $criteria, $cacheFlag)) {
            $query->bindGraph($graph);
            $rows = array();
            $fromCache = false;
            $collectionCaching = (integer) $xpdo->getOption(xPDO::OPT_CACHE_DB_COLLECTIONS, array(), 1);
            if ($collectionCaching > 0 && $xpdo->_cacheEnabled && $cacheFlag) {
                $rows= $xpdo->fromCache($query);
                $fromCache = !empty($rows);
            }
            if (!$fromCache) {
                if ($query->prepare()) {
                    if ($query->stmt->execute()) {
                        $objCollection= $query->hydrateGraph($query->stmt, $cacheFlag);
                    } else {
                        $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error {$query->stmt->errorCode()} executing query: {$query->sql} - " . print_r($query->stmt->errorInfo(), true));
                    }
                } else {
                    $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Error {$xpdo->errorCode()} preparing statement: {$query->sql} - " . print_r($xpdo->errorInfo(), true));
                }
            } elseif (!empty($rows)) {
                $objCollection= $query->hydrateGraph($rows, $cacheFlag);
            }
        }
        return $objCollection;
    }

    /**
     * Loads an instance from an associative array.
     *
     * @static
     * @param xPDO &$xpdo A valid xPDO instance.
     * @param string $className Name of the class.
     * @param xPDOQuery|string $criteria A valid xPDOQuery instance or relation alias.
     * @param array $row The associative array containing the instance data.
     * @return xPDOObject A new xPDOObject derivative representing a data row.
     */
    public static function _loadInstance(& $xpdo, $className, $criteria, $row) {
        $rowPrefix= '';
        if (is_object($criteria) && $criteria instanceof xPDOQuery) {
            $alias = $criteria->getAlias();
            $actualClass = $criteria->getClass();
        } elseif (is_string($criteria) && !empty($criteria)) {
            $alias = $criteria;
            $actualClass = $className;
        } else {
            $alias = $className;
            $actualClass= $className;
        }
        // Removed possibility to overload which class will be instantiated using class_key field
        /** @var xPDOObject $instance */
        $instance= $xpdo->newObject($actualClass);
        if (is_object($instance) && $instance instanceof xPDOObject) {
            $pk = $xpdo->getPK($actualClass);
            if ($pk) {
                if (is_array($pk)) $pk = reset($pk);
                if (isset($row["{$alias}_{$pk}"])) {
                    $rowPrefix= $alias . '_';
                }
                elseif ($actualClass !== $className && $actualClass !== $alias && isset($row["{$actualClass}_{$pk}"])) {
                    $rowPrefix= $actualClass . '_';
                }
                elseif ($className !== $alias && isset($row["{$className}_{$pk}"])) {
                    $rowPrefix= $className . '_';
                }
            } elseif (strpos(strtolower(key($row)), strtolower($alias . '_')) === 0) {
                $rowPrefix= $alias . '_';
            } elseif (strpos(strtolower(key($row)), strtolower($className . '_')) === 0) {
                $rowPrefix= $className . '_';
            }
            $parentClass = $className;
            $isSubPackage = strpos($className,'.');
            if ($isSubPackage !== false) {
                $parentClass = substr($className,$isSubPackage+1);
            }
            if (!$instance instanceof $parentClass) {
                $xpdo->log(xPDO::LOG_LEVEL_ERROR, "Instantiated a derived class {$actualClass} that is not a subclass of the requested class {$className}");
            }
            $instance->_lazy= $actualClass !== $className ? array_keys($xpdo->getFieldMeta($actualClass)) : array_keys($instance->_fieldMeta);
            $instance->fromArray($row, $rowPrefix, true, true);
            $instance->_dirty= array ();
            $instance->_new= false;
        }
        return $instance;
    }

    public function save($cacheFlag = false) {
        $profileFields = $this->xpdo->getFields('modUserProfile');
        $disProfileFields = $this->xpdo->getFields('disProfile');
        $userFields = $this->xpdo->getFields('modUser');

        foreach($this->_fields as $key => $value) {
            if ($this->isDirty($key)) {

            }
        }
    }
    /**
     * Initialize the user, setup basic metadata for them, and log their activity time.
     * @return bool
     */
    public function init() {
        $this->isLoggedIn = true;

        /* active user, update the disUser record */
        if (!$activity = $this->getOne('Activity', array('internalKey' => $this->get('id')))) {
            $activity = $this->xpdo->newObject('modActiveUser');
        }
        if ($activity->isNew()) {
            $activity->set('internalKey', $this->get('id'));
        }
        $activity->fromArray(array(
            'lasthit' => time(),
            'ip' => $this->xpdo->discuss->getIp(),
            'action' => 'discuss/' . $this->xpdo->discuss->request->getControllerValue() // Not really used at the moment. Just to make sure that actions do not interfere with manager logging
        ));
        $activity->save();
        $this->isAdmin();

        return true;
    }

    /**
     * Prepare a cache of all read boards for this user
     *
     * @return array
     */
    public function prepareReadBoards() {
        if (!$this->readBoardsPrepared) {
            $stmt = $this->xpdo->query('
            SELECT
                `board`,
                COUNT(`id`) AS `read`,
                (
                    SELECT num_topics FROM '.$this->xpdo->getTableName('disBoard').' AS `Board`
                    WHERE `Board`.`id` = `Read`.`board`
                ) AS `threads`
            FROM '.$this->xpdo->getTableName('disThreadRead').' `Read`
            WHERE `user` = '.$this->get('id').'
            GROUP BY `board`');
            if ($stmt) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $this->unreadBoardsCount[$row['board']] = $row['threads'] - $row['read'];
                    $this->readBoards[$row['board']] = $row['threads'] - $row['read'] > 0;
                }
                $stmt->closeCursor();
            }
            $this->readBoardsPrepared = true;
        }
        return $this->readBoards;
    }

    /**
     * See if a board is read by this user
     *
     * @param int $boardId
     * @return bool
     */
    public function isBoardRead($boardId) {
        $this->prepareReadBoards();
        return !empty($this->readBoards[$boardId]);
    }

    /**
     * Get all unread threads by this user for a specific board
     * @param int $boardId The ID of the board to look for
     * @return array An array of thread data
     */
    public function getUnreadThreadsForBoard($boardId) {
        $threads = array();
        $stmt = $this->xpdo->query("SELECT GROUP_CONCAT(`disThread`.`id` SEPARATOR ',') AS `threads`
				FROM `modx_discuss_threads` `disThread`
				WHERE NOT EXISTS(SELECT `read`.`thread` FROM modx_discuss_threads_read `read` WHERE `read`.`thread` = `disThread`.`id` AND `read`.`user` = {$this->get('id')})
				AND `disThread`.`board` = $boardId;");
        if ($stmt) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!empty($row)) {
                $threads = explode(',',$row['threads']);
            }
            $stmt->closeCursor();
        }
        return $threads;
    }


    /**
     * Overrides xPDOObject::get to provide formatting for certain fields
     *
     * @param string $k
     * @param string $format
     * @param string $formatTemplate
     * @return mixed|string
     */
    public function get($k,$format = '',$formatTemplate = '') {
        $v = parent::get($k,$format,$formatTemplate);
        if ($this->xpdo->context->key != 'mgr') {
            switch ($k) {
                case 'gender_formatted':
                    switch ($this->get('gender')) {
                        case 'm': $v = $this->xpdo->lexicon('discuss.male'); break;
                        case 'f': $v = $this->xpdo->lexicon('discuss.female'); break;
                        default: $v = ''; break;
                    }
                    break;
                case 'age':
                    $v = strtotime($this->get('birthdate'));
                    $v = floor((time() - $v) / 60 / 60 / 24 / 365);
                    $v = !empty($v) ? $v : '';
                    break;
                case 'ip':
                    if (!$this->xpdo->hasPermission('discuss.track_ip')) {
                        $v = '';
                    }
                    break;
                case 'last_active':
                    if (!$this->get('show_online') && !$this->isAdmin()) {
                        $v = '';
                    } elseif (!empty($v) && $v != '-001-11-30 00:00:00') {
                        $v = strftime($this->xpdo->discuss->dateFormat,strtotime($v));
                    } else {
                        $v = '';
                    }
                    break;
                case 'email':
                    if (!$this->get('show_email') && !$this->isAdmin()) {
                        $v = '';
                    }
                    break;
                case 'name':
                    if ($this->get('use_display_name')) {
                        $v = $this->get('display_name');
                    }
                    if (empty($v)) {
                        $v = $this->get('username');
                    }
                    break;
                case 'posts_formatted': {
                    $v = $this->get('posts');
                    $v = number_format($v);
                    break;
                }
            }
        }
        return $v;
    }

    /**
     * Override toArray to provide more values
     *
     * @param string $keyPrefix
     * @param bool $rawValues
     * @param bool $excludeLazy
     * @param bool $includeRelated
     * @return array
     */
    public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated = false) {
        $values = parent :: toArray($keyPrefix,$rawValues,$excludeLazy, $includeRelated);
        $values[$keyPrefix.'age'] = $this->get('age');
        $values[$keyPrefix.'gender_formatted'] = $this->get('gender_formatted');
        $values[$keyPrefix.'avatarUrl'] = $this->getAvatarUrl();
        $values[$keyPrefix.'isSelf'] = $this->get('id');
        $values[$keyPrefix.'canEdit'] = $values[$keyPrefix.'isSelf'];
        $values[$keyPrefix.'canAccount'] = $values[$keyPrefix.'isSelf'];
        $values[$keyPrefix.'canMerge'] = $values[$keyPrefix.'isSelf'];
        $values[$keyPrefix.'name'] = $this->get('name');
        $values[$keyPrefix.'posts_formatted'] = $this->get('posts_formatted');
        return $values;
    }

    /**
     * Gets the avatar URL for this user, depending on the avatar service.
     * @return string
     */
    public function getAvatarUrl() {
        $avatarUrl = '';

        $avatarService = $this->get('avatar_service');
        $avatar = $this->get('avatar');
        if (!empty($avatar) || !empty($avatarService)) {
            if (!empty($avatarService)) {
                if ($avatarService == 'gravatar') {
                    $avatarUrl = $this->xpdo->getOption('discuss.gravatar_url',null,'http://www.gravatar.com/avatar/').md5(strtolower(trim($this->_fields['email'])));
                    $avatarUrl .= '?d='.$this->xpdo->getOption('discuss.gravatar_default',null,'mm');
                    $avatarUrl .= '&r='.$this->xpdo->getOption('discuss.gravatar_rating',null,'g');
                }
            } else {
                $avatarUrl = $this->xpdo->getOption('discuss.files_url').'/profile/'.$this->get('user').'/'.$avatar;
            }
        }
        return $avatarUrl;
    }

    /**
     * Parse and return the signature for this user
     * @return string
     */
    public function parseSignature() {
        $message = $this->get('signature');
        $maxLength = $this->xpdo->getOption('discuss.signatures.max_length', null, 2000);
        if (strlen($message) > $maxLength) {
            $message = substr($message, 0, $maxLength);
        }
        if (!empty($message)) {
            $message = str_replace(array('&#91;','&#93;'),array('[',']'),$message);

            /* Check custom content parser setting */
            if ($this->xpdo->getOption('discuss.use_custom_post_parser',null,false)) {
                /* Load custom parser */
                $parsed = $this->xpdo->invokeEvent('OnDiscussPostCustomParser', array(
                    'content' => &$message,
                ));
                if (is_array($parsed)) {
                    foreach ($parsed as $msg) {
                        if (!empty($msg)) {
                            $message = $msg;
                        }
                    }
                } else if (!empty($parsed)) {
                    $message = $parsed;
                }
            } else if (true) {
                $message = $this->parseBBCode($message);
            }

            /* Allow for plugin to change content of posts after it has been parsed */
            $rs = $this->xpdo->invokeEvent('OnDiscussPostFetchContent',array(
                'content' => &$message,
            ));

            if (is_array($rs)) {
                foreach ($rs as $msg) {
                    if (!empty($msg)) {
                        $message = $msg;
                    }
                }
            } else if (!empty($rs)) {
                $message = $rs;
            }
        }
        return $message;
    }

    /**
     * Convert all linebreaks to br tags
     * @param string $str
     * @return mixed
     */
    private function _nl2br2($str) {
        $str = str_replace("\r", '', $str);
        return preg_replace('/(?<!>)\n/', "<br />\n", $str);
    }


    /**
     * Parse BBCode in post and return proper HTML. Supports SMF/Vanilla formats.
     *
     * @param $message The string to parse
     * @return string The parsed string with HTML instead of BBCode, and all code stripped
     */
    public function parseBBCode($message) {
        $parserClass = $this->xpdo->getOption('discuss.parser_class',null,'disBBCodeParser');
        $parserClassPath = $this->xpdo->getOption('discuss.parser_class_path');
        if (empty($this->parser)) {
            if (empty($parserClassPath)) {
                $parserClassPath = $this->xpdo->discuss->config['modelPath'].'discuss/parser/';
            }
            $this->parser = $this->xpdo->getService('disParser',$parserClass,$parserClassPath);
        }
        if (empty($this->parser)) {
            /* If we can't find the parser, log an error and return an empty message. */
            $this->xpdo->log(modX::LOG_LEVEL_ERROR,'Error loading signature parser ' . $parserClass . ' from ' . $parserClassPath);
            return '';
        }
        $allowedBBCodes = $this->xpdo->getOption('discuss.signatures.allowed_bbcodes', null,'b,i,u,s,url,quote,pre,ul,ol,list,smileys,rtl,hr,color,size');
        $allowedBBCodes = explode(',', $allowedBBCodes);
        $message = $this->parser->parse($message, $allowedBBCodes);
        $message = str_replace('&#39;','’',$message);
        $message = $this->stripBBCode($message);
        return $message;
    }

    /**
     * Strip BBCode from a string
     *
     * @param $str
     * @return mixed
     */
    public function stripBBCode($str) {
        $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
        $replace = '';
        return preg_replace($pattern, $replace, $str);
    }

    /**
     * Get a count of # of unread messages for the user
     *
     * @return int
     */
    public function countUnreadMessages() {
        $c = $this->xpdo->newQuery('disThread');
        $c->innerJoin('disThreadUser','Users');
        $c->leftJoin('disThreadRead','Reads','Reads.user = '.$this->get('id').' AND disThread.id = Reads.thread');
        $c->where(array(
            'disThread.private' => true,
            'Users.user' => $this->get('id'),
            'Reads.thread:IS' => null,
        ));
        return $this->xpdo->getCount('disThread',$c);
    }
    /**
     * Get the number of unread posts since last visit.
     * @return int
     */
    public function countUnreadPosts() {
        $response = $this->xpdo->call('disThread','fetchUnread',array(&$this->xpdo,"{$this->xpdo->escape('disThread')}.{$this->xpdo->escape('post_last_on')}",'DESC',10 ,0 ,true, true));
        return number_format($response['total']);
    }
    /**
     * Get the number of new replies to topics the user participates in since last visit.
     * @return int
     */
    public function countNewReplies() {
        $response = $this->xpdo->call('disThread','fetchNewReplies',array(&$this->xpdo,"{$this->xpdo->escape('disThread')}.{$this->xpdo->escape('post_last_on')}",'DESC',10,0, false, true));
        return number_format($response['total']);
    }
    /**
     * Get the number of threads without replies.
     * @return int
     */
    public function countWithoutReplies() {
        $response = $this->xpdo->call('disThread','fetchWithoutReplies',array(&$this->xpdo,"{$this->xpdo->escape('disThread')}.{$this->xpdo->escape('post_last_on')}",'DESC',10,0, false, true));
        return number_format($response['total']);
    }
    /**
     * Get the number of unanswered questions.
     * @return int
     */
    public function countUnansweredQuestions() {
        $response = $this->xpdo->call('disThread','fetchUnansweredQuestions',array(&$this->xpdo,"{$this->xpdo->escape('disThread')}.{$this->xpdo->escape('post_last_on')}",'DESC',10,0, false, true));
        return number_format($response['total']);
    }

    /**
     * Clear the cache for this User
     *
     * @return void
     */
    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/user/'.$this->get('id'));
            $this->xpdo->cacheManager->delete('discuss/board/user/'.$this->get('id'));
        }
    }

    /**
     * Parse a bbcode quote tag and return result
     *
     * @param $message The string to parse
     * @return string The quoted message
     */
    public function parseQuote($message) {
        preg_match_all("#\[quote=?(.*?)[\"']?\](.*?)\[/quote\]#si",$message,$matches);
        if (!empty($matches)) {
            $quotes = array();
            $replace = array();
            $meta = array();
            $with = array();
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) { $replace[] = $match; }
                foreach ($matches[1] as $match) { $meta[] = $match; }
                foreach ($matches[2] as $match) { $with[] = $match; }
            }
            for ($i=0;$i<count($replace);$i++) {
                $auth = array();
                $mt = explode(' ',$meta[$i]);
                foreach ($mt as $m) {
                    if (empty($m)) continue;
                    $m = explode('=',$m);
                    switch ($m[0]) {
                        case 'author': $auth['user'] = $m[1]; break;
                        case 'date': $auth['date'] = $m[1]; break;
                        case 'link': $auth['link'] = $m[1]; break;
                    }
                }
                $cite = '';
                if (!empty($auth['user']) || !empty($auth['date'])) {
                    $cite = '<cite>Quote';
                    if (!empty($auth['user'])) $cite .= ' from: '.$auth['user'];
                    if (!empty($auth['date'])) $cite .= ' at '.strftime($this->xpdo->discuss->dateFormat,$auth['date']);
                    $cite .= '</cite>';
                }

                /* strip annoying starting br tags */
                $with[$i] = substr($with[$i],0,6) == '<br />' ? $with[$i] = substr($with[$i],6) : $with[$i];

                /* now insert our quote */
                $message = str_replace($replace[$i],$cite.'<blockquote>'.$with[$i].'</blockquote>',$message);
            }
        }
        return $message;
    }

    /**
     * Return whether or not the user is an Administrator
     * @return boolean
     */
    public function isAdmin() {
        if (!$this->isLoggedIn) {
            $this->isAdmin = false;
        }
        if ($this->isAdmin == null) {
            $this->isAdmin = false;
            $adminGroups = $this->xpdo->getOption('discuss.admin_groups',null,'');
            $adminGroups = explode(',',$adminGroups);
            $level = 9999;
            if ($this->isMember($adminGroups)) {
                $this->isAdmin = true;
            }
        }
        $this->xpdo->setPlaceholder('discuss.user.isAdmin',$this->isAdmin);
        if (!array_key_exists('discuss.user.isModerator',$this->xpdo->placeholders) && $this->isAdmin) {
            $this->xpdo->setPlaceholder('discuss.user.isModerator',true);
        }
        $this->xpdo->setPlaceholder('discuss.user.isModerator',$this->isAdmin);
        return $this->isAdmin;
    }

    /**
     * Return whether or not the user is a global Moderator
     * @return boolean
     */
    public function isGlobalModerator() {
        if (!$this->isLoggedIn) {
            $this->isGlobalModerator = false;
        }
        if ($this->isGlobalModerator == null) {
            $this->isGlobalModerator = false;
            $moderators = $this->xpdo->getOption('discuss.global_moderators',null,'');
            $moderators = explode(',',$moderators);
            if (in_array($this->get('username'),$moderators)) {
                $this->isGlobalModerator = true;
            }
            $this->xpdo->setPlaceholder('discuss.user.isModerator',$this->isGlobalModerator);
        }
        return $this->isGlobalModerator;
    }

    /**
     * See if a user is a moderator of a board
     * @param int $boardId
     * @return bool
     */
    public function isModerator($boardId) {
        if (!array_key_exists($boardId,$this->moderatorships)) {
            if ($this->isGlobalModerator() || $this->isAdmin()) {
                $isModerator = true;
            } else {
                $moderator = $this->xpdo->getCount('disModerator',array(
                    'user' => $this->get('id'),
                    'board' => $boardId,
                ));
                $isModerator = $moderator > 0;
            }
            $this->moderatorships[$boardId] = $isModerator;
            $this->xpdo->setPlaceholder('discuss.user.isModerator',$isModerator);
        }
        return $this->moderatorships[$boardId];
    }

    /**
     * Fetch a list of active users in the forum
     *
     * @static
     * @param xPDO $modx A reference to the modX object
     * @param int $timeAgo If set, will grab within X seconds
     * @param int $limit Limit results to this number
     * @param int $start Start at this index
     * @return array A response array of active users
     */
    public static function fetchActive(xPDO &$modx,$timeAgo = 0,$limit = 0,$start = 0) {
        $response = array();

        $c = $modx->newQuery('disUser');
        $c->innerJoin('disSession','Session',$modx->getSelectColumns('disSession','Session','',array('user')).' = '.$modx->getSelectColumns('disUser','disUser','',array('id')));
        $c->innerJoin('modUser','User');
        $c->leftJoin('disUserGroupProfile','PrimaryDiscussGroup');
        if (!empty($timeAgo)) {
            $c->where(array(
                'Session.access:>=' => $timeAgo,
            ));
        }
        if (!empty($limit)) {
            $c->limit($limit,$start);
        }
        $response['total'] = $modx->getCount('disUser',$c);
        $c->select(array(
            'disUser.id',
            'disUser.username',
            'PrimaryDiscussGroup.color',
        ));
        $c->groupby('disUser.id');
        $c->sortby('Session.access','ASC');
        $response['results'] = $modx->getCollection('disUser',$c);
        return $response;
    }

    /**
     * Return the last visited thread by the User
     * @return disThread
     */
    public function getLastVisitedThread() {
        $c = $this->xpdo->newQuery('disThread');
        $c->innerJoin('disBoard','Board');
        $c->leftJoin('disBoardUserGroup','UserGroups','Board.id = UserGroups.board');
        $groups = $this->getUserGroups();
        if (!empty($groups) && !$this->isAdmin()) {
            /* restrict boards by user group if applicable */
            $g = array(
                'UserGroups.usergroup:IN' => $groups,
            );
            $g['OR:UserGroups.usergroup:='] = null;
            $where[] = $g;
            $c->andCondition($where,null,2);
        }
        $c->where(array(
            'Board.status:!=' => disBoard::STATUS_INACTIVE,
            'id' => $this->get('thread_last_visited'),
        ));
        return $this->xpdo->getObject('disThread',$c);
    }

    /**
     * Check to see if the user qualifies for any post-based groups
     * and if so, grant it to them
     *
     * @return bool
     */
    public function checkForPostGroupAdvance() {
        $joined = false;
        $c = $this->xpdo->newQuery('disUserGroupProfile');
        $c->innerJoin('modUserGroup','UserGroup');
        $c->where(array(
            'post_based' => true,
            'min_posts:<=' => $this->get('posts'),
        ));
        $postGroups = $this->xpdo->getCollection('disUserGroupProfile',$c);
        if (!empty($postGroups)) {
            $joined = true;
            $user = $this->getOne('User');
            foreach ($postGroups as $group) {
                $user->joinGroup($group->get('usergroup'));
            }
        }
        return $joined;
    }

    /**
     * Merge another user into this account
     *
     * @param disUser $oldUser
     * @return boolean
     */
    public function merge(disUser &$oldUser) {
        $success = true;
        $user = $this;
        if (empty($user)) return false;

        $oldModxUser = &$oldUser;
        if (empty($oldModxUser)) return false;

        $this->xpdo->beginTransaction();

        /* merge post count */
        $posts = $user->get('posts');
        $posts = $posts + $oldUser->get('posts');
        $this->set('posts',$posts);

        /* merge ignore boards */
        $ibs = $this->get('ignore_boards');
        $ibs = explode(',',$ibs);
        $oldIbs = $oldUser->get('ignore_boards');
        $oldIbs = explode(',',$oldIbs);
        $ibs = array_merge($oldIbs,$ibs);
        $this->set('ignore_boards',implode(',',$ibs));

        /* merge signature if needed */
        $signature = $this->get('signature');
        $oldSignature = $oldUser->get('signature');
        if (empty($signature) && !empty($oldSignature)) {
            $this->set('signature',$oldSignature);
        }

        /* merge title if needed */
        $title = $this->get('title');
        $oldTitle = $oldUser->get('title');
        if (empty($title) && !empty($oldTitle)) {
            $this->set('title',$oldTitle);
        }

        /* merge primary_group if needed */
        $pg = $this->get('primary_group');
        $oldPg = $oldUser->get('primary_group');
        if (empty($pg) && !empty($oldPg)) {
            $this->set('primary_group',$oldPg);
        }

        $this->set('integrated_id',$oldUser->get('integrated_id'));
        $this->set('synced',true);
        $this->set('syncedat',$this->xpdo->discuss->now());

        $this->save();

        /* grant old usergroups to this user */
        $oldUserGroups = $this->xpdo->getCollection('modUserGroupMember',array('member' => $oldModxUser->get('id')));
        $ugs = array();
        foreach ($oldUserGroups as $oldUserGroup) {
            $ugs[] = $oldUserGroup->get('user_group');
        }
        $ugs = array_unique($ugs);
        foreach ($ugs as $ug) {
            $user->joinGroup($ug);
        }

        /* merge in posts, change authors */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disPost').'
            SET `author` = '.$this->get('id').'
            WHERE `author` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThread').'
            SET `author_first` = '.$this->get('id').'
            WHERE `author_first` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThread').'
            SET `author_last` = '.$this->get('id').'
            WHERE `author_last` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disThreadRead */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThreadRead').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disThreadUser */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disThreadUser').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);


        /* merge in disUserFriend */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disUserFriend').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);
        $sql = 'UPDATE '.$this->xpdo->getTableName('disUserFriend').'
            SET `friend` = '.$this->get('id').'
            WHERE `friend` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disUserNotification */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disUserNotification').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge in disModerator */
        $sql = 'UPDATE '.$this->xpdo->getTableName('disModerator').'
            SET `user` = '.$this->get('id').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* remove old user sessions */
        $sql = 'DELETE FROM '.$this->xpdo->getTableName('disUserFriend').'
            WHERE `user` = '.$oldUser->get('id').'
        ';
        $this->xpdo->query($sql);

        /* merge all PMs users fields for user */
        $c = $this->xpdo->newQuery('disThread');
        $c->innerJoin('disThreadUser','Users');
        $c->leftJoin('disThreadRead','Reads','Reads.user = '.$oldUser->get('id').' AND disThread.id = Reads.thread');
        $c->where(array(
            'disThread.private' => true,
            'Users.user' => $oldUser->get('id'),
        ));
        $pms = $this->xpdo->getCollection('disThread',$c);
        foreach ($pms as $pm) {
            $users = $pm->get('users');
            $users = explode(',',$users);
            $users = array_diff($users,array($oldUser->get('id')));
            $users[] = $this->get('id');
            $pm->set('users',implode(',',$users));
            $pm->save();
        }

        /* remove old users */
        $oldModxUser->remove();

        /* check for post group advance */
        $this->checkForPostGroupAdvance();

        $this->xpdo->commit();
        return $success;
    }

    /**
     * Gets the badge for the Primary Group for this user
     *
     * @return string
     */
    public function getGroupBadge() {
        $badge = '';
        if (!$this->PrimaryDiscussGroup) {
            $this->getOne('PrimaryDiscussGroup');
        }
        if ($this->PrimaryDiscussGroup) {
            $badge = $this->PrimaryDiscussGroup->getBadge();
        }
        return $badge;
    }

    /**
     * Makes a link to the profile page for this user
     * @return string
     */
    public function getUrl() {
        $url = $this->xpdo->discuss->request->makeUrl('user', array('type' => 'username', 'user' => $this->get('username')));
        $this->set('url',$url);
        return $url;
    }

    /**
     * Gets a User Setting (bypasses cache)
     * @param string $key
     * @param string $default
     * @return string
     */
    public function getSetting($key,$default = '') {
        $setting = $this->xpdo->getObject('modUserSetting',array(
            'key' => $key,
            'user' => $this->get('id'),
        ));
        if ($setting) {
            $default = $setting->get('value');
        }
        return $default;
    }

    /**
     * Set a custom User Setting for this user
     *
     * @param string $key The key of the Setting
     * @param string $value The value to set
     * @param string $default The default value of the setting
     * @return bool True if successfully updated
     */
    public function setSetting($key,$value,$default = null) {
        $saved = false;
        $setting = $this->xpdo->getObject('modUserSetting',array(
            'key' => $key,
            'user' => $this->get('id'),
        ));
        if ($setting) {
            if ($value == $default) {
                $setting->remove();
            } else {
                $setting->set('value',$value);
                $saved = $setting->save();
            }
        } else if ($value != $default) {
            $sysSetting = $this->xpdo->getObject('modSystemSetting',array(
                'key' => $key,
            ));
            $setting = $this->xpdo->newObject('modUserSetting');
            $setting->set('user',$this->get('id'));
            $setting->set('key',$key);
            $setting->set('value',$value);
            $setting->set('namespace','discuss');
            if ($sysSetting) {
                $setting->set('xtype',$sysSetting->get('xtype'));
                $setting->set('area',$sysSetting->get('area'));
            } else {
                $setting->set('xtype','textfield');
                $setting->set('area','General');
            }
            $saved = $setting->save();
        }
        if ($saved) {
            $this->xpdo->reloadConfig();
        }
        return $saved;
    }

    /**
     * @return boolean
     */
    public function canViewProfiles() {
        return $this->isLoggedIn && $this->xpdo->hasPermission('discuss.view_profiles');
    }

    /**
     * @return boolean
     */
    public function canViewEmails() {
        return $this->isLoggedIn && $this->xpdo->hasPermission('discuss.view_emails');
    }

    /**
     * @param int $boardId
     * @return int
     */
    public function getUnreadCount($boardId = 0) {
        $this->prepareReadBoards();
        if (isset($this->unreadBoardsCount[$boardId])) {
            return $this->unreadBoardsCount[$boardId];
        }
        return 0;
    }
}
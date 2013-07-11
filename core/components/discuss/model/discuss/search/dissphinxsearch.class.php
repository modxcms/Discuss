<?php
/**
 * Discuss
 *
 *
 * @package discuss
 */
require dirname(__FILE__).'/dissearch.class.php';
if (!class_exists('SphinxClient')) {
    require_once(dirname(__FILE__).'/sphinx/sphinxapi.php');
}
/**
 * Sphinx search for Discuss
 *
 * @package discuss
 * @subpackage search
 * @extends disSearch
 */

class disSphinxSearch extends disSearch {

    /**
     * An array of connection configuration options for the Sphinx client
     *
     * @var array $_connectionOptions
     */
    private $_connectionOptions = array();
    /**
     * Comma, space or semicolon separated string list of indices used for search
     * @var string $_indices
     */
    private $_indices;

    /**
     * The client API for the Sphinx search instance
     * @var SphinxClient $client
     */
    public $client;
    public $connected;
    /**
     * Initialize the Sphinx search engine
     * @return void
     */
    public function initialize() {
        $this->_connectionOptions = array(
            'host_name' => $this->modx->getOption('discuss.sphinx.host_name', null, 'localhost'),
            'port' => $this->modx->getOption('discuss.sphinx.port',null,9312),
            'connection_timeout' => $this->modx->getOption('discuss.sphinx.connection_timeout', null, 30),
            'searchd_retries' => $this->modx->getOption('discuss.sphinx.searchd_retries', null, 3),
            'searchd_retry_delay' => $this->modx->getOption('discuss.sphinx.searchd_retry_delay', null, 10000)
        );
        $this->_indices = $this->modx->getOption('discuss.sphinx.indexes', null, 'discuss_posts');

        $this->client = new SphinxClient();
        $this->client->SetServer($this->_connectionOptions['host_name'], $this->_connectionOptions['port']);
        $this->client->SetConnectTimeout($this->_connectionOptions['connection_timeout']);
        $this->client->SetMatchMode(SPH_MATCH_EXTENDED);

        return true;
    }

    /**
     * create search class specific time range (Sets date ranges)
     * @param null $start
     * @param null $end
     */
    public function createTimeRange(&$conditions, $start = '', $end = '') {
        if ($start != '' && $end != '') {
            $start = strtotime($start.' 00:00:00');
            $end = strtotime($end.' 23:59:59');
            $this->setFilterRange('createdon', $start, $end);
        } else if ($start != '') {
            $start = strtotime($start.' 00:00:00');
            $this->setFilterRange('createdon', $start, time());
        } else if ($end != '') {
            $end = strtotime($end.' 23:59:59');
            $this->setFilterRange('createdon', 0, $end);
        }
    }

    /**
     * Return last error
     * @param bool $conn
     * @return bool|string
     */
    public function getError($conn = false) {
        return (!$conn) ? $this->client->GetLastError() : $this->client->IsConnectError();
    }

    /**
     * Return last warning
     * @return string
     */
    public function getWarning() {
        return $this->client->GetLastWarning();
    }

    /**
     * Calls SphinxClient::query
     * @param $query
     * @param string $comment
     * @return bool
     */
    public function query($query, $comment="") {
        $results = $this->client->Query($query, $this->_indices, $comment);
        if (!$results) {
            if ($this->getError(true)) {
                $this->modx->log(modX::LOG_LEVEL_ERROR, "Sphinx search connection failed on API side");
            }
            $this->modx->log(modX::LOG_LEVEL_ERROR, "Sphinx search issued error: " . $this->getError());
        }
        if ($this->getWarning() != '') {
            $this->modx->log(modX::LOG_LEVEL_WARN, "Sphinx search issued warning: " . $this->getWarning());
        }
        return $results;
    }

    /**
     * Run the search based on the specified search string.
     *
     * @param string $string The string to run the search on.
     * @param int $limit The number of results to limit to.
     * @param int $start The starting result index to search from.
     * @param array $conditions An array of conditions to add to the search filter.
     * @return array An array of search results.
     */

    public function run($string,$limit = 10,$start = 0,array $conditions = array()) {
        $response = array(
            'results' => array(),
            'total' => 0,
        );
        $grouped = (int) $this->modx->getOption('discuss.group_by_thread', '', 1);

        if ($this->discuss->user->isLoggedIn) {
            $ignoreBoards = $this->discuss->user->get('ignore_boards');
            if (!empty($ignoreBoards)) {
                $this->setFilter('board', explode(',',$ignoreBoards), true);
            }
        }
        $this->modx->log(modX::LOG_LEVEL_ERROR, print_r($conditions['board'], true));
        if (!empty($conditions['board'])) {
            if (is_string($conditions['board'])) {
                if (stripos(',', $conditions['board']) !== false) {
                    $conditions['board'] = explode(',', $conditions['board']);
                } else {
                    $conditions['board'] = array($conditions['board']);
                }

            }
            $this->setFilter('board', $conditions['board']);
        }
        if (!empty($conditions['author'])) {
            if (is_string($conditions['author'])) {
                $string .= " @username discuss_username_{$conditions['author']}";
            } else {
                $string .= " @userid discuss_user_id_{$conditions['author']}";
            }

        }
        if (!empty($conditions['class_key'])) {
            $this->setFilter('class_key', array($conditions['class_key']));
            if (!empty($conditions['answered']) && !is_null($conditions['answered'])) {
                $this->setFilter('answered', array($conditions['answered']));
            }
        }
        $this->setLimits($start, $limit);
        if ($grouped === 1) {
            $this->setGroupBy('thread', SPH_GROUPBY_ATTR);
        }
        $results = $this->query($string);

        $retry = 1;
        while(!$results && $retry <= $this->_connectionOptions['searchd_retries']) {
            sleep($this->_connectionOptions['searchd_retry_delay']);
            $results = $this->query($string);
            $retry++;
        }
        if ($results['total'] == 0) {
            return $response;
        }
        $response['total'] = (int) $results['total'];
        foreach(array_keys($results['matches']) as $id) {
            $in[] = $id;
        }

        $c = $this->modx->newQuery('disPost');
        $c->innerJoin('disThread','Thread');
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disUser','Author');
        $c->select(array(
            $this->modx->getSelectColumns('disPost','disPost', 'group_', array('thread')),
            'replies' => 'Thread.replies',
            'username' => 'Author.username',
            'board_name' => 'Board.name'
        ));
        $c->select($this->modx->getSelectColumns('disPost','disPost'));
        $c->where(array('id:IN' => $in));
        $posts = $this->modx->getCollection('disPost', $c);
        foreach($posts as $post) {
            $postArray = $post->toArray('', true, true);
            $postArray['message'] = $post->getContent();
            $results['matches'][$post->get('id')] = $postArray;
        }
        $response['results'] = $results['matches'];
        return $response;
    }

    /**
     * Calls SphinxClient::SetFilter()
     * @param $attribute
     * @param $values
     * @param bool $exclude
     */
    public function setFilter($attribute, $values, $exclude=false) {
        $this->client->SetFilter($attribute, $values, $exclude);
    }

    /**
     * Calls SphinxClient::SetFilterRange
     * @param $attribute
     * @param $min
     * @param $max
     * @param bool $exclude
     */
    public function setFilterRange($attribute, $min, $max, $exclude=false) {
        $this->client->SetFilterRange($attribute, $min, $max, $exclude);
    }

    /**
     * Calls SphinxClient::SetGroupBy
     * @param $attribute
     * @param $func
     * @param string $groupsort
     */
    public function setGroupBy($attribute, $func, $groupsort="@group desc") {
        $this->client->SetGroupBy($attribute, $func, $groupsort);
    }

    /**
     * Calls SphinxClient::SetLimits
     * @param $offset
     * @param $limit
     * @param int $max
     * @param int $cutoff
     */
    public function setLimits ( $offset, $limit, $max=0, $cutoff=0 ) {
        $this->client->SetLimits((int)$offset, (int)$limit, $max, $cutoff);
    }
}
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
 * Default Search class for searching records in Discuss
 * 
 * @package discuss
 * @subpackage search
 */
class disSearch {

    /**
     * @param Discuss $discuss A reference to the Discuss instance
     * @param array $config An array of configuration properties
     */
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        $this->config = array_merge($config,array(

        ));
        $this->initialize();
    }

    /**
     * Initialize the search request.
     * @return bool
     */
    public function initialize() {
        return true;
    }

    /**
     * Index the current search result.
     *
     * @param array $fields
     * @param array $options
     *
     * @return bool
     */
    public function index(array $fields = array(), array $options = array()) {
        return true;
    }
    
    /**
     * Remove search result from the index.
     * @param $id
     * @return bool
     */
    public function removeIndex($id) {
        return true;
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
        $grouped = $this->modx->getOption('discuss.group_by_thread', '', 1);

        $c = $this->modx->newQuery('disPost');
        $c->innerJoin('disThread','Thread');
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disUser','Author');
        $c->where(array(
            "MATCH (disPost.title,disPost.message) AGAINST ({$this->modx->quote($string, PDO::PARAM_STR)} IN BOOLEAN MODE)",
            'Thread.private' => 0,
        ));
        if ($this->discuss->user->isLoggedIn) {
            $ignoreBoards = $this->discuss->user->get('ignore_boards');
            if (!empty($ignoreBoards)) {
                $c->where(array(
                    'Board.id:NOT IN' => explode(',',$ignoreBoards),
                ));
            }
        }
        if (!empty($conditions['board'])) {
            $c->where(array('Board.id:IN' => $conditions['board']));
        }
        if (!empty($conditions['author'])) {
            if (is_string($conditions['author'])) {
                $c->where(array('Author.username' => $conditions['author']));
            } else {
                $c->where(array('author' => $conditions['author']));
            }

        }
        if (!empty($conditions['class_key'])) {
            $c->where(array('Thread.class_key' => $conditions['class_key']));
            if (!empty($conditions['answered']) && !is_null($conditions['answered'])) {
                $c->where(array('Thread.answered' => $conditions['answered']));
            }
        }
        if (!empty($conditions['createdon'])) {
            $c->where(array("{$this->modx->escape('disPost')}.{$this->modx->escape('createdon')} {$conditions['createdon']}"));
        }

        $c->select(array(
            $this->modx->getSelectColumns('disPost','disPost', 'group_', array('thread')),
            'replies' => 'Thread.replies',
            'username' => 'Author.username',
            'board_name' => 'Board.name',
            "MATCH (disPost.title,disPost.message) AGAINST ({$this->modx->quote($string, PDO::PARAM_STR)} IN BOOLEAN MODE) AS score",
        ));
        $c->select($this->modx->getSelectColumns('disPost','disPost'));

        $c->sortby('score','DESC');
        $rowsFetched = (int)$this->modx->getOption('discuss.max_search_results', '', 500);
        if ($grouped) {
            $rowsFetched += (int)$this->modx->getOption('discuss.search_results_buffer', '', 200);
        }
        $c->limit($rowsFetched);

        $c->prepare();
        if (!$c->stmt->execute()) {
            $errorInfo= $c->stmt->errorInfo();
            $this->modx->log(xPDO::LOG_LEVEL_ERROR, "Error " . $c->stmt->errorCode() . " executing statement:\n" . $c->toSQL() . "\n" . print_r($errorInfo, true));
            return $response;
        }
        $threads = array();
        $i = 0; // used for thread grouping
        $skip = 0; // skip to start, if necessary
        $posts = array();
        if ($grouped) {
            $rows = $c->stmt->fetchAll(PDO::FETCH_ASSOC|PDO::FETCH_GROUP);
        } else {
            $rows = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        foreach($rows as $row) {
            if ($skip < $start) { // Scroll to start or skip thread if found already
                $skip++;
                continue;
            }
            if ($grouped){
                $row = $row[0];
            }
            xPDOObject::_loadCollectionInstance($this->modx, $posts, 'disPost', $c, $row, false, false);
            $i++;
            if ($i == $limit || $i == count($rows)) { // $limit results found; get total row count, closeCursor (just in case) and exit while loop
                $response['total'] = (count($rows) > (int)$this->modx->getOption('discuss.max_search_results', '', 500)) ? (int)$this->modx->getOption('discuss.max_search_results', '', 500) : count($rows);
                $c->stmt->closeCursor();
                break;
            }
        }

        foreach($posts as $post) {
            $postArray = $post->toArray('', true, true);
            $postArray['message'] = $post->getContent();
            $response['results'][] = $postArray;
        }
        return $response;
    }

    /**
     * Commit the search and close the connection.
     * @return void
     */
    public function commit() {}
}

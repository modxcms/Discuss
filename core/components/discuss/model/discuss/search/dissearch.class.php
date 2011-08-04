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
     * @return bool
     */
    public function index(array $fields = array()) {
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
        
        $c = $this->modx->newQuery('disPost');
        $c->innerJoin('disThread','Thread');
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disUser','Author');
        $c->where(array(
            'MATCH (disPost.title,disPost.message) AGAINST ("'.$string.'" IN BOOLEAN MODE)',
            'Thread.private' => 0,
        ));
        if ($this->discuss->isLoggedIn) {
            $ignoreBoards = $this->discuss->user->get('ignore_boards');
            if (!empty($ignoreBoards)) {
                $c->where(array(
                    'Board.id:NOT IN' => explode(',',$ignoreBoards),
                ));
            }
        }
        if (!empty($conditions['board'])) $c->where(array('disBoard.id' => $conditions['board']));
        if (!empty($conditions['user'])) $c->where(array('disPost.author' => $conditions['user']));
        $response['total'] = $this->modx->getCount('disPost',$c);
        $c->select($this->modx->getSelectColumns('disPost','disPost'));
        $c->select(array(
            'username' => 'Author.username',
            'board_name' => 'Board.name',
            'replies' => 'Thread.replies',
            'MATCH (disPost.title,disPost.message) AGAINST ("'.$string.'" IN BOOLEAN MODE) AS score',
        ));
        $c->groupby('disPost.thread');
        $c->sortby('score','ASC');
        $c->sortby('disPost.rank','ASC');
        $c->limit($limit,$start);
        $postObjects = $this->modx->getCollection('disPost',$c);

        if (!empty($postObjects)) {
            /** @var disPost $post */
            foreach ($postObjects as $post) {
                $postArray = $post->toArray();
                $postArray['message'] = $post->getContent();
                $response['results'][] = $postArray;
            }
        }
        return $response;
    }

    /**
     * Commit the search and close the connection.
     * @return void
     */
    public function commit() {}
}
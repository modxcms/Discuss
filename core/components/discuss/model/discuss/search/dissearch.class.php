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
 * @package discuss
 * @subpackage search
 */
class disSearch {
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        $this->config = array_merge($config,array(

        ));
        $this->initialize();
    }

    public function initialize() {
        return true;
    }
    public function index(array $fields = array()) {
        return true;
    }
    public function removeIndex($id) {
        return true;
    }

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
            'Author.username AS username',
            'Board.name AS board_name',
            'MATCH (disPost.title,disPost.message) AGAINST ("'.$string.'" IN BOOLEAN MODE) AS score',
        ));
        $c->groupby('disPost.thread');
        $c->sortby('score','ASC');
        $c->sortby('disPost.rank','ASC');
        $c->limit($limit,$start);
        $postObjects = $this->modx->getCollection('disPost',$c);

        if (!empty($postObjects)) {
            foreach ($postObjects as $post) {
                $postArray = $post->toArray();
                $postArray['message'] = $post->getContent();
                $response['results'][] = $postArray;
            }
        }
        return $response;
    }
}
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
require dirname(__FILE__).'/dissearch.class.php';
/**
 * @package discuss
 * @subpackage search
 * @extends disSearch
 */
class disSolrSearch extends disSearch {

    /**
     * An array of connection configuration options for the Solr client
     * 
     * @var array $_connectionOptions
     */
    private $_connectionOptions = array();
    private $_searchOptions = array();
    /**
     * The client API for the Solr instance
     * @var SolrClient $client
     */
    public $client;

    /**
     * Initialize the Solr search engine
     * @return void
     */
    public function initialize() {
        $this->_connectionOptions = array(
            'hostname' => $this->modx->getOption('discuss.solr.hostname',null,'127.0.0.1'),
            'port' => $this->modx->getOption('discuss.solr.port',null,'8080'),
            'path' => $this->modx->getOption('discuss.solr.path',null,''),
            'login' => $this->modx->getOption('discuss.solr.username',null,''),
            'password' => $this->modx->getOption('discuss.solr.password',null,''),
            'timeout' => $this->modx->getOption('discuss.solr.timeout',null,30),
            'secure' => $this->modx->getOption('discuss.solr.ssl',null,false),
            'ssl_cert' => $this->modx->getOption('discuss.solr.ssl_cert',null,''),
            'ssl_key' => $this->modx->getOption('discuss.solr.ssl_key',null,''),
            'ssl_keypassword' => $this->modx->getOption('discuss.solr.ssl_keypassword',null,''),
            'ssl_cainfo' => $this->modx->getOption('discuss.solr.ssl_cainfo',null,''),
            'ssl_capath' => $this->modx->getOption('discuss.solr.ssl_capath',null,''),
            'proxy_host' => $this->modx->getOption('discuss.solr.proxy_host',null,''),
            'proxy_port' => $this->modx->getOption('discuss.solr.proxy_port',null,''),
            'proxy_login' => $this->modx->getOption('discuss.solr.proxy_username',null,''),
            'proxy_password' => $this->modx->getOption('discuss.solr.proxy_password',null,''),
        );

        $this->_searchOptions = array(
            'requestHandler' => $this->modx->getOption('discuss.solr.requestHandler',null,''),
        );

        try {
            $this->client = new SolrClient($this->_connectionOptions);
        } catch (Exception $e) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,'Error connecting to Solr server: '.$e->getMessage());
        }
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
        /* sanitize string */
        $string = str_replace(array('!'),'',$string);

        /* @var SolrQuery $query */
        $query = new SolrQuery();
        $query->setQuery($string);
        $query->setStart($start);
        $query->setRows($limit);

        // turn board array into solr-compatible OR argument
        if(isset($conditions['board']) && is_array($conditions['board'])) {
            $c = array();
            foreach($conditions['board'] as $board) {
                $c[] = $board['id'];
            }
            $conditions['board'] = '(' . implode(' OR ', $c) . ')';
        }

        // allow for non-default Solr requestHandler
        if(isset($this->_searchOptions['requestHandler']) && !empty($this->_searchOptions['requestHandler'])) {
            $this->client->setServlet(SolrClient::SEARCH_SERVLET_TYPE, $this->_searchOptions['requestHandler']);
        } else {
            $query->addField('id')
                ->addField('title')
                ->addField('message')
                ->addField('thread')
                ->addField('board')
                ->addField('category')
                //->addField('category_name')
                ->addField('author')
                ->addField('username')
                ->addField('replies')
                ->addField('createdon')
                ->addField('board_name')
                ->addField('url')
                ->addField('private')
                //->addField('score');
            ;
        }

        foreach ($conditions as $k => $v) {
            $query->addFilterQuery($k.':'.$v);
        }

        $response = array(
            'total' => 0,
            'start' => $start,
            'limit' => $limit,
            'status' => 0,
            'query_time' => 0,
            'results' => array(),
        );
        try {
            $queryResponse = $this->client->query($query);
            $responseObject = $queryResponse->getResponse();
            if ($responseObject) {
                $response['total'] = $responseObject->response->numFound;
                $response['query_time'] = $responseObject->responseHeader->QTime;
                $response['status'] = $responseObject->responseHeader->status;
                $response['results'] = array();
                if (!empty($responseObject->response->docs)) {
                    foreach ($responseObject->response->docs as $doc) {
                        $d = array();
                        foreach ($doc as $k => $v) {
                            if ($k == 'createdon') {
                                $v = strftime($this->discuss->dateFormat,strtotime($v));
                            }
                            $d[$k] = $v;
                        }
                        $response['results'][] = $d;
                    }
                }
            }
        } catch (Exception $e) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,'Error running query on Solr server: '.$e->getMessage());
        }
        return $response;
    }

    /**
     * Index the current search result.
     *
     * @param array $fields
     * @return bool
     */
    public function index(array $fields = array(), array $options = array()) {
        $document = new SolrInputDocument();
        $document->addField('id',$fields['id']);
        $document->addField('private',$fields['private']);
        $document->addField('username',$fields['username']);
        $document->addField('createdon',''.strftime('%Y-%m-%dT%H:%M:%SZ',strtotime($fields['createdon'])));
        $document->addField('board',$fields['board']);
        $document->addField('author',$fields['author']);
        $document->addField('thread',$fields['thread']);

        $document->addField('title',$fields['title'],2);
        $document->addField('message',$fields['message'],2);
        $document->addField('url',$fields['url']);

        if (!empty($fields['replies'])) {
            $document->addField('replies',$fields['replies']);
        }
        if (!empty($fields['board_name'])) {
            $document->addField('board_name',$fields['board_name']);
        }
        if (!empty($fields['category'])) {
            $document->addField('category',$fields['category']);
        }
        if (!empty($fields['category_name'])) {
            $document->addField('category_name',$fields['category_name']);
        }
        if (!empty($fields['answered_question'])) {
            $document->addField('answered_question',$fields['answered_question']);
        }

        $response = false;
        try {
            $response = $this->client->addDocument($document);
            if(isset($options['commit']) && $options['commit'] !== false) {
                $this->commit();
            }
        } catch (Exception $e) {
            $response = $e->getMessage();
        }
        return $response;
    }

    /**
     * Remove search result from the index.
     * @param $id
     * @return bool
     */
    public function removeIndex($id) {
        $this->client->deleteById($id);
        $this->commit();
    }

    /**
     * Commit the search and close the connection.
     * @return void
     */
    public function commit() {
        try {
            $this->client->commit();
        } catch (Exception $e) {
        }
    }
}
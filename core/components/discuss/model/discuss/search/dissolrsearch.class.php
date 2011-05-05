<?php
require dirname(__FILE__).'/dissearch.class.php';
/**
 * @package discuss
 * @subpackage search
 */
class disSolrSearch extends disSearch {
    private $_connectionOptions = array();
    public $client;

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

        try {
            $this->client = new SolrClient($this->_connectionOptions);
        } catch (Exception $e) {
            $this->modx->log(xPDO::LOG_LEVEL_ERROR,'Error connecting to Solr server: '.$e->getMessage());
        }
    }

    public function run($string,$limit = 10,$start = 0,array $conditions = array()) {
        $query = new SolrQuery();
        $query->setQuery($string);
        $query->setStart($start);
        $query->setRows($limit);
        $query->addField('id')
              ->addField('title')
              ->addField('message')
              ->addField('thread')
              ->addField('board')
              ->addField('category')
              ->addField('category_name')
              ->addField('author')
              ->addField('username')
              ->addField('createdon')
              ->addField('board_name')
              ->addField('url')
              ->addField('score');

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

    public function index($fields = array()) {
        $document = new SolrInputDocument();
        $document->addField('id',$fields['id']);
        $document->addField('thread',$fields['thread']);
        $document->addField('title',$fields['title'],2);
        $document->addField('message',$fields['message'],2);
        $document->addField('board',$fields['board']);
        if (!empty($fields['url'])) {
            $document->addField('url',$fields['url']);
        }
        if (!empty($fields['board_name'])) {
            $document->addField('board_name',$fields['board_name']);
        }
        if (!empty($fields['username'])) {
            $document->addField('username',$fields['username']);
        }
        $document->addField('author',$fields['author']);
        if (!empty($fields['createdon'])) {
            $document->addField('createdon',''.strftime('%Y-%m-%dT%H:%M:%SZ',strtotime($fields['createdon'])));
        }

        $response = false;
        try {
            $response = $this->client->addDocument($document);
        } catch (Exception $e) {
        }
        $this->commit();
        return $response;
    }

    public function removeIndex($id) {
        $this->client->deleteById($id);
        $this->commit();
    }

    public function commit() {
        try {
            $this->client->commit();
        } catch (Exception $e) {
        }
    }
}
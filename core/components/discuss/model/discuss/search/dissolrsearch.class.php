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
        );
        $this->client = new SolrClient($this->_connectionOptions);
    }

    public function run($string,$limit = 10,$start = 0) {

        $query = new SolrQuery();
        $query->setQuery($string);
        $query->setStart($start);
        $query->setRows($limit);
        $query->addField('id')
              ->addField('title')
              ->addField('message')
              ->addField('thread')
              ->addField('board')
              ->addField('author')
              ->addField('username')
              ->addField('createdon')
              ->addField('board_name')
              ->addField('url')
              ->addField('score');

        $queryResponse = $this->client->query($query);
        $responseObject = $queryResponse->getResponse();

        $response = array();
        $response['total'] = $responseObject->response->numFound;
        $response['start'] = $responseObject->response->start;
        $response['query_time'] = $responseObject->responseHeader->QTime;
        $response['status'] = $responseObject->responseHeader->status;
        $response['results'] = array();
        if (!empty($responseObject->response->docs)) {
            foreach ($responseObject->response->docs as $doc) {
                $d = array();
                $pns = $doc->getPropertyNames();
                foreach ($pns as $k) {
                    foreach ($doc as $k => $v) {
                        if ($k == 'createdon') {
                            $v = strftime($this->discuss->dateFormat,strtotime($v));
                        }
                        $d[$k] = $v;
                    }
                }
                $response['results'][] = $d;
            }
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

        $response = $this->client->addDocument($document);
        $this->commit();
        return $response;
    }

    public function removeIndex($id) {
        $this->client->deleteById($id);
        $this->commit();
    }

    public function commit() {
        $this->client->commit();
    }
}
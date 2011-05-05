<?php
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

    public function run($string,$limit = 10,$start = 0) {
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
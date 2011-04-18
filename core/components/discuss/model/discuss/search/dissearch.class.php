<?php
class disSearch {
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        $this->config = array_merge($config,array(

        ));
    }

    public function run($string) {

        $c = $this->modx->newQuery('disPost');
        $c->innerJoin('disThread','Thread');
        $c->innerJoin('disBoard','Board');
        $c->innerJoin('disUser','Author');
        $c->innerJoin('disPostClosure','PostClosure','PostClosure.descendant = disPost.id AND PostClosure.ancestor != 0');
        $c->where(array(
            'MATCH (disPost.title,disPost.message) AGAINST ("'.$string.'" IN BOOLEAN MODE)',
        ));
        if ($this->discuss->isLoggedIn) {
            $ignoreBoards = $this->discuss->user->get('ignore_boards');
            if (!empty($ignoreBoards)) {
                $c->where(array(
                    'Board.id:NOT IN' => explode(',',$ignoreBoards),
                ));
            }
        }
        $c->select($this->modx->getSelectColumns('disPost','disPost'));
        $c->select(array(
            'username' => 'Author.username',
            'board_name' => 'Board.name',
            'MATCH (disPost.title,disPost.message) AGAINST ("'.$string.'" IN BOOLEAN MODE) AS score',
        ));
        $c->sortby('score','ASC');
        $c->sortby('disPost.rank','ASC');
        $c->limit(10);
        $postObjects = $this->modx->getCollection('disPost',$c);

        $posts = array();
        foreach ($postObjects as $post) {
            $postArray = $post->toArray();
            $postArray['content'] = $post->getContent();
            $posts[] = $postArray;
        }
        return $posts;
    }
}
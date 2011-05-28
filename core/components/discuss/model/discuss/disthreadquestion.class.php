<?php
/**
 * @package discuss
 */
class disThreadQuestion extends disThread {

    /**
     * Fetch all posts for this question, sorting the correct answer at the top
     *
     * @param mixed $post A reference to a disPost or ID of disPost to start the posts from
     * @param array $options An array of options for sorting, limiting and display
     * @return array
     */
    public function fetchPosts($post = false,array $options = array()) {
        $response = array();
        $c = $this->xpdo->newQuery('disPost');
        $c->innerJoin('disThread','Thread');
        $c->where(array(
            'thread' => $this->get('id'),
        ));
        $cc = clone $c;
        $response['total'] = $this->xpdo->getCount('disPost',$cc);
        $c->select($this->xpdo->getSelectColumns('disPost','disPost'));
        $c->select(array(
            'IF(Thread.post_first = disPost.id,1,0) AS thread_first',
            'IF(Thread.post_answer = disPost.id,1,0) AS thread_answer',
        ));

        $flat = $this->xpdo->getOption('flat',$options,true);
        $limit = $this->xpdo->getOption('limit',$options,(int)$this->xpdo->getOption('discuss.post_per_page',$options, 10));
        $start = $this->xpdo->getOption('start',$options,0);
        if ($flat) {
            $sortBy = $this->xpdo->getOption('sortBy',$options,'createdon');
            $sortDir = $this->xpdo->getOption('sortDir',$options,'ASC');
            $c->sortby($this->xpdo->escape('thread_first'),'DESC');
            $c->sortby($this->xpdo->escape('thread_answer'),'DESC');
            $c->sortby($this->xpdo->getSelectColumns('disPost','disPost','',array($sortBy)),$sortDir);
            if (empty($_REQUEST['print'])) {
                $c->limit($limit, $start);
            }
        } else {
            $c->sortby($this->xpdo->escape('thread_first'),'DESC');
            $c->sortby($this->xpdo->escape('thread_answer'),'DESC');
            $c->sortby($this->xpdo->getSelectColumns('disPost','disPost','',array('rank')),'ASC');
        }

        if (!empty($post)) {
            if (!is_object($post)) {
                $post = $this->xpdo->getObject('disPost',$post);
            }
            if ($post) {
                $c->where(array(
                    'disPost.createdon:>=' => $post->get('createdon'),
                ));
            }
        }

        $c->bindGraph('{"Author":{},"EditedBy":{}}');
        $response['results'] = $this->xpdo->getCollectionGraph('disPost','{"Author":{},"EditedBy":{}}',$c);
        return $response;
    }

    /**
     * Check to see if active user can mark a post as the answer for this thread
     * 
     * @return bool
     */
    public function canMarkAsAnswer() {
        $isModerator = $this->isModerator($this->xpdo->discuss->user->get('id'));
        $isAdmin = $this->xpdo->discuss->user->isAdmin();
        return $this->xpdo->discuss->user->isLoggedIn && ($isModerator || $isAdmin || $this->get('author_first') == $this->xpdo->discuss->user->get('id'));
    }

    /**
     * Mark a Post as an answer for this thread
     *
     * @param int $postId
     * @return bool
     */
    public function markAsAnswer($postId) {
        $this->set('post_answer',$postId);
        return $this->save();
    }

    /**
     * Mark this thread as unsolved
     *
     * @return bool
     */
    public function unmarkAsAnswer() {
        $this->set('post_answer',0);
        return $this->save();
    }

    /**
     * Overrides xPDOObject::get to provide Solved tag on title if thread is solved
     * 
     * @param string $k
     * @param string $format
     * @param string $formatTemplate
     * @return mixed|string
     */
    public function get($k,$format = '',$formatTemplate = '') {
        $v = parent::get($k,$format,$formatTemplate);

        if ($k == 'title' && $this->xpdo->lexicon) {
            $postAnswer = $this->get('post_answer');
            if (!empty($postAnswer)) {
                $v .= ' ['.$this->xpdo->lexicon('discuss.solved').']';
            }
        }
        return $v;
    }
}
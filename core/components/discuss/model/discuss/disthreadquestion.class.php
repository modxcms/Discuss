<?php
/**
 * @package discuss
 */
class disThreadQuestion extends disThread {
    /**
     * Whether or not the active user can mark this thread as an answer
     * @var boolean $canMarkAsAnswer
     */
    public $canMarkAsAnswer;

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
        ));

        $flat = $this->xpdo->getOption('flat',$options,true);
        $limit = $this->xpdo->getOption('limit',$options,(int)$this->xpdo->getOption('discuss.post_per_page',$options, 10));
        $start = $this->xpdo->getOption('start',$options,0);
        if ($flat) {
            $sortBy = $this->xpdo->getOption('sortBy',$options,'createdon');
            $sortDir = $this->xpdo->getOption('sortDir',$options,'ASC');
            $c->sortby($this->xpdo->escape('thread_first'),'DESC');
            $c->sortby($this->xpdo->escape('answer'),'DESC');
            $c->sortby($this->xpdo->getSelectColumns('disPost','disPost','',array($sortBy)),$sortDir);
            if (empty($_REQUEST['print'])) {
                $c->limit($limit, $start);
            }
        } else {
            $c->sortby($this->xpdo->escape('thread_first'),'DESC');
            $c->sortby($this->xpdo->escape('answer'),'DESC');
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
        if (!isset($this->canMarkAsAnswer)) {
            $isModerator = $this->isModerator($this->xpdo->discuss->user->get('id'));
            $isAdmin = $this->xpdo->discuss->user->isAdmin();
            $this->canMarkAsAnswer = $this->xpdo->discuss->user->isLoggedIn && ($isModerator || $isAdmin || $this->get('author_first') == $this->xpdo->discuss->user->get('id'));
        }
        return $this->canMarkAsAnswer;
    }

    /**
     * Mark a Post as an answer for this thread
     *
     * @param int $postId
     * @return bool
     */
    public function markAsAnswer($postId) {
        $marked = false;
        $post = $this->xpdo->getObject('disPost',$postId);
        if ($post) {
            $post->set('answer',true);

            /* fire before mark as answer event */
            $rs = $this->xpdo->invokeEvent('OnDiscussBeforeMarkAsAnswer',array(
                'post' => &$post,
                'thread' => &$this,
            ));
            $canSave = $this->xpdo->discuss->getEventResult($rs);
            if (!empty($canSave)) {
                return $this->xpdo->error->failure($canSave);
            }

            if ($post->save()) {
                $this->set('answered',true);
                $marked = $this->save();
                if ($marked) {
                    $rs = $this->xpdo->invokeEvent('OnDiscussMarkAsAnswer',array(
                        'post' => &$post,
                        'thread' => &$this,
                    ));
                }
            }
        }
        return $marked;
    }

    /**
     * Mark this thread as unsolved
     *
     * @param int $postId
     * @return bool
     */
    public function unmarkAsAnswer($postId) {
        $marked = false;
        $post = $this->xpdo->getObject('disPost',$postId);
        if ($post) {
            $post->set('answer',false);

            /* fire before mark as answer event */
            $rs = $this->xpdo->invokeEvent('OnDiscussBeforeUnmarkAsAnswer',array(
                'post' => &$post,
                'thread' => &$this,
            ));
            $canSave = $this->xpdo->discuss->getEventResult($rs);
            if (!empty($canSave)) {
                return $this->xpdo->error->failure($canSave);
            }

            if ($post->save()) {
                $marked = true;
                $answersLeft = $this->xpdo->getCount('disPost',array(
                    'thread' => $this->get('id'),
                    'answer' => true,
                ));
                if ($answersLeft <= 0) {
                    $this->set('answered',false);
                    $this->save();
                }

                if ($marked) {
                    $rs = $this->xpdo->invokeEvent('OnDiscussUnmarkAsAnswer',array(
                        'post' => &$post,
                        'thread' => &$this,
                    ));
                }
            }
        }
        return $marked;
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
            $answered = $this->get('answered');
            if (!empty($answered)) {
                $v .= ' ['.$this->xpdo->lexicon('discuss.solved').']';
            }
        }
        return $v;
    }

    /**
     * Prepares the thread view, useful for extra processing when using derivative thread types
     * 
     * @param array $postArray
     * @return array
     */
    public function prepareThreadView(array $postArray) {
        if ($this->canMarkAsAnswer() && $postArray['id'] != $this->get('post_first')) {
            if (!empty($postArray['answer'])) {
                $postArray['action_mark_as_answer'] = $this->xpdo->discuss->getChunk('disActionLink',array(
                    'url' => $this->getUrl(false,array('unanswer' => $postArray['id'])),
                    'text' => $this->xpdo->lexicon('discuss.unmark_as_answer'),
                    'class' => 'dis-report-link',
                    'id' => '',
                    'attributes' => '',
                ));
                $postArray['actions'][] = $postArray['action_mark_as_answer'];
            } else {
                $postArray['action_mark_as_answer'] = $this->xpdo->discuss->getChunk('disActionLink',array(
                    'url' => $this->getUrl(false,array('answer' => $postArray['id'])),
                    'text' => $this->xpdo->lexicon('discuss.mark_as_answer'),
                    'class' => 'dis-report-link',
                    'id' => '',
                    'attributes' => '',
                ));
                $postArray['actions'][] = $postArray['action_mark_as_answer'];
            }
        }

        if (!empty($postArray['answer'])) {
            $postArray['class'][] = 'dis-post-answer';
            $postArray['title'] .= ' ('.$this->xpdo->lexicon('discuss.best_answer').')';
        }
        return $postArray;
    }

    /**
     * Handle actions on thread view
     * 
     * @param array $scriptProperties
     * @return boolean
     */
    public function handleThreadViewActions(array $scriptProperties) {
        $success = parent::handleThreadViewActions($scriptProperties);
        
        if (!empty($scriptProperties['answer']) && $this->canMarkAsAnswer()) {
            if ($this->markAsAnswer($scriptProperties['answer'])) {
                $this->xpdo->sendRedirect($this->getUrl());
            }
        }
        if (!empty($scriptProperties['unanswer']) && $this->canMarkAsAnswer()) {
            if ($this->unmarkAsAnswer($scriptProperties['unanswer'])) {
                $this->xpdo->sendRedirect($this->getUrl());
            }
        }

        return $success;
    }
}
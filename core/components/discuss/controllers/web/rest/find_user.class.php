<?php

/**
 * Displays posts for a Board in RSS format
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussRestFindUserController extends DiscussController {
    public $useWrapper = false;

    /**
     * @return string
     */
    public function process() {
        @header('Content-type: application/json');
        $search = trim($this->getProperty('term', ''));

        if (empty($search) || strlen($search) < 2) {
            $this->setPlaceholder('content', $this->modx->toJSON(array(
                'success' => false,
                'message' => $this->modx->lexicon('discuss.error_no_term_passed'),
                'data' => array(),
            )));
        }

        $c = $this->modx->newQuery('disUser');
        $c->where(array(
            'username:LIKE' => "%{$search}%",
            array(
                'OR:display_name:LIKE' => "%{$search}%",
                'AND:use_display_name:=' => true,
            )
        ));

        $c->select($this->modx->getSelectColumns('disUser','disUser','',array('id','username','display_name','use_display_name')));
        $c->sortby('username','ASC');
        $c->limit(20);

        $results = array();
        /* @var disUser $user */
        foreach ($this->modx->getIterator('disUser', $c) as $user) {
            $data = $user->toArray();
            $name = ($data['use_display_name'] && !empty($data['display_name'])) ? $data['username'] . ' (' . $data['display_name'] .')' : $data['username'];
            $results[] = array(
                'label' => $name,
                'value' => $data['username']
            );
        }

        $this->setPlaceholder('content',
            $this->modx->toJSON(array(
                'success' => (!empty($data)),
                'data' => $results
            ))
        );
    }

    /**
     * @return string
     */
    public function getSessionPlace() {
        return '';
    }

    /**
     * @return string
     */
    public function getPageTitle() {
        return 'Resting';
    }
}

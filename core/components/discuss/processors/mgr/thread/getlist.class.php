<?php
class disThreadGetListProcessor extends modProcessor {
    public function getLanguageTopics() {
        return array('discuss:default');
    }

    public function initialize() {
        $this->setDefaultProperties(array(
            'sort' => 'title',
            'dir' => 'DESC',
            'start' => 0,
            'limit' => 10,
            'query' => '',
            'dateFormat' => '%b %d, %Y %I:%M %p',
        ));
        return true;
    }
    public function process() {
        $data = $this->getData();
        
        /* iterate */
        $list = array();
        /** @var disThread $thread */
        foreach ($data['results'] as $thread) {
            $threadArray = $this->prepareThread($thread);
            if (!empty($threadArray)) {
                $list[] = $threadArray;
            }
        }
        return $this->outputArray($list,$data['total']);
    }

    public function prepareThread(disThread $thread) {
        $threadArray = $thread->toArray();
        if (!empty($threadArray['post_last_on'])) {
            $threadArray['post_last_on'] = strftime($this->getProperty('dateFormat'),strtotime($threadArray['post_last_on']));
        } else {
            $threadArray['post_last_on'] = '';
        }
        return $threadArray;
    }

    public function getData() {
        $data = array();
        $isLimit = (int)$this->getProperty('limit') > 0;

        /* build query */
        $c = $this->modx->newQuery('disThread');
        $c->innerJoin('disUser','FirstAuthor');

        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'FirstAuthor.username:LIKE' => '%'.$query.'%',
                'OR:disThread.title:LIKE' => '%'.$query.'%',
            ));
        }
        $data['total'] = $this->modx->getCount('disThread',$c);
        $c->select($this->modx->getSelectColumns('disThread','disThread'));
        $c->select($this->modx->getSelectColumns('disUser','FirstAuthor','',array('username')));
        if ($isLimit) {
            $c->limit($this->getProperty('limit'),$this->getProperty('start'));
        }
        $data['results'] = $this->modx->getCollection('disThread', $c);
        return $data;
    }
}
return 'disThreadGetListProcessor';
<?php
/**
 * Gets a list of disBoard objects.
 */
class disBoardGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'disBoard';
    public $languageTopics = array('discuss:default');
    public $defaultSortField = 'name';
    public $defaultSortDirection = 'ASC';

    /**
     * @param xPDOQuery $c
     * @return \xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $c->innerJoin('disCategory','Category');
        $c->leftJoin('disBoard','Parent');
        $c->select($this->modx->getSelectColumns('disBoard','disBoard','',array('id','name')));
        $c->select($this->modx->getSelectColumns('disCategory','Category','category_',array('id','name')));
        $c->select($this->modx->getSelectColumns('disBoard','Parent','parent_',array('id','name')));

        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'disBoard.name:LIKE' => "%{$query}%"
            ));
        }
        return $c;
    }


    /**
     * Transform the xPDOObject derivative to an array;
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $row = $object->toArray('', false, true);
        if (!empty($row['parent_name'])) {
            $row['category_name'] .= ' &raquo; ' . $row['parent_name'];
        }

        return $row;
    }
}
return 'disBoardGetListProcessor';

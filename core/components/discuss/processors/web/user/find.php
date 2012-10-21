<?php
/**
 * @var modX $modx
 * @var array $scriptProperties
 */
$search = trim($modx->getOption('term', $scriptProperties, ''));

if (empty($search)) {
    return $modx->toJSON(array(
        'success' => false,
        'message' => $modx->lexicon('discuss.error_no_term_passed'),
        'data' => array(),
    ));
}

$c = $modx->newQuery('disUser');
$c->where(array(
    'username:LIKE' => "%{$search}%",
    array(
        'OR:display_name:LIKE' => "%{$search}%",
        'AND:use_display_name:=' => true,
    )
));

$c->select($modx->getSelectColumns('disUser','disUser','',array('id','username','display_name','use_display_name')));
$c->sortby('username','ASC');
$c->limit(20);

$results = array();
/* @var disUser $user */
foreach ($modx->getIterator('disUser', $c) as $user) {
    $data = $user->toArray();
    $name = ($data['use_display_name'] && !empty($data['display_name'])) ? $data['username'] . ' (' . $data['display_name'] .')' : $data['username'];
    $results[] = array(
        'label' => $name,
        'value' => $data['username']
    );
}

return $modx->toJSON(array(
    'success' => (!empty($data)),
    'data' => $results
));


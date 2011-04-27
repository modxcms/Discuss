<?php
/**
 * Handle updating of profile
 */
$discuss =& $modx->discuss;
$modx->lexicon->load('discuss:user');

$disUser = $modx->getObject('disUser',array(
    'user' => $modx->user->get('id'),
));
if (!$disUser) return true;

unset($fields['id']);
unset($fields['user']);
$disUser->fromArray($fields);
if (!empty($fields['signature'])) {
    $fields['signature'] = str_replace(array('&#91;','&#93;'),array('[',']'),$fields['signature']);
    $disUser->set('signature',$fields['signature']);
}
if (!$disUser->save()) {
    $modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not sync profile information during UpdateProfile snippet posthook: '.print_r($fields,true));
}

$forumsResourceId = $modx->getOption('discuss.forums_resource_id',null,0);
/*
if (!empty($_REQUEST['discuss']) && !empty($forumsResourceId)) {
    $url = $modx->makeUrl($forumsResourceId,'','','full').'user/?user='.$disUser->get('id');
    $modx->sendRedirect($url);
}*/
return true;

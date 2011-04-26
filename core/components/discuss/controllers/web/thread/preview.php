<?php
$placeholders = array();
$postArray = $scriptProperties;
$postArray['action_remove'] = '';
$postArray['action_modify'] = '';
$postArray['action_quote'] = '';
$postArray['action_reply'] = '';

$author = $discuss->user->toArray();
foreach ($author as $k => $v) {
    $postArray['author.'.$k] = $v;
}

$post = $modx->newObject('disPost');
$post->fromArray($postArray);
$postArray = $post->toArray();
/* handle MODX tags */
$post->set('message',str_replace(array('[[',']]'),array('&#91;&#91;','&#93;&#93;'),$postArray['message']));

/* get formatted content */
$postArray['message'] = $post->getContent();
$postArray['createdon'] = strftime($discuss->dateFormat,time());

$output = $discuss->getChunk('post/disPostPreview',$postArray);
$placeholders = array('post' => $output);
return $placeholders;
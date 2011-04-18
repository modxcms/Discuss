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
$postArray['message'] = $post->getContent();
$postArray['createdon'] = strftime($discuss->dateFormat,time());
if (!empty($postArray['author.signature'])) {
   // $postArray['author.signature'] = $post->parseBBCode($postArray['author.signature']);
 //   $postArray['author.signature'] = $post->stripBBCode($postArray['author.signature']);
}

$output = $discuss->getChunk('post/disPostPreview',$postArray);
$placeholders = array('post' => $output);
return $placeholders;
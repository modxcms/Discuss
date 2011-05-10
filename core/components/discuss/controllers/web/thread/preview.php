<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
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
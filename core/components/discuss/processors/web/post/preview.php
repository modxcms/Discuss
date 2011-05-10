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
/**
 * Show a preview of the post before it is made
 *
 * @package discuss
 * @subpackage processors
 */
$post = $modx->newObject('disPost');
$post->fromArray($_POST);
$post->set('author',$modx->user->get('id'));
$post->set('parent',0);
$post->set('createdon',strftime('%Y-%m-%d %H:%M:%S'));
$post->set('ip',$_SERVER['REMOTE_ADDR']);

/* now output html back to browser */
$post->set('username',$modx->user->get('username'));

$postArray = $post->toArray();
$postArray['content'] = $post->getContent();

$o = $discuss->getChunk('disPost',$postArray);

return $modx->error->success($o,$post);
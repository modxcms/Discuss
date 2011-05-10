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
 * @package discuss
 */
$modx->lexicon->load('discuss:post');

if (empty($_POST['post'])) return $modx->error->failure($modx->lexicon('discuss.post_err_ns'));
$post = $modx->getObject('disPost',$_POST['post']);
if ($post == null) return $modx->error->failure($modx->lexicon('discuss.post_err_nf'));

$board = $post->getOne('Board');

/* fire pre-remove event */
$rs = $modx->invokeEvent('OnDiscussBeforePostRemove',array(
    'post' => &$post,
    'board' => &$board,
    'mode' => 'remove',
));
$canRemove = $discuss->getEventResult($rs);
if (!empty($canRemove)) {
    return $modx->error->failure($canSave);
}

if ($post->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.post_err_remove'));
}

$board->set('num_posts',$board->get('num_posts')-1);
$board->set('total_posts',$board->get('total_posts')-1);
$board->save();

$modx->invokeEvent('OnDiscussPostRemove',array(
    'post' => &$post,
    'board' => &$board,
    'mode' => 'remove',
));


return $modx->error->success();
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
 * Display form to post a new thread
 *
 * @package discuss
 */
$discuss->setSessionPlace('newmessage');
$discuss->setPageTitle($modx->lexicon('discuss.message_new'));
$placeholders = array();

/* get breadcrumb trail */
$trail = array();
$trail[] = array(
    'url' => $discuss->request->makeUrl(),
    'text' => $modx->getOption('discuss.forum_title'),
);
$trail[] = array('text' => $modx->lexicon('discuss.messages'),'url' => $discuss->request->makeUrl().'messages');
$trail[] = array('text' => $modx->lexicon('discuss.message_new'),'active' => true);

$trail = $discuss->hooks->load('breadcrumbs',array_merge($scriptProperties,array(
    'items' => &$trail,
)));
$placeholders['trail'] = $trail;

/* setup defaults */
if (empty($_POST)) {
    $participants = array($modx->user->get('username'));
    if (!empty($scriptProperties['user'])) {
        $ps = explode(',',$scriptProperties['user']);
        $participants = array_merge($ps,$participants);
    }
    asort($participants);
    $placeholders['participants_usernames'] = implode(',',array_unique($participants));
}
$placeholders['buttons'] = $discuss->getChunk('disPostButtons',array('buttons_url' => $discuss->config['imagesUrl'].'buttons/'));

/* set max attachment limit */
$placeholders['max_attachments'] = $modx->getOption('discuss.attachments_max_per_post',null,5);

/* load theme options */
$discuss->config['max_attachments'] = $placeholders['max_attachments'];

/* output form to browser */
$modx->setPlaceholder('discuss.error_panel',$discuss->getChunk('Error'));

$modx->toPlaceholders($placeholders,'fi');
return $placeholders;
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
 * Remove Post page
 *
 * @package discuss
 */
/* get thread root */
$post = $modx->getObject('disPost',$scriptProperties['post']);
if (empty($post)) $discuss->sendErrorPage();
$thread = $modx->call('disThread', 'fetch', array(&$modx,$post->get('thread')));
if (empty($thread)) $discuss->sendErrorPage();

$discuss->setPageTitle($modx->lexicon('discuss.report_to_mod',array('title' => $thread->get('title'))));

/* get breadcrumb trail */
$placeholders = $post->toArray();
$placeholders['url'] = $post->getUrl();
$placeholders['trail'] = $thread->buildBreadcrumbs();

/* process form */
if (!empty($scriptProperties['report-thread'])) {
    $author = $post->getOne('Author');

    /* setup default properties */
    $subject = $modx->getOption('subject',$scriptProperties,$modx->getOption('discuss.email_reported_post_subject',null,'Reported Post: [[+title]]'));
    $subject = str_replace('[[+title]]',$post->get('title'),$subject);
    $tpl = $modx->getOption('tpl',$scriptProperties,$modx->getOption('discuss.email_reported_post_chunk',null,'emails/disReportedEmail'));

    /* build post url */
    $url = $modx->getOption('site_url',null,MODX_SITE_URL).$post->getUrl();

    /* setup email properties */
    $emailProperties = array_merge($scriptProperties,$post->toArray());
    $emailProperties['tpl'] = $tpl;
    $emailProperties['title'] = $post->get('title');
    if ($author) {
        $emailProperties['author'] = $author->get('title');
    }
    $emailProperties['reporter'] = $discuss->user->get('username');
    $emailProperties['url'] = $url;
    $emailProperties['forum_title'] = $modx->getOption('discuss.forum_title');
    $emailProperties['message'] = nl2br(strip_tags($scriptProperties['message']));

    /* send reported email */
    $moderators = $thread->getModerators();
    foreach ($moderators as $moderator) {
        $sent = $discuss->sendEmail($moderator->get('email'),$moderator->get('username'),$subject,$emailProperties);
    }
    unset($emailProperties);

    /* redirect to thread */
    $modx->sendRedirect($url);
}

/* output */
$modx->setPlaceholder('discuss.thread',$thread->get('title'));
return $placeholders;

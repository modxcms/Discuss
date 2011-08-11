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
 * Report Post page
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussPostReportController extends DiscussController {
    /**
     * @var disThread $thread
     */
    public $thread;
    /**
     * @var disPost $this->post
     */
    public $post;
    
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.report_to_mod',array('title' => $this->thread->get('title')));
    }
    public function getSessionPlace() {
        return '';
    }

    public function initialize() {
        $this->post = $this->modx->getObject('disPost',$this->getProperty('post',false));
        if (empty($this->post)) $this->discuss->sendErrorPage();
        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$this->post->get('thread')));
        if (empty($this->thread)) $this->discuss->sendErrorPage();

        /* ensure user can report this post */
        if (!$this->post->canReport()) {
            $this->modx->sendRedirect($this->thread->getUrl());
        }
    }

    public function process() {
        /* get breadcrumb trail */
        $this->setPlaceholders($this->post->toArray());
        $this->setPlaceholder('url',$this->post->getUrl());

        /* output */
        $this->modx->setPlaceholder('discuss.thread',$this->thread->get('title'));
    }

    public function getBreadcrumbs() {
        return $this->thread->buildBreadcrumbs(array(),$this->options['showTitleInBreadcrumbs']);
    }


    public function handleActions() {
        if (!empty($scriptProperties['report-thread'])) {
            $this->report();
        }
    }

    /**
     * Send a spam report via email
     * @return void
     */
    public function report() {
        $author = $this->post->getOne('Author');

        /* setup default properties */
        $subject = $this->modx->getOption('subject',$this->scriptProperties,$this->modx->getOption('discuss.email_reported_post_subject',null,'Reported Post: [[+title]]'));
        $subject = str_replace('[[+title]]',$this->post->get('title'),$subject);
        $tpl = $this->modx->getOption('tpl',$this->scriptProperties,$this->modx->getOption('discuss.email_reported_post_chunk',null,'emails/disReportedEmail'));

        /* build post url */
        $url = $this->modx->getOption('site_url',null,MODX_SITE_URL).$this->post->getUrl();

        /* setup email properties */
        $emailProperties = array_merge($this->scriptProperties,$this->post->toArray());
        $emailProperties['tpl'] = $tpl;
        $emailProperties['title'] = $this->post->get('title');
        if ($author) {
            $emailProperties['author'] = $author->get('title');
        }
        $emailProperties['reporter'] = $this->discuss->user->get('username');
        $emailProperties['url'] = $url;
        $emailProperties['forum_title'] = $this->modx->getOption('discuss.forum_title');
        $emailProperties['message'] = nl2br(strip_tags($this->scriptProperties['message']));

        /* send reported email */
        $moderators = $this->thread->getModerators();
        /** @var disUser $moderator */
        foreach ($moderators as $moderator) {
            $sent = $this->discuss->sendEmail($moderator->get('email'),$moderator->get('username'),$subject,$emailProperties);
        }
        unset($emailProperties);

        $this->discuss->logActivity('post_report',$this->post->toArray(),$this->post->getUrl());

        /* redirect to thread */
        $this->modx->sendRedirect($url);
    }
}

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
 * Get all new replies to posts authored by user
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadNewRepliesToPostsController extends DiscussController {
    /** @var array $threads */
    public $threads = array();
    
    public function initialize() {

    }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.new_replies_to_posts').' ('.number_format($this->threads['total']).')';
    }
    public function getSessionPlace() {
        return 'thread/new_replies_to_posts::'.$this->getProperty('page',1);
    }

    public function getDefaultOptions() {
        return array(
            'tpl' => 'post/disThreadLi',
            'dateFormat' => $this->discuss->dateFormat,
            'rowSeparator' => "\n",
        );
    }

    public function process() {
        /* setup default properties */
        $limit = $this->getProperty('limit',$this->modx->getOption('discuss.threads_per_page',null,20),'empty');
        $limit = !empty($this->scriptProperties['limit']) ? $this->scriptProperties['limit'] : $this->modx->getOption('discuss.threads_per_page',null,20);
        $page = $this->getProperty('page',1);
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;

        $sortBy = $this->getProperty('sortBy','post_last_on');
        $sortDir = $this->getProperty('sortDir','DESC');

        /* get new replies threads */
        $this->threads = $this->modx->call('disThread','fetchNewReplies',array(&$this->modx,$sortBy,$sortDir,$limit,$start));
        $this->threads['limit'] = $limit;
        $this->threads['start'] = $start;
        
        $canViewProfiles = $this->modx->hasPermission('discuss.view_profiles');
        $hotThreadThreshold = $this->modx->getOption('discuss.hot_thread_threshold',null,10);
        $enableSticky = $this->modx->getOption('discuss.enable_sticky',null,true);
        $enableHot = $this->modx->getOption('discuss.enable_hot',null,true);
        $list = array();
        /** @var disThread $thread */
        foreach ($this->threads['results'] as $thread) {
            $thread->calcLastPostPage();
            $thread->getUrl();
            $threadArray = $thread->toArray();
            $threadArray['createdon'] = strftime($this->getOption('dateFormat',$this->discuss->dateFormat),strtotime($threadArray['createdon']));
            $threadArray['icons'] = '';

            /* set css class */
            $class = array('dis-board-li','board-post');
            if ($enableHot) {
                $threshold = $hotThreadThreshold;
                if ($this->discuss->user->get('id') == $threadArray['author'] && $this->discuss->user->isLoggedIn) {
                    $class[] = $threadArray['replies'] < $threshold ? 'dis-my-normal-thread' : 'dis-my-veryhot-thread';
                } else {
                    $class[] = $threadArray['replies'] < $threshold ? '' : 'dis-veryhot-thread';
                }
            }
            $threadArray['class'] = implode(' ',$class);

            /* if sticky/locked */
            $icons = array();
            if ($threadArray['locked']) { $icons[] = '<div class="dis-thread-locked"></div>'; }
            if ($enableSticky && $threadArray['sticky']) {
                $icons[] = '<div class="dis-thread-sticky"></div>';
            }
            $threadArray['icons'] = implode("\n",$icons);

            $threadArray['views'] = number_format($threadArray['views']);
            $threadArray['replies'] = number_format($threadArray['replies']);

            /* unread class */
            $threadArray['unread'] = false;
            $threadArray['unread-cls'] = '';
            $threadArray['author_link'] = $canViewProfiles ? '<a class="dis-last-post-by" href="'.$this->discuss->request->makeUrl('user',array('user' => $threadArray['author'])).'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];

            $list[] = $this->discuss->getChunk($this->getOption('tpl','post/disThreadLi'),$threadArray);
        }
        $this->setPlaceholder('threads',implode($this->getOption('rowSeparator',"\n"),$list));

        $this->getActionButtons();
        $this->buildPagination();
    }

    /**
     * Build the pagination for this view
     * @return void
     */
    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array_merge(array(
            'count' => $this->threads['total'],
            'id' => 0,
            'view' => 'thread/new_replies_to_posts',
            'limit' => $this->threads['limit'],
        ), $this->options));
    }

    /**
     * Get the action buttons for this view
     * @return void
     */
    public function getActionButtons() {
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) {
            $markReadLink = $this->discuss->request->makeUrl('thread/new_replies_to_posts',array('read' => 1));
            $this->setPlaceholder('actionlink_mark_read', $markReadLink);
            $actionButtons[] = array('url' => $markReadLink, 'text' => $this->modx->lexicon('discuss.mark_all_as_read'), 'cls' => 'dis-action-mark_all_as_read');
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.new_replies_to_posts').' ('.number_format($this->threads['total']).')','active' => true);
        return $trail;
    }

    public function handleActions() {
        /* handle marking all as read */
        if (!empty($this->scriptProperties['read']) && $this->discuss->user->isLoggedIn) {
            $this->discuss->hooks->load('thread/read_all');
        }
    }
}

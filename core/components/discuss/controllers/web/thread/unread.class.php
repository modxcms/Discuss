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
 * Get all unread posts by user
 * 
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadUnreadController extends DiscussController {
    /** @var array $threads */
    public $threads = array();
    public $method = 'fetchUnread';
    
    public function getDefaultOptions() {
        return array(
            'postTpl' => 'post/disThreadLi',
            'dateFormat' => $this->discuss->dateFormat,

            'textButtonMarkAllRead' => $this->modx->lexicon('discuss.mark_all_as_read'),
            'textBreadcrumbsUnreadPosts' => $this->modx->lexicon('discuss.unread_posts'),

            'clsRow' => 'board-post,dis-board-li',
            'clsMyNormalThread' => 'dis-my-normal-thread',
            'clsMyHotThread' => 'dis-my-veryhot-thread',
            'clsNormalThread' => 'dis-normal-thread',
            'clsHotThread' => 'dis-veryhot-thread',
            'clsUnread' => 'dis-unread',

            'iconLocked' => '<div class="dis-thread-locked"></div>',
            'iconSticky' => '<div class="dis-thread-sticky"></div>',
            'iconSeparator' => "\n",
        );
    }
    
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.unread_posts').' ('.number_format($this->threads['total']).')';
    }
    public function getSessionPlace() {
        return 'thread/unread::'.$this->getProperty('page',1);
    }
    public function process() {
        /* setup default properties */
        $this->getData();

        $this->setOption('canViewProfiles',$this->modx->hasPermission('discuss.view_profiles'));
        $this->setOption('hotThreadThreshold',$this->modx->getOption('discuss.hot_thread_threshold',null,10));
        $this->setOption('enableSticky',$this->modx->getOption('discuss.enable_sticky',null,true));
        $this->setOption('enableHot',$this->modx->getOption('discuss.enable_hot',null,true));
        $list = array();
        /** @var disThread $thread */
        foreach ($this->threads['results'] as $thread) {
            $threadArray = $this->prepareThread($thread);
            $list[] = $this->discuss->getChunk($this->getOption('postTpl'),$threadArray);
        }
        $this->setPlaceholder('threads',implode("\n",$list));

        $this->getActionButtons();
        $this->buildPagination();
    }

    public function getData() {
        $limit = $this->getProperty('limit',$this->modx->getOption('discuss.threads_per_page',null,20),'!empty');
        $page = $this->getProperty('page',1,'!empty');
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;

        $sortBy = $this->getProperty('sortBy','post_last_on');
        $sortDir = $this->getProperty('sortDir','DESC');

        /* get unread threads */
        $this->threads = $this->modx->call('disThread',$this->method,array(&$this->modx,$sortBy,$sortDir,$limit,$start));
        $this->threads['limit'] = $limit;
        $this->threads['start'] = $start;
    }

    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array_merge(array(
            'count' => $this->threads['total'],
            'id' => 0,
            'view' => 'thread/unread',
            'limit' => $this->threads['limit'],
            'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
        ), $this->options));
    }

    public function getActionButtons() {
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) {
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/unread',array('read' => 1)), 'text' => $this->getOption('textButtonMarkAllRead'), 'cls' => 'dis-action-mark_all_as_read');
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->getOption('textBreadcrumbsUnreadPosts').' ('.number_format($this->threads['total']).')','active' => true);
        return $trail;
    }

    public function handleActions() {
        /* handle marking all as read */
        if (!empty($this->scriptProperties['read']) && $this->discuss->user->isLoggedIn) {
            $this->discuss->hooks->load('thread/read_all');
        }
    }

    /**
     * Prepare the thread for iteration
     * @param disThread $thread
     * @return array
     */
    protected function prepareThread(disThread $thread) {
        $thread->calcLastPostPage();
        $thread->getUrl();

        $threadArray = $thread->toArray();
        $threadArray['createdon'] = strftime($this->getOption('dateFormat'),strtotime($threadArray['createdon']));
        $threadArray['icons'] = '';

        /* set css class */
        $class = $this->getOption('clsRow');
        $class = explode(',',$class);
        if ($this->getOption('enableHot')) {
            $threshold = $this->getOption('hotThreadThreshold');
            if ($this->discuss->user->get('id') == $threadArray['author'] && $this->discuss->user->isLoggedIn) {
                $class[] = $threadArray['replies'] < $threshold ? $this->getOption('clsMyNormalThread') : $this->getOption('clsMyHotThread');
            } else {
                $class[] = $threadArray['replies'] < $threshold ? $this->getOption('clsNormalThread') : $this->getOption('clsHotThread');
            }
        }
        $threadArray['class'] = implode(' ',$class);

        /* if sticky/locked */
        $icons = array();
        if ($threadArray['locked']) { $icons[] = $this->getOption('iconLocked'); }
        if ($this->getOption('enableSticky') && $threadArray['sticky']) {
            $icons[] = $this->getOption('iconSticky');
        }
        $threadArray['icons'] = implode($this->getOption('iconSeparator'),$icons);

        $threadArray['views'] = number_format($threadArray['views']);
        $threadArray['replies'] = number_format($threadArray['replies']);

        /* unread class */
        $threadArray['unread'] = true;
        $threadArray['unread-cls'] = $this->getOption('clsUnread');
        $threadArray['author_link'] = $this->getOption('canViewProfiles') ? '<a class="dis-last-post-by" href="'.$this->discuss->request->makeUrl('u/'.$threadArray['author_username']).'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];
        return $threadArray;
    }
}

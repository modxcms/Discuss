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
require_once dirname(__FILE__).'/unread.class.php';
/**
 * Get all unread posts by user since their last visit
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadUnreadLastVisitController extends DiscussThreadUnreadController {
     public function process() {
        /* setup default properties */
        $limit = !empty($this->scriptProperties['limit']) ? $this->scriptProperties['limit'] : $this->modx->getOption('discuss.threads_per_page',null,20);
        $page = !empty($this->scriptProperties['page']) ? $this->scriptProperties['page'] : 1;
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;

        $sortBy = $this->modx->getOption('sortBy',$this->scriptProperties,'LastPost.createdon');
        $sortDir = $this->modx->getOption('sortDir',$this->scriptProperties,'DESC');
        $postTpl = $this->modx->getOption('postTpl',$this->options,'post/disPostLi');

        /* get unread threads */
        $this->threads = $this->modx->call('disThread','fetchUnread',array(&$this->modx,$sortBy,$sortDir,$limit,$start,true));
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
            $threadArray['createdon'] = strftime($this->discuss->dateFormat,strtotime($threadArray['createdon']));
            $threadArray['icons'] = '';

            /* set css class */
            $class = array('board-post','dis-board-li');
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
            $threadArray['unread'] = '<img src="'.$this->discuss->config['imagesUrl'].'icons/new.png'.'" class="dis-new" alt="" />';
            $threadArray['author_link'] = $canViewProfiles ? '<a class="dis-last-post-by" href="'.$this->discuss->request->makeUrl('u/'.$threadArray['author_username']).'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];

            $list[] = $this->discuss->getChunk($postTpl,$threadArray);
        }
        $this->setPlaceholder('threads',implode("\n",$list));

        $this->getActionButtons();
        $this->buildPagination();
    }


    public function getActionButtons() {
        $actionButtons = array();
        $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/unread'), 'text' => $this->modx->lexicon('discuss.unread_posts_all'));
        if ($this->discuss->user->isLoggedIn) {
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/unread_last_visit',array('read' => 1)), 'text' => $this->modx->lexicon('discuss.mark_all_as_read'));
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }


    public function handleActions() {
        /* handle marking all as read */
        if (!empty($this->scriptProperties['read']) && $this->discuss->user->isLoggedIn) {
            $this->discuss->hooks->load('thread/read_all',array(
                'lastLogin' => $this->discuss->user->get('last_login'),
            ));
        }
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.unread_posts_last_visit').' ('.number_format($this->threads['total']).')','active' => true);
        return $trail;
    }

    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array(
            'count' => $this->threads['total'],
            'id' => 0,
            'view' => 'thread/unread_last_visit',
            'limit' => $this->threads['limit'],
            'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
        ));
    }
}
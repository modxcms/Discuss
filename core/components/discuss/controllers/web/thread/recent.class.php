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
 * Show Recent Posts
 * 
 * @package discuss
 */
class DiscussThreadRecentController extends DiscussController {
    /** @var array $list */
    public $list = array();
    public function initialize() {
        $this->options['postTpl'] = 'post/disPostLi';
    }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.recent_posts');
    }
    public function getSessionPlace() {
        return 'recent';
    }
    public function process() {
        /* get default options */
        $limit = $this->modx->getOption('limit',$this->scriptProperties,$this->modx->getOption('discuss.num_recent_posts',null,10));
        $start = $this->modx->getOption('start',$this->scriptProperties,0);
        $page = !empty($this->scriptProperties['page']) ? $this->scriptProperties['page'] : 1;
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;

        /* recent posts */
        $this->list = $this->discuss->hooks->load('post/recent',array(
            'limit' => $limit,
            'start' => $start,
            'getTotal' => true,
            'postTpl' => $this->options['postTpl'],
        ));
        $this->list['limit'] = $limit;
        $this->list['start'] = $start;
        $this->setPlaceholder('recent_posts',$this->list['results']);

        $this->buildPagination();
        $this->getActionButtons();
    }

    public function getActionButtons() {
        $rssIcon = $this->discuss->getChunk('disLink',array(
            'url' => $this->discuss->request->makeUrl('thread/recent.xml'),
            'text' => '',
            'class' => 'dis-recent-rss',
            'id' => '',
            'attributes' => '',
        ));
        $this->setPlaceholder('rss_icon',$rssIcon);
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.recent_posts').' ('.number_format($this->list['total']).')','active' => true);
        return $trail;
    }

    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array(
            'count' => $this->list['total'],
            'id' => 0,
            'view' => 'thread/recent',
            'limit' => $this->list['limit'],
        ));
    }
}
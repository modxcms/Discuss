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
 * @subpackage controllers
 */
class DiscussThreadRecentController extends DiscussController {
    /** @var array $list */
    public $list = array();
    public function getDefaultOptions() {
        return array(
            'postTpl' => 'post/disPostLi',
            'textBreadcrumbsRecentPosts' => $this->modx->lexicon('discuss.recent_posts'),
            'rssIconLinkCls' => 'dis-recent-rss',
            'rssIconLinkId' => '',
            'rssIconLinkAttributes' => '',
            'rssIconLinkText' => '',
        );
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.recent_posts');
    }
    public function getSessionPlace() {
        return 'recent::'.$this->getProperty('page',1);
    }
    public function process() {
        /* get default options */
        $limit = $this->getProperty('limit',$this->modx->getOption('discuss.num_recent_posts',null,10),'!empty');
        $page = $this->getProperty('page',1);
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;

        /* recent posts */
        $this->list = $this->discuss->hooks->load('post/recent',array(
            'limit' => $limit,
            'start' => $start,
            'getTotal' => true,
            'postTpl' => $this->getOption('postTpl'),
        ));
        echo 'total from placeholder: ' . $this->list['total'];
        $this->list['limit'] = $limit;
        $this->list['start'] = $start;
        $this->setPlaceholder('recent_posts',$this->list['results']);
        $this->setPlaceholder('total', $this->list['total']);

        $this->buildPagination();
        $this->getActionButtons();
    }

    public function getActionButtons() {
        $rssIcon = $this->discuss->getChunk('disLink',array(
            'url' => $this->discuss->request->makeUrl('thread/recent.xml'),
            'text' => $this->getOption('rssIconLinkText',''),
            'class' => $this->getOption('rssIconLinkCls','dis-recent-rss'),
            'id' => $this->getOption('rssIconLinkId',''),
            'attributes' => $this->getOption('rssIconLinkAttributes',''),
        ));
        $this->setPlaceholder('rss_icon',$rssIcon);
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->getOption('textBreadcrumbsRecentPosts').' ('.number_format($this->list['total']).')','active' => true);
        return $trail;
    }

    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array(
            'count' => $this->list['total'],
            'id' => 0,
            'view' => 'thread/recent',
            'limit' => $this->list['limit'],
            'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
        ));
    }
}

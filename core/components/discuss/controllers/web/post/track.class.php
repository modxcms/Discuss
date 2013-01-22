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
 * Get all posts by an IP address
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussPostTrackController extends DiscussController {
    /** @var array $posts */
    public $posts;
    
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.track_ip');
    }
    public function getSessionPlace() {
        return '';
    }

    public function initialize() {
        if (!$this->discuss->user->isLoggedIn) $this->modx->sendUnauthorizedPage();
    }

    public function process() {
        $ip = $this->getProperty('ip','');
        
        $limit = $this->getProperty('limit',$this->modx->getOption('discuss.post_per_page',null,10));
        //$start = $this->getProperty('start',0);
        $page = (int)$this->getProperty('page',1);
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;

        /* posts by ip */
        $this->posts = $this->discuss->hooks->load('post/byip',array(
            'ip' => $ip,
            'limit' => $limit,
            'start' => $start,
            'getTotal' => true,
        ));
        $this->setPlaceholder('posts',$this->posts['results']);
        $this->setPlaceholder('total',$this->posts['total']);
        $this->setPlaceholder('ip',$ip);

        /* build pagination */
        $this->discuss->hooks->load('pagination/build',array(
            'count' => $this->posts['total'],
            'id' => 0,
            'view' => 'post/track',
            'limit' => $limit,
            'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
        ));
    }
    
    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.track_ip').': '.$this->getProperty('ip','').' ('.number_format($this->posts['total']).')','active' => true);
        return $trail;
    }
}

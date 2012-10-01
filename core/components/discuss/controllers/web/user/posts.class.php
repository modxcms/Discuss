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
 * @subpackage controllers
 */
/**
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussUserPostsController extends DiscussController {
    /** @var disUser $user */
    public $user;
    public $posts;


    public function initialize() {
        $user = $this->getProperty('user',$this->discuss->user->get('id'));
        if (empty($user)) { $this->discuss->sendErrorPage(); }
        $user = trim($user,' /');
        $key = intval($user) <= 0 ? 'username' : 'id';
        $c = array();
        $c[!empty($this->scriptProperties['i']) ? 'integrated_id' : $key] = $user;
        $this->user = $this->modx->getObject('disUser',$c);
        if (empty($this->user)) { $this->discuss->sendErrorPage(); }

        $this->modx->lexicon->load('discuss:user');
    }

    public function getPageTitle() {
        return $this->modx->lexicon('discuss.user_posts',array('user' => $this->user->get('username'), 'count' => $this->posts['total']));
    }
    public function getSessionPlace() {
        return 'user/posts:'.(($this->user) ? $this->user->get('id') : (int)$this->scriptProperties['user']);
    }

    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }

    /**
     * Process the page
     * @return void
     */
    public function process() {
        $userArray = $this->user->toArray();
        if (!$this->user->isAdmin() && !$this->user->get('show_email')) {
            unset($userArray['email']);
        }
        $this->setPlaceholders($userArray);

        $this->getPosts();
        $this->buildPagination();

        /* do output */
        $this->getMenu();
        $this->modx->setPlaceholder('discuss.user',$this->user->get('username'));
    }

    public function getMenu() {
        $this->setPlaceholder('usermenu',$this->discuss->getChunk('disUserMenu',$this->getPlaceholders()));
    }

    public function getPosts() {
        $perPage = $this->modx->getOption('discuss.num_recent_posts',null,10);
        $start = (isset($this->scriptProperties['page']) && !empty($this->scriptProperties['page'])) ?
            (intval($this->scriptProperties['page']) > 1) ? ((int)$this->scriptProperties['page'] - 1) * $perPage : 0
            : 0;
        $options = array_merge($this->options,array(
            'user' => $this->user->get('id'),
            'start' => $start,
            'limit' => $perPage,
            'getTotal' => true
        ));
        $this->posts = $this->discuss->hooks->load('post/byuser',$options);
        $this->setPlaceholder('posts',$this->posts['results']);
        $this->setPlaceholder('posts.total',$this->posts['total']);
    }

    public function buildPagination() {
        if (empty($this->scriptProperties['print'])) {
            $this->discuss->hooks->load('pagination/build',array(
                'count' => $this->posts['total'],
                'id' => $this->user->get('id'),
                'view' => 'user/posts',
                'limit' => $this->posts['limit'],
                'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
            ));
        }
    }
    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );

        $userParams = array();
        if ($this->user->get('id') != $this->discuss->user->get('id')) {
            $userParams = array('user' => $this->user->get('id'));
        }
        $trail[] = array(
            'text' => $this->modx->lexicon('discuss.user.trail',array('user' => $this->user->get('username'))),
            'url' => $this->discuss->request->makeUrl('user', $userParams)
        );

        $trail[] = array('text' => $this->modx->lexicon('discuss.posts'),'active' => true);
        return $trail;
    }
}

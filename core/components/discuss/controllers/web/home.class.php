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
 * Handle the home page
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussHomeController extends DiscussController {

    public function getSessionPlace() {
        return 'home';
    }

    public function getPageTitle() {
        return $this->modx->getOption('discuss.forum_title');
    }

    public function process() {
        $this->handleActions();

        if (!empty($this->options['showBoards'])) {
            $this->getBoards();
        }

        $this->renderActionButtons();

        if (!empty($this->options['showRecentPosts'])) {
            $this->getRecentPosts();
        }

        $this->getBreadcrumbs();

        /* invoke render event for plugin injection of custom stuff */
        $this->setPlaceholders(array(
            'top' => '',
            'bottom' => '',
            'aboveBoards' => '',
            'belowBoards' => '',
            'aboveRecent' => '',
            'belowRecent' => '',
        ));
        $eventOutput = $this->discuss->invokeRenderEvent('OnDiscussRenderHome',$this->getPlaceholders());
        if (!empty($eventOutput)) {
            $this->setPlaceholders($eventOutput);
        }
    }

    /**
     * Handle any POST actions on the page
     * @return void
     */
    public function handleActions() {
        /* process logout */
        if (isset($this->scriptProperties['logout']) && $this->scriptProperties['logout']) {
            $response = $this->modx->runProcessor('security/logout');
            $url = $this->discuss->request->makeUrl();
            $this->modx->sendRedirect($url);
        }
        if (isset($this->scriptProperties['read']) && !empty($this->scriptProperties['read'])) {
            $c = array(
                'board' => 0,
            );
            if (!empty($this->scriptProperties['category'])) $c['category'] = (int)$this->scriptProperties['category'];
            $this->discuss->hooks->load('thread/read_all',$c);
        }
    }

    /**
     * Get boards
     * @return void
     */
    public function getBoards() {
        $c = array(
            'board' => 0,
            'checkUnread' => $this->getOption('checkUnread',true,'isset'),
        );
        if (!empty($this->scriptProperties['category'])) {
            $c['category'] = (int)$this->scriptProperties['category'];
            $this->setPlaceholder('category',$c['category']);
        } else {
            $this->setPlaceholder('category',0);
        }
        $boards = $this->discuss->hooks->load('board/getlist',$c);
        if (!empty($boards)) {
            $this->setPlaceholder('boards',$boards);
        }
    }

    /**
     * @return void
     */
    public function renderActionButtons() {
        /* action buttons */
        $actionButtons = array();
        if ($this->discuss->user->isLoggedIn) { /* if logged in */
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('',array('read' => 1)), 'text' => $this->modx->lexicon('discuss.mark_all_as_read'));
            if ($this->getOption('showLogoutActionButton',false,'!empty')) {
                $authLink = $this->discuss->request->makeUrl('logout');
                $authMsg = $this->modx->lexicon('discuss.logout');
                $this->modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');
                $actionButtons[] = array('url' => $authLink, 'text' => $authMsg, 'cls' => 'dis-action-logout');
            }
        } else { /* if logged out */
            $authLink = $this->discuss->request->makeUrl('login');
            $authMsg = $this->modx->lexicon('discuss.login');
            $this->modx->setPlaceholder('discuss.authLink','<a href="'.$authLink.'">'.$authMsg.'</a>');

            if ($this->getOption('showLoginForm',false,'!empty')) {
                $this->modx->setPlaceholder('discuss.loginForm',$this->discuss->getChunk('disLogin'));
            }
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function getRecentPosts() {
        $cacheKey = 'discuss/board/recent/'.$this->discuss->user->get('id');
        $recent = $this->modx->cacheManager->get($cacheKey);
        if (empty($recent)) {
            $recent = $this->discuss->hooks->load('post/recent');
            $this->modx->cacheManager->set($cacheKey,$recent,$this->modx->getOption('discuss.cache_time',null,3600));
        }
        $this->setPlaceholder('recent_posts',$recent['results']);
        unset($recent);
    }

    /**
     * Get the breadcrumbs for this page
     * @return array
     */
    public function getBreadcrumbs() {
        $trail = array();
        /** @var disCategory|null $category */
        $category = null;
        if (!empty($this->scriptProperties['category'])) {
            $category = $this->modx->getObject('disCategory',$this->scriptProperties['category']);
        } else if ($this->getOption('hideIndexBreadcrumbs',true)) {
            return '';
        }
        if (!empty($category)) {
            $trail[] = array(
                'text' => $this->modx->getOption('discuss.forum_title'),
                'url' => $this->discuss->request->makeUrl(),
            );
            $trail[] = array(
                'text' => $category->get('name'),
                'active' => true
            );
        } else {
            $trail[] = array('text' => $this->modx->getOption('discuss.forum_title'),'active' => true);
        }
        return $trail;
    }
}

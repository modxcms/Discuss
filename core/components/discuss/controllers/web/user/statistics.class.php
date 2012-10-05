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
 * User statistics page
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussUserStatisticsController extends DiscussController {
    /** @var disUser $user */
    public $user;
    
    public function initialize() {
        $user = $this->getProperty('user',false);
        if (empty($user)) { $this->discuss->sendErrorPage(); }
        $this->user = $this->modx->getObject('disUser',$user);
        if (empty($this->user)) { $this->discuss->sendErrorPage(); }

        $this->modx->lexicon->load('discuss:user');
    }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.user_statistics_header',array('user' => $this->user->get('username')));
    }
    public function getSessionPlace() {
        return 'user/statistics:'.(($this->user) ? $this->user->get('id') : $this->getProperty('user', 0));
    }
    public function process() {
        $this->setPlaceholders($this->user->toArray());

        /* # of topics started */
        $this->setPlaceholder('topics',number_format($this->modx->getCount('disThread',array(
            'author_first' => $this->user->get('id'),
        ))));

        /* # of replies to topics */
        $this->setPlaceholder('replies',number_format($this->modx->getCount('disPost',array(
            'author' => $this->user->get('id'),
            'parent:!=' => 0,
        ))));

        /* # of total posts */
        $this->setPlaceholder('posts',number_format($this->user->get('posts')));

        $this->modx->setPlaceholder('discuss.user',$this->user->get('username'));
        $this->getMenu();
    }

    public function getMenu() {
        $menuTpl = $this->getProperty('menuTpl','disUserMenu');
        $this->setPlaceholder('usermenu',$this->discuss->getChunk($menuTpl,$this->getPlaceholders()));
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
            'url' => $this->discuss->request->makeUrl(array('action' => 'user'), $userParams)
        );

        $trail[] = array('text' => $this->modx->lexicon('discuss.stats'),'active' => true);
        return $trail;
    }
}

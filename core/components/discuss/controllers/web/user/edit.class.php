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
 * The edit user page
 *
 * @todo Currently only supports SSO mode. Add native support.
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussUserEditController extends DiscussController {

    public function initialize() {
        /* allow external update profile page */
        $upResourceId = $this->modx->getOption('discuss.update_profile_resource_id',null,0);
        if (!empty($upResourceId) && $this->discuss->ssoMode) {
            $url = $this->modx->makeUrl($upResourceId,'',array('discuss' => 1),'full');
            $this->modx->sendRedirect($url);
        }

        $this->modx->lexicon->load('discuss:user');
    }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.user_edit_header',array('user' => $this->discuss->user->get('username')));
    }
    public function getSessionPlace() {
        return 'user/edit';
    }
    public function process() {
        $this->setPlaceholders($this->discuss->user->toArray());
        $this->modx->setPlaceholder('discuss.user',$this->discuss->user->get('username'));
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

        $trail[] = array(
            'text' => $this->modx->lexicon('discuss.user.trail',array('user' => $this->discuss->user->get('username'))),
            'url' => $this->discuss->request->makeUrl('user')
        );

        $trail[] = array('text' => $this->modx->lexicon('discuss.edit'),'active' => true);
        return $trail;
    }
}

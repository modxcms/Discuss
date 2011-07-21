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
 * User merge account page
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussUserMergeController extends DiscussController {

    public function initialize() {
        $this->modx->lexicon->load('discuss:user');
    }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.account_merge');
    }
    public function getSessionPlace() {
        return 'user-merge:'.$this->discuss->user->get('id');
    }
    public function process() {
        $this->setPlaceholders($this->discuss->user->toArray());
        $this->getMenu();

    }
    public function getMenu() {
        $menuTpl = $this->getProperty('menuTpl','disUserMenu');
        $this->setPlaceholder('usermenu',$this->discuss->getChunk($menuTpl,$this->getPlaceholders()));
    }
}
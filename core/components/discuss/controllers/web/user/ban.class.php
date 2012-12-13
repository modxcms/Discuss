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
 * The edit user page
 *
 * @var modX $modx
 * @var Discuss $discuss
 *
 * @package discuss
 */
class DiscussUserBanController extends DiscussController {
    /** @var disUser $user */
    public $user;
    /** @var modUser $modxUser */
    public $modxUser;

    public function initialize() {
        /* @var disUser $user get user */
        if (empty($this->scriptProperties['u'])) { $this->modx->sendErrorPage(); }
        $c = array();
        $c[!empty($this->scriptProperties['i']) ? 'integrated_id' : 'id'] = $this->scriptProperties['u'];
        $this->user = $this->modx->getObject('disUser',$c);
        if (empty($this->user)) { $this->modx->sendErrorPage(); }

        $this->modxUser = $this->user->getOne('User');

        $this->modx->lexicon->load('discuss:user');
    }

    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn && $this->discuss->user->isAdmin();
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.ban_user_header',array('user' => $this->user->get('username')));
    }
    public function getSessionPlace() {
        return 'user/ban:u='.$this->user->get('id');
    }
    public function process() {
        $this->setPlaceholders($this->user->toArray('fi.'));

        if (!empty($scriptProperties['success'])) {
            $this->setPlaceholder('fi.successMessage',$this->modx->lexicon('discuss.ban_added_msg'));
        }

        if (empty($_POST)) {
            $this->setPlaceholder('fi.expireson',30);
            $this->setPlaceholder('fi.ip_range',$this->getPlaceholder('fi.ip'));
            $this->setPlaceholder('fi.hostname',gethostbyaddr($this->getPlaceholder('fi.ip')));
            $this->setPlaceholder('fi.disUser',$this->user->get('id'));
        }
        $this->setPlaceholder('other_fields','');

        /* fire OnDiscussBanUser */
        $placeholders = $this->getPlaceholders();
        $this->modx->invokeEvent('OnDiscussBeforeBanUser',array(
            'user' => &$this->user,
            'modUser' => &$this->modxUser,
            'placeholders' => &$placeholders,
        ));
        $this->setPlaceholders($placeholders);

        /* do output */
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

        $userParams = array();
        if ($this->user->get('id') != $this->discuss->user->get('id')) {
            $userParams = array('user' => $this->user->get('id'));
        }
        $trail[] = array(
            'text' => $this->modx->lexicon('discuss.user.trail',array('user' => $this->user->get('username'))),
            'url' => $this->discuss->request->makeUrl('user', $userParams)
        );

        $trail[] = array('text' => $this->modx->lexicon('discuss.ban'),'active' => true);
        return $trail;
    }

}

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
 * Display the Login page
 *
 * @todo Only supports SSO login now. Eventually make a real login page.
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussLoginController extends DiscussController {
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.login');
    }
    public function getSessionPlace() { return ''; }

    public function process() {
        $loginResourceId = $this->modx->getOption('discuss.login_resource_id',null,0);
        if (!empty($loginResourceId) && $this->discuss->ssoMode) {
            $url = $this->modx->makeUrl($loginResourceId,'',array('discuss' => 1));
            $this->modx->sendRedirect($url);
        }
    }
}
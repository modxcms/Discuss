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
 * Auto-redirect to profile page. Handles SMF-style redirects.
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussProfileController extends DiscussController {
    public function initialize() {
        $params['action'] = 'user';
        if (!empty($_REQUEST['u'])) {
            $params['user'] = $_REQUEST['u'];
        }
        /* handle SMF profiles */
        $qs = $_SERVER['QUERY_STRING'];
        $u = strpos($qs,';u=');
        if ($u !== false) {
            $params['user'] = substr($qs,$u+3);
            $params['i'] = true;
        }
        $url = $this->modx->makeUrl($this->modx->resource->get('id'),'',$params);
        $this->modx->sendRedirect($url);
    }
    public function getPageTitle() { return ''; }
    public function getSessionPlace() { return ''; }
    public function process() {}
}
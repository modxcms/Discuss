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
 * Loads the home page.
 *
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientStartupScript($modx->getOption('manager_url').'assets/modext/widgets/security/modx.tree.user.group.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/usergroup/usergroups.panel.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/board/boards.panel.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/user/users.panel.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/home.panel.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'sections/home.js');
$output = '<div id="dis-panel-home-div"></div>';

return $output;

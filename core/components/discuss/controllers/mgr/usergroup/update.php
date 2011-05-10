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
 * @package discuss
 * @subpackage controllers
 */
/* if there is no disUserGroupProfile for this user group, create one */
if (empty($_REQUEST['id'])) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_ns'));
$profile = $modx->getObject('disUserGroupProfile',array('usergroup' => $_REQUEST['id']));
if (empty($profile)) {
    $usergroup = $modx->getObject('modUserGroup',$_REQUEST['id']);
    if (empty($usergroup)) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf',array('id' => $_REQUEST['id'])));

    $profile = $modx->newObject('disUserGroupProfile');
    $profile->set('usergroup',$_REQUEST['id']);
    $profile->save();
}


$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.boards.grid.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.members.grid.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.panel.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'sections/usergroup/update.js');
$output = '<div id="dis-panel-usergroup-div"></div>';

return $output;

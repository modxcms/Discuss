<?php
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


$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/usergroup/usergroup.boards.grid.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/usergroup/usergroup.members.grid.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/usergroup/usergroup.panel.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/sections/usergroup/update.js');
$output = '<div id="dis-panel-usergroup-div"></div>';

return $output;

<?php
/**
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/usergroup/usergroup.boards.grid.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/usergroup/usergroup.members.grid.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/usergroup/usergroup.panel.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/sections/usergroup/update.js');
$output = '<div id="dis-panel-usergroup-div"></div>';

return $output;

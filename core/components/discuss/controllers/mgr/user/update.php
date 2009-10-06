<?php
/**
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/user/user.posts.grid.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/user/user.panel.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/sections/user/update.js');
$output = '<div id="dis-panel-user-div"></div>';

return $output;

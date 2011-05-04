<?php
/**
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/user/user.posts.grid.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/user/user.panel.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'sections/user/update.js');
$output = '<div id="dis-panel-user-div"></div>';
return $output;

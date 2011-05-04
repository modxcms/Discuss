<?php
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

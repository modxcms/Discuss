<?php
/**
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/board/board.moderators.grid.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/board/board.usergroups.grid.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/widgets/board/board.panel.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/sections/board/update.js');
$output = '<div id="dis-panel-board-div"></div>';

return $output;

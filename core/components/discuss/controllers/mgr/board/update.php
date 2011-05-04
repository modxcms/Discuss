<?php
/**
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/board/board.moderators.grid.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/board/board.usergroups.grid.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'widgets/board/board.panel.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'sections/board/update.js');
$output = '<div id="dis-panel-board-div"></div>';

return $output;

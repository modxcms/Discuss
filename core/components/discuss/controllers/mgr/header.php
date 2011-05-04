<?php
/**
 * Loads the header for mgr pages.
 *
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientCSS($discuss->config['mgrCssUrl'].'mgr.css');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'discuss.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'combos.js');
$modx->regClientStartupScript($discuss->config['mgrJsUrl'].'windows.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    Dis.config = '.$modx->toJSON($discuss->config).';
    Dis.config.connector_url = "'.$discuss->config['connectorUrl'].'";
    Dis.request = '.$modx->toJSON($_GET).';
});
</script>');

return '';
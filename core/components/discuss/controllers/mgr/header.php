<?php
/**
 * Loads the header for mgr pages.
 *
 * @package discuss
 * @subpackage controllers
 */
$modx->regClientCSS($discuss->config['cssUrl'].'mgr.css');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/discuss.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/combos.js');
$modx->regClientStartupScript($discuss->config['jsUrl'].'mgr/windows.js');
$modx->regClientStartupHTMLBlock('<script type="text/javascript">
Ext.onReady(function() {
    Dis.config = '.$modx->toJSON($discuss->config).';
    Dis.config.connector_url = "'.$discuss->config['connectorUrl'].'";
    Dis.request = '.$modx->toJSON($_GET).';
});
</script>');

return '';
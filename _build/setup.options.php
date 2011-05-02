<?php
/**
 * Build the setup options form.
 *
 * @package discuss
 * @subpackage build
 */
/* set some default values */
$demoDataChecked = '';
/* get values based on mode */
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $modelPath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/';
        $modx->addPackage('discuss',$modelPath);
        $cat = $modx->getObject('disCategory',array('name' => 'Welcome'));
        $demoDataChecked = $cat ? '' : ' checked="checked"';

        break;
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

/* do output html */
$output = '
<h2>Discuss Installer</h2>
<p>Thanks for installing Discuss! Please review the setup options below before proceeding.</p>
<br />

<h3>Demo Data</h3>

<label for="discuss-install_demodata">Install Demo Data:</label>
<input type="checkbox" name="install_demodata" id="discuss-install_demodata" value="1"'.$demoDataChecked.' />
<p>Checking this will install demo data. It is recommended to do this on your first install of Discuss.</p>
<br /><br />
';


return $output;
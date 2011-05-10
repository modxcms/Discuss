<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 */
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
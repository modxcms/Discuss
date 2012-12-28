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
 * @var modX $modx
 * @var array $options
 * 
 * @package discuss
 * @subpackage build
 */
$demoDataChecked = '';
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:
        $modelPath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/';
        $modx->addPackage('discuss',$modelPath);
        $cat = $modx->getCount('disUser');
        $demoDataChecked = $cat > 0 ? '' : ' checked="checked"';
        break;
    case xPDOTransport::ACTION_UNINSTALL:
        break;
}

/* do output html */
$output = '
<h2>Discuss Installer</h2>
<p>Thanks for installing Discuss! Please review the setup options below before proceeding.</p>
<br />

<h3>Setup Options</h3>

<label for="discuss-install_demodata">Install Demo Data:</label>
<input type="checkbox" name="install_demodata" id="discuss-install_demodata" value="1"'.$demoDataChecked.' />
<p>Checking this will install demo data. It is recommended to do this on your first install of Discuss.</p>
<br /><br />

<label for="discuss-install_resource">Install Discuss Resource:</label>
<input type="checkbox" name="install_resource" id="discuss-install_resource" value="1"'.$demoDataChecked.' />
<p>Checking this field will create a Resource in your site root with the alias "forums" with Discuss setup inside it.</p>
<br /><br />
';


return $output;

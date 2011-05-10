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
 * Resolve paths
 *
 * @package discuss
 * @subpackage build
 */
function createSetting(&$modx,$key,$value) {
    $ct = $modx->getCount('modSystemSetting',array(
        'key' => 'discuss.'.$key,
    ));
    if (empty($ct)) {
        $setting = $modx->newObject('modSystemSetting');
        $setting->set('key','discuss.'.$key);
        $setting->set('value',$value);
        $setting->set('namespace','discuss');
        $setting->set('area','Paths');
        $setting->save();
    }
}
if ($object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
            $modx =& $object->xpdo;

            /* setup paths */
            //createSetting($modx,'core_path',$modx->getOption('core_path').'components/discuss/');
            //createSetting($modx,'assets_path',$modx->getOption('assets_path').'components/discuss/');
            createSetting($modx,'attachments_path',$modx->getOption('assets_path').'components/discuss/attachments/');

            /* setup urls */
            //createSetting($modx,'assets_url',$modx->getOption('assets_url').'components/discuss/');
            createSetting($modx,'files_url',$modx->getOption('assets_url').'components/discuss/files/');
            createSetting($modx,'attachments_url',$modx->getOption('assets_url').'components/discuss/attachments/');
        break;
        case xPDOTransport::ACTION_UPGRADE:
        break;
    }
}
return true;
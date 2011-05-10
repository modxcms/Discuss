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
 * Resolve creating db tables
 *
 * @package discuss
 * @subpackage build
 */
function adjustSetting(&$modx,$name,$options,$area = 'General') {
    if (empty($options[$name])) return false;

    $setting = $modx->getObject('modSystemSetting',array(
        'key' => 'discuss.'.$name,
    ));
    if (!$setting) {
        $setting = $modx->newObject('modSystemSetting');
        $setting->set('key','discuss.'.$name);
        $setting->set('namespace','discuss');
        $setting->set('area',$area);
    }
    $setting->set('value',$options[$name]);
    $setting->save();
}

if ($object->xpdo && !empty($options['install_demodata'])) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modx =& $object->xpdo;

            break;
    }
}
return true;
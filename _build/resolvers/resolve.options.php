<?php
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

            adjustSetting($modx,'forum_title',$options);
            adjustSetting($modx,'use_css',$options);
            adjustSetting($modx,'load_jquery',$options);

            break;
    }
}
return true;
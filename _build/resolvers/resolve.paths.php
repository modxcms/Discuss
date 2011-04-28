<?php
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
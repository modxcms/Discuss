<?php
/**
 * Resolve creating db tables
 *
 * @package discuss
 * @subpackage build
 */
if ($object->xpdo && !empty($options['install_demodata'])) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:

$modx =& $object->xpdo;

/* set forum title */
$setting = $modx->getObject('modSystemSetting',array(
    'key' => 'discuss.forum_title',
));
if (!$setting) {
    $setting = $modx->newObject('modSystemSetting');
    $setting->set('key','discuss.forum_title');
    $setting->set('namespace','discuss');
    $setting->set('area','General');
}
$setting->set('value',$options['forum_title']);
$setting->save();


            break;
    }
}
return true;
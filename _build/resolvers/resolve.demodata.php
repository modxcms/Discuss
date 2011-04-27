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

$modx =& $object->xpdo;
$modelPath = $modx->getOption('discuss.core_path',null,$modx->getOption('core_path').'components/discuss/').'model/';
$modx->addPackage('discuss',$modelPath);

$modx->log(modX::LOG_LEVEL_INFO,'Installing demo data...');

$category = $modx->newObject('disCategory');
$category->fromArray(array(
    'name' => 'Welcome',
    'description' => 'The welcome section.',
    'collapsible' => true,
));
$category->save();

$board = $modx->newObject('disBoard');
$board->fromArray(array(
    'name' => 'Discuss 101',
    'category' => $category->get('id'),
    'description' => 'Introduce yourself to the community here.',
    'ignoreable' => true,
));
$board->save();

if ($modx->user && $modx->user instanceof modUser) {
    $modx->user->profile = $modx->user->getOne('Profile');
    if ($modx->user->profile && $modx->user->profile instanceof modUserProfile) {
        $disUser = $modx->newObject('disUser');
        $disUser->fromArray($modx->user->profile->toArray());
        $name = $modx->user->profile = $profile->get('fullname');
        $name = explode(' ',$name);
        $disUser->fromArray(array(
            'user' => $modx->user->get('id'),
            'username' => $modx->user->get('username'),
            'createdon' => strftime('%Y-%m-%d %H:%M:%S'),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'synced' => true,
            'syncedat' => strftime('%Y-%m-%d %H:%M:%S'),
            'source' => 'internal',
            'confirmed' => true,
            'confirmedon' => strftime('%Y-%m-%d %H:%M:%S'),
            'status' => disUser::ACTIVE,
            'name_first' => $name[0],
            'name_last' => !empty($name[1]) ? $name[1] : '',
            'salt' => $modx->user->get('salt'),
        ));
        $disUser->save();
    }
}
            break;
        case xPDOTransport::ACTION_UPGRADE:
            break;
    }
}
return true;
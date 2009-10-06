<?php
/**
 * Adds modActions and modMenus into package
 *
 * @package discuss
 * @subpackage build
 */
$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'discuss',
    'parent' => '0',
    'controller' => 'index',
    'haslayout' => '1',
    'lang_topics' => 'discuss:default,file',
    'assets' => '',
),'',true,true);

/* load menu into action */
$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'id' => 1,
    'parent' => '2',
    'text' => 'discuss',
    'description' => 'discuss.menu_desc',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => '0',
    'params' => '',
    'handler' => '',
),'',true,true);
$action->addMany($menu,'Menus');
unset($menus);
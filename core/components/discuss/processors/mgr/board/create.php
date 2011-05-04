<?php
/**
 * Create a Board.
 * 
 * @package discuss
 * @subpackage processors
 */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.board_err_ns_name'));
if (empty($scriptProperties['category'])) $modx->error->addField('category',$modx->lexicon('discuss.board_err_ns_category'));

$scriptProperties['locked'] = !empty($scriptProperties['locked']) ? true : false;
$scriptProperties['ignoreable'] = !empty($scriptProperties['ignoreable']) ? true : false;


$category = $modx->getObject('disCategory',$scriptProperties['category']);
if (empty($category)) $modx->error->addField('category',$modx->lexicon('discuss.board_err_ns_category'));

if ($modx->error->hasError()) {
    return $modx->error->failure();
}

$board = $modx->newObject('disBoard');
$board->fromArray($scriptProperties);

/* add default user groups */
$defaultUserGroups = $category->get('default_usergroups');
if (empty($defaultUserGroups)) {
    $defaultUserGroups = $modx->getOption('discuss.default_board_usergroups',null,false);
}
if (!empty($defaultUserGroups)) {
    $defaultUserGroups = explode(',',$defaultUserGroups);
    $ugs = array();
    foreach ($defaultUserGroups as $userGroupName) {
        $usergroup = $modx->getObject('modUserGroup',array(
            'name' => trim($userGroupName),
        ));
        if ($usergroup) {
            $ug = $modx->newObject('disBoardUserGroup');
            $ug->set('usergroup',$usergroup->get('id'));
            $ugs[] = $ug;
        }
    }
    $board->addMany($ugs,'UserGroups');
}

/* add default moderators */
$defaultModerators = $category->get('default_moderators');
if (empty($defaultUserGroups)) {
    $defaultUserGroups = $modx->getOption('discuss.default_board_moderators',null,false);
}
if (!empty($defaultModerators)) {
    $defaultModerators = explode(',',$defaultModerators);
    $mods = array();
    foreach ($defaultModerators as $username) {
        $c = $modx->newQuery('disUser');
        $c->innerJoin('modUser','User');
        $c->where(array(
            'User.username' => trim($username),
        ));
        $user = $modx->getObject('disUser',$c);
        if ($user) {
            $mod = $modx->newObject('disModerator');
            $mod->set('user',$user->get('id'));
            $mods[] = $mod;
        }
    }
    $board->addMany($mods,'Moderators');
}

if ($board->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.board_err_save'));
}

return $modx->error->success('',$board);
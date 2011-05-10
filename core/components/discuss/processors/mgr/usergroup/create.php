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
 * Create a User Group
 *
 * @package discuss
 * @subpackage processors
 */
/* validate form */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.usergroup_err_ns_name'));

/* check for existing */
$alreadyExists = $modx->getObject('modUserGroup',array('name' => $scriptProperties['name']));
if ($alreadyExists) $modx->error->addField('name',$modx->lexicon('discuss.usergroup_err_ae'));

/* if any errors, return */
if ($modx->error->hasError()) {
    return $modx->error->failure();
}

/* create usergroup */
$usergroup = $modx->newObject('modUserGroup');
$usergroup->fromArray($scriptProperties);

/* save usergroup */
if ($usergroup->save() === false) {
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_save'));
}

/* create discuss user group */
$profile = $modx->newObject('disUserGroupProfile');
$scriptProperties['post_based'] = !empty($scriptProperties['post_based']) ? $scriptProperties['post_based'] : 0;
$profile->fromArray($scriptProperties);
$profile->set('usergroup',$usergroup->get('id'));
if (!$profile->save()) {
    $usergroup->remove();
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_save'));
}

return $modx->error->success('',$usergroup);
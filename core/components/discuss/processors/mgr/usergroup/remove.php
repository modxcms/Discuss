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
 * @package discuss
 * @subpackage processors
 */
/* get usergroup */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_ns'));
$profile = $modx->getObject('disUserGroupProfile',array('usergroup' => $scriptProperties['id']));
if ($profile == null) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf'));

$usergroup = $profile->getOne('UserGroup');
if (!$usergroup) return $modx->error->failure($modx->lexicon('discuss.usergroup_err_nf',array('id' => $scriptProperties['id'])));

/* remove usergroup */
$profile->remove();
if ($usergroup->remove() == false) {
    return $modx->error->failure($modx->lexicon('discuss.usergroup_err_remove'));
}

return $modx->error->success('',$usergroup);
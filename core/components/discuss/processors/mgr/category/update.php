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
/* get category */
if (empty($scriptProperties['id'])) return $modx->error->failure($modx->lexicon('discuss.category_err_ns'));
$category = $modx->getObject('disCategory',$scriptProperties['id']);
if (!$category) return $modx->error->failure($modx->lexicon('discuss.category_err_nf'));

/* do validation */
if (empty($scriptProperties['name'])) $modx->error->addField('name',$modx->lexicon('discuss.category_err_ns_name'));

$scriptProperties['collapsible'] = !empty($scriptProperties['collapsible']) ? true : false;

if ($modx->error->hasError()) {
    $modx->error->failure();
}

/* set fields */
$category->fromArray($scriptProperties);

/* save board */
if ($category->save() == false) {
    return $modx->error->failure($modx->lexicon('discuss.category_err_save'));
}

return $modx->error->success('',$category);
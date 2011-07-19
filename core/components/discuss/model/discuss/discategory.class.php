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
 * Top-level aggregator for disBoard objects
 *
 * @property string $name A name for the Category
 * @property string $description A user-provided description
 * @property boolean $collapsible Not yet in use.
 * @property int $rank The order of the category in sorting
 * @property string $default_moderators A comma-separated list of Users to automatically make Moderators for all new boards in this Category
 * @property string $default_usergroups A comma-separated list of UserGroups to automatically assign to all new boards in this Category
 * @property int $integrated_id If imported from another system, the PK of the category in that system
 *
 * @property array $Boards A collection of Boards in this category
 *
 * @package discuss
 */
class disCategory extends xPDOSimpleObject {
    /**
     * Gets the URL for the category
     * 
     * @return string
     */
    public function getUrl() {
        $url = $this->xpdo->discuss->request->makeUrl('',array('category' => $this->get('id')));
        $this->set('url',$url);
        return $url;
    }
}
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
 * @subpackage controllers
 */
class DiscussMgrUserGroupUpdateManagerController extends DiscussManagerController {
    /** @var disUserGroupProfile $profile */
    public $profile;
    /** @var modUserGroup $userGroup */
    public $userGroup;

    public function initialize() {
        parent::initialize();
        if (empty($this->scriptProperties['id'])) return $this->failure($this->modx->lexicon('discuss.usergroup_err_ns'));

        $this->profile = $this->modx->getObject('disUserGroupProfile',array('usergroup' => $this->scriptProperties['id']));

        if (empty($this->profile)) {
            $this->userGroup = $this->modx->getObject('modUserGroup',$this->scriptProperties['id']);
            if (empty($this->userGroup)) return $this->modx->error->failure($this->modx->lexicon('discuss.usergroup_err_nf',array('id' => $this->scriptProperties['id'])));

            $this->profile = $this->modx->newObject('disUserGroupProfile');
            $this->profile->set('usergroup',$this->scriptProperties['id']);
            $this->profile->save();
        } else {
            $this->userGroup = $this->profile->getOne('UserGroup');
        }
        return true;
    }

    public function process(array $scriptProperties = array()) {
    
    }
    
    public function getPageTitle() { return $this->modx->lexicon('discuss.usergroup').': '.$this->userGroup->get('name'); }
    public function loadCustomCssJs() {
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.boards.grid.js');
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.members.grid.js');
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.panel.js');
        $this->addLastJavascript($this->discuss->config['mgrJsUrl'].'sections/usergroup/update.js');
    }
    public function getTemplateFile() { return $this->discuss->config['templatesPath'].'usergroup/update.tpl'; }
}

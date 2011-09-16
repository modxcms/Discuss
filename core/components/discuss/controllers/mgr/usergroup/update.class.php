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

    public $userGroupArray = array();

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
        if (!$this->getUserGroup()) {
            return $this->failure($this->modx->lexicon('discuss.usergroup_err_nf'));
        }

        $this->getMembers();
        $this->getBoards();
        $this->getBadge();
        return array();
    }

    /**
     * Get the User Group
     * 
     * @return modUserGroup
     */
    public function getUserGroup() {
        $c = $this->modx->newQuery('modUserGroup');
        $c->select($this->modx->getSelectColumns('modUserGroup','modUserGroup'));
        $c->select($this->modx->getSelectColumns('disUserGroupProfile','Profile','',array(
            'post_based',
            'min_posts',
            'color',
            'image',
        )));
        $c->leftJoin('disUserGroupProfile','Profile','modUserGroup.id = Profile.usergroup');
        $c->where(array(
            'modUserGroup.id' => $this->scriptProperties['id'],
        ));
        /** @var modUserGroup $userGroup */
        $userGroup = $this->modx->getObject('modUserGroup',$c);
        if (empty($userGroup)) return false;

        $this->userGroupArray = $userGroup->toArray();
        return $this->userGroup;
    }

    /**
     * Get the members of the User Group
     * 
     * @return array
     */
    public function getMembers() {
        /* get members */
        $c = $this->modx->newQuery('modUser');
        $c->innerJoin('modUserGroupMember','UserGroupMembers');
        $c->innerJoin('modUserGroupRole','Role','UserGroupMembers.role = Role.id');
        $c->where(array(
            'UserGroupMembers.user_group' => $this->userGroup->get('id'),
        ));
        $c->select($this->modx->getSelectColumns('modUser','modUser'));
        $c->select(array(
            'role' => 'Role.id',
            'role_name' => 'Role.name',
        ));
        $members = $this->modx->getCollection('modUser',$c);
        $list = array();
        /** @var $member modUserGroupMember */
        foreach ($members as $member) {
            $list[] = array(
                $member->get('id'),
                $member->get('username'),
                $member->get('role'),
                $member->get('role_name'),
            );
        }
        $this->userGroupArray['members'] = '(' . $this->modx->toJSON($list) . ')';
        return $list;
    }

    /**
     * Get the boards of the group
     * 
     * @return array
     */
    public function getBoards() {
        $c = $this->modx->newQuery('disBoard');
        $c->innerJoin('disBoardClosure','Descendants');
        $c->leftJoin('disBoardUserGroup','UserGroups');
        $c->innerJoin('disCategory','Category');
        $c->select($this->modx->getSelectColumns('disBoard','disBoard'));
        $c->select(array(
            'IF(UserGroups.usergroup = "'.$this->userGroup->get('id').'",1,0) AS access',
            'MAX(Descendants.depth) AS depth',
            'Category.name AS category_name',
        ));
        $c->sortby('disBoard.category','ASC');
        $c->sortby('disBoard.map','ASC');
        $c->groupby('disBoard.id');
        $boards = $this->modx->getCollection('disBoard',$c);
        $list = array();
        /** @var disBoard $board */
        foreach ($boards as $board) {
            $depth = $board->get('depth') > 0 ? $board->get('depth')-1 : 0;
            $list[] = array(
                $board->get('id'),
                str_repeat('--',$depth).$board->get('name'),
                $board->get('access') ? true : false,
                $board->get('category'),
                $board->get('category_name'),
            );
        }
        $this->userGroupArray['boards'] = '(' . $this->modx->toJSON($list) . ')';
        return $list;
    }

    public function getBadge() {
        $this->userGroupArray['badge'] = '';

        /* @var disUserGroupProfile $profile */
        $profile = $this->modx->getObject('disUserGroupProfile',array(
            'usergroup' => $this->userGroup->get('id'),
        ));
        if ($profile) {
            if ($this->userGroupArray['image'] == 'Array') {
                $this->userGroupArray['image'] = '';
                $profile->set('image','');
                $profile->save();
            }
            $this->userGroupArray['badge'] = $profile->get('badge');
        }
    }

    public function getPageTitle() { return $this->modx->lexicon('discuss.usergroup').': '.$this->userGroup->get('name'); }
    public function loadCustomCssJs() {
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.boards.grid.js');
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.members.grid.js');
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'widgets/usergroup/usergroup.panel.js');
        $this->addLastJavascript($this->discuss->config['mgrJsUrl'].'sections/usergroup/update.js');
        $this->addHtml('<script type="text/javascript">
Ext.onReady(function() {
    MODx.load({
        xtype: "dis-page-usergroup-update"
        ,record: '.$this->modx->toJSON($this->userGroupArray).'
    });
});
</script>');

    }
    public function getTemplateFile() { return $this->discuss->config['templatesPath'].'usergroup/update.tpl'; }
}

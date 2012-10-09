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
 * Display form to post a new private message
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussMessagesNewController extends DiscussController {
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.message_new');
    }
    public function getSessionPlace() {
        return 'messages/new';
    }
    public function process() {
        $this->modx->lexicon->load('discuss:post');
        /* setup defaults */
        if (empty($_POST)) {
            $participants = array($this->modx->user->get('username'));
            if (!empty($this->scriptProperties['user'])) {
                $ps = explode(',',$this->scriptProperties['user']);
                $participants = array_merge($ps,$participants);
            }
            asort($participants);
            $this->setPlaceholder('participants_usernames',implode(',',array_unique($participants)));
        }
        /* set max attachment limit */
        $this->setPlaceholder('max_attachments',$this->modx->getOption('discuss.attachments_max_per_post',null,5));

        $this->modx->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('Error'));
        $this->modx->toPlaceholders($this->getPlaceholders(),'fi');
    }
    
    public function getButtons() {
        $this->setPlaceholder('buttons',$this->discuss->getChunk('disPostButtons',array('buttons_url' => $this->discuss->config['imagesUrl'].'buttons/')));
    }
    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.messages'),'url' => $this->discuss->request->makeUrl('messages'));
        $trail[] = array('text' => $this->modx->lexicon('discuss.message_new'),'active' => true);
        return $trail;
    }
}

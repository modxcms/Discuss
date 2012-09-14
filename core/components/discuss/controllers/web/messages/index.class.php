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
 * View all messages for the current user
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussMessagesController extends DiscussController {
    /** @var array $list */
    public $list = array();

    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.messages');
    }
    public function getSessionPlace() {
        return 'messages::'.$this->getProperty('page',1);
    }
    public function process() {
        $this->modx->lexicon->load('discuss:post');
        $this->getMessages();
        $this->buildPagination();
        $this->getActionButtons();
    }

    public function getMessages() {
        $limit = $this->getProperty('limit',$this->modx->getOption('discuss.threads_per_page',null,20),'empty');
        $page = $this->getProperty('page',1);
        $page = $page <= 0 ? 1 : $page;
        $start = ($page-1) * $limit;

        /* get all messages */
        $c = $this->modx->newQuery('disThread');
        $c->innerJoin('disPost','FirstPost');
        $c->innerJoin('disPost','LastPost');
        $c->innerJoin('disUser','LastAuthor');
        $c->innerJoin('disUser','FirstAuthor');
        $c->innerJoin('disThreadUser','Users');
        $c->leftJoin('disThreadRead','Reads','Reads.user = '.$this->discuss->user->get('id').' AND disThread.id = Reads.thread');
        $c->where(array(
            'disThread.private' => true,
            'Users.user' => $this->discuss->user->get('id'),
        ));
        $total = $this->modx->getCount('disThread',$c);
        $c->select($this->modx->getSelectColumns('disPost','LastPost'));
        $c->select(array(
            'disThread.id',
            'disThread.replies',
            'disThread.views',
            'disThread.sticky',
            'disThread.locked',
            'FirstPost.title',
            'LastPost.id AS post_id',
            'LastPost.author AS author',
            'LastAuthor.username AS author_username',
            'FirstAuthor.id AS author_first',
            'FirstAuthor.username AS author_first_username',
            'Reads.thread AS viewed',
        ));
        $c->sortby('LastPost.createdon','DESC');
        $c->limit($limit,$start);
        $messages = $this->modx->getCollection('disThread',$c);

        $canViewProfiles = $this->modx->hasPermission('discuss.view_profiles');
        $this->list = array('results' => array(),'limit' => $limit,'start' => $start,'total' => $total);
        $idx = 0;
        /** @var disThread $message */
        foreach ($messages as $message) {
            $message->buildIcons();
            $message->buildCssClass('board-post');
            $threadArray = $message->toArray();
            $threadArray['idx'] = $idx;
            $threadArray['createdon'] = strftime($this->discuss->dateFormat,strtotime($threadArray['createdon']));

            $threadArray['author_link'] = $canViewProfiles ? '<a href="'.$this->discuss->url.'user/?user='.$threadArray['author'].'">'.$threadArray['author_username'].'</a>' : $threadArray['author_username'];
            $threadArray['views'] = number_format($threadArray['views']);
            $threadArray['replies'] = number_format($threadArray['replies']);
            $threadArray['read'] = 1;
            $threadArray['title'] = str_replace(array('[',']'),array('&#91;','&#93;'),$threadArray['title']);
            $threadArray['idx'] = $idx+1;

            $threadArray['unread'] = false;
            $threadArray['unread-cls'] = 'dis-post-read';
            if (!$threadArray['viewed'] && $this->discuss->user->isLoggedIn) {
                $threadArray['unread'] = true;
                $threadArray['unread-cls'] = 'dis-post-read';
            }

            $this->list['results'][] = $this->discuss->getChunk('message/disMessageLi',$threadArray);
            $idx++;
        }
        $this->setPlaceholder('messages',implode("\n",$this->list['results']));
        $this->setPlaceholder('total',$total);

    }

    public function getActionButtons() {
        $actionButtons = array();
        if ($this->modx->hasPermission('discuss.pm_send') && $this->discuss->user->isLoggedIn) {
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('messages/new'), 'text' => $this->modx->lexicon('discuss.message_new'), 'cls' => 'dis-action-message_new');
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('messages',array('read' => 1)), 'text' => $this->modx->lexicon('discuss.mark_all_as_read'), 'cls' => 'dis-action-mark_all_as_read');
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function buildPagination() {
        $this->discuss->hooks->load('pagination/build',array(
            'count' => $this->list['total'],
            'id' => 0,
            'view' => 'messages',
            'limit' => $this->list['limit'],
            'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
        ));
    }

    public function handleActions() {
        if (!empty($this->scriptProperties['read']) && $this->discuss->user->isLoggedIn) {
            $this->modx->call('disThread','readAll',array(&$this->modx,'message'));
        }
    }

    public function getBreadcrumbs() {
        $trail = array();
        $trail[] = array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        );
        $trail[] = array('text' => $this->modx->lexicon('discuss.messages').' ('.number_format($this->list['total']).')','active' => true);
        return $trail;

    }
}

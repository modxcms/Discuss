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
 * Reply to a current post
 *
 * @package discuss
 */
class DiscussMessagesReplyController extends DiscussController {
    /** @var disThread $thread */
    public $thread;
    /** @var disPost $post */
    public $post;
    /** @var disUser $author */
    public $author;

    public function initialize() {
        if (empty($this->scriptProperties['thread']) && empty($this->scriptProperties['post'])) $this->modx->sendErrorPage();
        if (!empty($this->scriptProperties['post']) && empty($this->scriptProperties['thread'])) {
            $this->post = $this->modx->getObject('disPost',$this->scriptProperties['post']);
            if (empty($this->post)) $this->discuss->sendErrorPage();
            $this->scriptProperties['thread'] = $this->post->get('thread');
        }
        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$this->scriptProperties['thread'],disThread::TYPE_MESSAGE));
        if (empty($this->thread)) $this->modx->sendErrorPage();
        if (empty($this->post)) {
            $this->post = $this->thread->getOne('FirstPost');
            if (empty($this->post)) $this->discuss->sendErrorPage();
        }
        
        $this->author = $this->post->getOne('Author');
        $this->modx->lexicon->load('discuss:post');
    }
    public function checkPermissions() {
        $users = explode(',',$this->thread->get('users'));
        $inMessage = in_array($this->discuss->user->get('id'),$users);
        return $inMessage && $this->discuss->user->isLoggedIn;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.reply_to_post',array('title' => $this->post->get('title')));
    }
    public function getSessionPlace() {
        return 'messages/reply';
    }
    public function process() {
        /* setup default snippet properties */
        $replyPrefix = $this->modx->getOption('replyPrefix',$this->scriptProperties,'Re: ');

        /* setup placeholders */
        $postArray = $this->post->toArray();
        $postArray['participants_usernames'] = $this->thread->get('participants_usernames');
        $postArray['post'] = $postArray['id'];
        $postArray['thread'] = $this->thread->get('id');
        $postArray['is_author'] = ($this->thread->get('author_first') == $this->discuss->user->get('id')) ? true : false;
        $this->setPlaceholders($postArray);

        $this->getThreadSummary();

        /* default values */
        if (empty($_POST)) {
            $this->setPlaceholder('title',$replyPrefix.$this->post->get('title'));
        }

        $this->handleQuote();
        $this->handleAttachments();

        /* output form to browser */
        $this->modx->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('disError'));
        $this->modx->setPlaceholders($this->getPlaceholders(),'fi.');
    }

    public function handleQuote() {
        if (empty($_POST) && !empty($this->scriptProperties['quote'])) {
            $message = str_replace(array('[',']'),array('&#91;','&#93;'),$this->post->br2nl($this->post->get('message')));
            $message = '[quote author='.$this->author->get('username').' date='.strtotime($this->post->get('createdon')).']'.$message.'[/quote]'."\n";
            $this->setPlaceholder('message',$message);
            
        } elseif (empty($_POST) && empty($this->scriptProperties['quote'])) {
            $this->setPlaceholder('message','');
        }
    }

    public function handleAttachments() {
        /* set max attachment limit */
        $this->setPlaceholder('max_attachments',$this->modx->getOption('discuss.attachments_max_per_post',null,5));
        if ($this->thread->canPostAttachments()) {
            $this->setPlaceholder('attachment_fields',$this->discuss->getChunk('post/disAttachmentFields',$this->getPlaceholders()));
        } else {
            $this->setPlaceholder('attachment_fields','');
        }
        $this->modx->regClientHTMLBlock('<script type="text/javascript">
        DIS.config.attachments_max_per_post = '.$this->getPlaceholder('max_attachments',5).';
        </script>');
    }

    public function getThreadSummary() {
        $thread = $this->discuss->hooks->load('post/getthread',array(
            'post' => &$this->post,
            'controller' => &$this,
            'thread' => &$this->thread,
            'limit' => 10,
        ));
        $this->setPlaceholder('thread_posts',$thread['results']);
    }

    public function getButtons() {
        $this->setPlaceholder('buttons',$this->discuss->getChunk('disPostButtons',array('buttons_url' => $this->discuss->config['imagesUrl'].'buttons/')));
    }
    public function getBreadcrumbs() {
        $trail = array(array(
            'url' => $this->discuss->request->makeUrl(),
            'text' => $this->modx->getOption('discuss.forum_title'),
        ),array(
            'text' => $this->modx->lexicon('discuss.messages'),
            'url' => $this->discuss->request->makeUrl(array('action' => 'messages')),
        ),array(
            'text' => $this->post->get('title'),
            'url' => $this->discuss->request->makeUrl(array('action' => 'messages', 'messages' => 'view'),array('thread' => $this->thread->get('id'))),
        ),array(
            'text' => $this->modx->lexicon('discuss.reply'),
            'active' => true,
        ));
        return $trail;
    }
}

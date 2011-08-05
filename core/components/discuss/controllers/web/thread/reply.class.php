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
 * @subpackage controllers
 */
class DiscussThreadReplyController extends DiscussController {
    /** @var disThread $thread */
    public $thread;
    /** @var disPost $post */
    public $post;
    /** @var disUser $author */
    public $author;
    /** @var disBoard $board */
    public $board;

    public function initialize() {
        $thread = $this->getProperty('thread',false);
        if (empty($thread)) {
            $post = $this->getProperty('post',false);
            if (empty($post)) { $this->modx->sendErrorPage(); }
            $this->post = $this->modx->getObject('disPost',$post);
            if (empty($this->post)) $this->discuss->sendErrorPage();
        
            /* get thread root */
            $this->thread = $this->post->getOne('Thread');
            if (empty($this->thread)) $this->discuss->sendErrorPage();
        } else {
            $this->thread = $this->modx->getObject('disThread',$thread);
            if (empty($this->thread)) $this->discuss->sendErrorPage();
            $this->post = $this->thread->getOne('FirstPost');
            if (empty($this->post)) $this->discuss->sendErrorPage();
        }

        $this->author = $this->post->getOne('Author');
        $this->board = $this->thread->getOne('Board');
        $this->modx->lexicon->load('discuss:post');
        var_dump($_POST);
    }
    
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.reply_to_post',array('title' => $this->post->get('title')));
    }

    public function checkPermissions() {
        return $this->post->canReply();
    }

    public function getSessionPlace() {
        return 'thread-reply:'.$this->thread->get('id');
    }

    public function process() {
        $replyPrefix = $this->modx->getOption('replyPrefix',$this->scriptProperties,'Re: ');

        $this->setPlaceholders($this->post->toArray());
        $this->setPlaceholders(array(
            'url' => $this->post->getUrl(),
            'buttons' => $this->discuss->getChunk('disPostButtons',array('buttons_url' => $this->discuss->config['imagesUrl'].'buttons/')),
            'post' => $this->post->get('id'),
            'thread' => $this->thread->get('id'),
            'title' => $replyPrefix.str_replace($replyPrefix,'',$this->post->get('title')),
        ));

        $this->getThreadSummary();
        $this->checkThreadPermissions();

        $this->handleAttachments();
        $this->handleQuote();
        
        $this->modx->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('disError'));
        /* set placeholders for FormIt inputs */
        $this->modx->setPlaceholders($this->getPlaceholders(),'fi.');
    }

    /**
     * Hide or show inputs on the form depending on whether the user has the correct Thread permissions
     * @return void
     */
    public function checkThreadPermissions() {
        if ($this->thread->canLock()) {
            $locked = !empty($_POST['locked']) ? ' checked="checked"' : '';
            $this->setPlaceholders(array(
                'locked' => $locked,
                'locked_cb' => '<label class="dis-cb"><input type="checkbox" name="locked" value="1" '.$locked.' />'.$this->modx->lexicon('discuss.thread_lock').'</label>',
                'can_lock' => true,
            ));
        }
        if ($this->thread->canStick()) {
            $sticky = !empty($_POST['sticky']) ? ' checked="checked"' : '';
            $this->setPlaceholders(array(
                'sticky' => $sticky,
                'sticky_cb' => '<label class="dis-cb"><input type="checkbox" name="sticky" value="1" '.$sticky.' />'.$this->modx->lexicon('discuss.thread_stick').'</label>',
                'can_stick' => true,
            ));
        }
    }

    /**
     * Get the thread summary to display below the reply form
     * @return void
     */
    public function getThreadSummary() {
        $threadData = $this->discuss->hooks->load('post/getthread',array(
            'post' => &$this->post,
            'thread' => $this->post->get('thread'),
            'limit' => 5,
        ));
        $this->setPlaceholder('thread_posts',$threadData['results']);
    }

    public function getBreadcrumbs() {
        return $this->board->buildBreadcrumbs(array(array(
            'text' => $this->modx->lexicon('discuss.reply_to_post',array(
                'post' => '<a class="active" href="'.$this->discuss->url.'thread?thread='.$this->thread->get('id').'">'.$this->post->get('title').'</a>',
            )),
            'active' => true,
        )),true);
    }

    /**
     * Handle Quote functionality that will append the quoted post into the initial message
     * @return void
     */
    public function handleQuote() {
        if (empty($_POST) && !empty($this->scriptProperties['quote'])) {
            $message = str_replace(array('[',']'),array('&#91;','&#93;'),$this->post->br2nl($this->post->get('message')));
            $message = '[quote author='.$this->author->get('username').' date='.strtotime($this->post->get('createdon')).']'.$message.'[/quote]'."\n";
            $this->setPlaceholder('message',$message);
        } elseif (empty($_POST) && empty($this->scriptProperties['quote'])) {
            $this->setPlaceholder('message','');
        }
    }

    /**
     * Handle the rendering and options of attachments being sent to the form
     * @return void
     */
    public function handleAttachments() {
        $this->setPlaceholder('max_attachments',$this->modx->getOption('discuss.attachments_max_per_post',null,5));
        if ($this->thread->canPostAttachments()) {
            $this->setPlaceholder('attachment_fields',$this->discuss->getChunk('post/disAttachmentFields',$this->getPlaceholders()));
        } else {
            $this->setPlaceholder('attachment_fields','');
        }
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
        $(function() { DIS.config.attachments_max_per_post = '.$this->getPlaceholder('max_attachments').'; });
        </script>');
    }
}
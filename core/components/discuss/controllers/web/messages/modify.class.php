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
 * Render the Modify Private Message form
 * 
 * @package discuss
 * @subpackage controllers
 */
class DiscussMessagesModifyController extends DiscussController {
    /** @var disPost $post */
    public $post;
    /** @var disThread $thread */
    public $thread;

    public function initialize() {
        $post = $this->getProperty('post',false);
        if (empty($post)) { $this->modx->sendErrorPage(); }
        $this->post = $this->modx->getObject('disPost',$post);
        if (empty($this->post)) { $this->discuss->sendErrorPage(); }

        $this->discuss->setPageTitle($this->modx->lexicon('discuss.modify_post_header',array('title' => $this->post->get('title'))));

        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$this->post->get('thread'),disThread::TYPE_MESSAGE));
        if (empty($this->thread)) $this->discuss->sendErrorPage();
        $this->modx->lexicon->load('discuss:post');
    }
    
    public function checkPermissions() {
        /* ensure user is IN this PM */
        $users = explode(',',$this->thread->get('users'));
        $inMessage = in_array($this->discuss->user->get('id'),$users);
        return $this->discuss->user->isLoggedIn && $inMessage;
    }
    public function getPageTitle() {
        return $this->modx->lexicon('discuss.message_modify');
    }
    public function getSessionPlace() {
        return 'messages/modify:post='.$this->post->get('id');
    }
    public function process() {
        $this->setPlaceholders($this->post->toArray());
        $this->setPlaceholders(array(
            'post' => $this->post->get('id'),
            'participants_usernames' => $this->thread->get('participants_usernames'),
            'thread' => $this->thread->get('id'),
            'message' => str_replace(array('[',']'),array('&#91;','&#93;'),$this->post->br2nl($this->post->get('message'))),
        ));

        $this->getAttachments();
        $this->getButtons();
        $this->getThreadSummary();

        /* output form to browser */
        $this->modx->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('disError'));
        $this->modx->setPlaceholders($this->getPlaceholders(),'fi.');

    }

    public function getThreadSummary() {
        $thread = $this->discuss->hooks->load('post/getthread',array(
            'post' => &$this->post,
            'controller' => &$this,
            'thread' => $this->post->get('thread'),
            'limit' => 5,
        ));
        $this->setPlaceholder('thread_posts',$thread['results']);
    }

    public function getAttachments() {

        $attachments = $this->post->getMany('Attachments');
        $idx = 1;
        $atts = array();
        $postAttachmentRowTpl = $this->modx->getOption('postAttachmentRowTpl',$this->scriptProperties,'post/disPostEditAttachment');
        foreach ($attachments as $attachment) {
            $attachmentArray = $attachment->toArray();
            $attachmentArray['filesize'] = $attachment->convert();
            $attachmentArray['url'] = $attachment->getUrl();
            $attachmentArray['idx'] = $idx;
            $atts[] = $this->discuss->getChunk($postAttachmentRowTpl,$attachmentArray);
            $idx++;
        }
        $placeholders['attachments'] = implode("\n",$atts);
        $placeholders['max_attachments'] = $this->modx->getOption('discuss.attachments_max_per_post',null,5);
        $placeholders['attachmentCurIdx'] = count($attachments)+1;
        $this->modx->regClientHTMLBlock('<script type="text/javascript">
            DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].';
            DIS.DISModifyMessage.init({
                attachments: '.(count($attachments)+1).'
            });
        </script>');

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
            'url' => $this->discuss->request->makeUrl('messages'),
        ),array(
            'text' => $this->post->get('title'),
            'url' => $this->discuss->request->makeUrl('messages/view',array('thread' => $this->thread->get('id'))),
        ),array(
            'text' => $this->modx->lexicon('discuss.modify'),
            'active' => true,
        ));
        return $trail;
    }
}

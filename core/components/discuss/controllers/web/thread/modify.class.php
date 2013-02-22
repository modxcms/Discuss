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
 * Modify Thread page
 * 
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadModifyController extends DiscussController {
    /** @var disPost $post */
    public $post;
    /** @var disThread $thread */
    public $thread;
    /** @var disBoard $board */
    public $board;

    public function getDefaultOptions() {
        return array(
            'textCheckboxLocked' => $this->modx->lexicon('discuss.thread_lock'),
            'textCheckboxSticky' => $this->modx->lexicon('discuss.thread_stick'),
            'textCheckboxNotify' => $this->modx->lexicon('discuss.subscribe_by_email'),
        );
    }

    public function getPageTitle() {
        return $this->modx->lexicon('discuss.modify_post_header',array('title' => $this->post->get('title')));
    }

    public function getSessionPlace() {
        return '';
    }

    public function initialize() {
        $post = $this->getProperty('post',false);
        if (empty($post)) { $this->discuss->sendErrorPage(); }

        $this->post = $this->modx->getObject('disPost',$post);
        if (empty($this->post)) { $this->discuss->sendErrorPage(); }

        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$this->post->get('thread')));
        if (empty($this->thread)) $this->discuss->sendErrorPage();

        $this->modx->lexicon->load('discuss:post');
    }

    public function process() {
        /* setup defaults */
        $placeholders = $this->post->toArray();
        $placeholders['url'] = $this->post->getUrl();
        $placeholders['post'] = $this->post->get('id');
        $placeholders['buttons'] = $this->discuss->getChunk('disPostButtons',array('buttons_url' => $this->discuss->config['imagesUrl'].'buttons/'));
        $placeholders['message'] = str_replace(array('[',']','<','>'),array('&#91;','&#93;','&lt;','&gt;'),$placeholders['message']);

        /* get thread root */
        $placeholders['thread'] = $this->thread->get('id');
        $placeholders['locked'] = $this->thread->get('locked');
        $placeholders['sticky'] = $this->thread->get('sticky');
        $placeholders['class_key'] = $this->thread->get('class_key');
        $placeholders['is_root'] = $this->thread->get('post_first') == $this->post->get('id') ? 1 : 0;

        /* ensure user can modify this post */
        $isModerator = $this->discuss->user->isGlobalModerator() || $this->thread->isModerator($this->discuss->user->get('id')) || $this->discuss->user->isAdmin();
        $canModifyPost = $this->discuss->user->isLoggedIn && $this->modx->hasPermission('discuss.thread_modify');
        $canModify = $this->discuss->user->get('id') == $this->post->get('author') || ($isModerator && $canModifyPost);
        if (!$canModify) {
            $this->modx->sendRedirect($this->thread->getUrl());
        }

        /* get attachments for post */
        $attachments = $this->post->getMany('Attachments');
        $idx = 1;
        $atts = array();
        $postAttachmentRowTpl = $this->modx->getOption('postAttachmentRowTpl',$this->scriptProperties,'post/disPostEditAttachment');
        /** @var disPostAttachment $attachment */
        foreach ($attachments as $attachment) {
            $attachmentArray = $attachment->toArray();
            $attachmentArray['filesize'] = $attachment->convert();
            $attachmentArray['url'] = $attachment->getUrl();
            $attachmentArray['idx'] = $idx;
            $atts[] = $this->discuss->getChunk($postAttachmentRowTpl,$attachmentArray);
            $idx++;
        }

        /* attachments */
        $placeholders['attachment_fields'] = '';
        $placeholders['attachments'] = implode("\n",$atts);
        $placeholders['max_attachments'] = $this->modx->getOption('discuss.attachments_max_per_post',null,5);
        $placeholders['attachmentCurIdx'] = count($attachments)+1;
        if ($this->thread->canPostAttachments()) {
            $placeholders['attachment_fields'] = $this->discuss->getChunk('post/disAttachmentFields',$placeholders);
        }

        /* perms */
        if($placeholders['is_root']) {
            if ($this->thread->canLock() || $this->thread->canUnlock()) {
                $checked = !empty($_POST) ? !empty($_POST['locked']) : $this->thread->get('locked');
                $placeholders['locked'] = $checked ? ' checked="checked"' : '';
                $placeholders['locked_cb'] = $this->discuss->getChunk('form/disCheckbox',array(
                    'name' => 'locked',
                    'value' => 1,
                    'text' => $this->getOption('textCheckboxLocked'),
                    'attributes' => $placeholders['locked'],
                ));
                $placeholders['can_lock'] = true;
            }
            if ($this->thread->canStick() || $this->thread->canUnstick()) {
                $checked = !empty($_POST) ? !empty($_POST['sticky']) : $this->thread->get('sticky');
                $placeholders['sticky'] = $checked ? ' checked="checked"' : '';
                $placeholders['sticky_cb'] = $this->discuss->getChunk('form/disCheckbox',array(
                    'name' => 'sticky',
                    'value' => 1,
                    'text' => $this->getOption('textCheckboxSticky'),
                    'attributes' => $placeholders['sticky'],
                ));
                $placeholders['can_stick'] = true;
            }
        }
        /* If the editing user is the author, show him the subscribe checkbox */
        if($this->discuss->user->get('id') == $this->post->get('author')) {
            $checked = !empty($_POST) ? !empty($_POST['notify']) : $this->thread->hasSubscription();
            $placeholders['notify']     = $checked ? ' checked="checked"' : '';
            $placeholders['notify_cb']  = $this->discuss->getChunk('form/disCheckbox',array(
                'name' => 'notify',
                'value' => 1,
                'text' => $this->getOption('textCheckboxNotify'),
                'attributes' => $placeholders['notify'],
            ));
            $placeholders['can_subscribe'] = true;
        }

        $this->getThreadSummary();

        /* output form to browser */
        $this->modx->regClientHTMLBlock('<script type="text/javascript">
            DIS.config.attachments_max_per_post = '.$placeholders['max_attachments'].';
            DIS.DISModifyPost.init({
                attachments: '.(count($attachments)+1).'
            });
        </script>');
        $this->modx->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('disError'));
        $this->modx->setPlaceholders($placeholders,'fi.');
        $this->setPlaceholders($placeholders);
    }

    public function getThreadSummary() {
        $threadData = $this->discuss->hooks->load('post/getthread',array(
            'post' => &$this->post,
            'controller' => &$this,
            'thread' => $this->post->get('thread'),
            'limit' => 5,
        ));
        $this->setPlaceholder('thread_posts',$threadData['results']);
    }

    public function getBreadcrumbs() {
        /* build breadcrumbs */
        $trail = '';
        if (empty($this->board)) {
            $this->board = $this->thread->getOne('Board');
        }
        if ($this->board) {
            $default = array();
            if (!empty($this->options['showTitleInBreadcrumbs'])) {
                $default[] = array(
                    'text' => $this->modx->lexicon('discuss.modify_post_header',array(
                        'post' => $this->post->get('title'),
                    )),
                    'active' => true,
                );
            }

            $this->board->buildBreadcrumbs($default,true);
            $trail = $this->board->get('trail');
        }
        return $trail;
    }
}

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
 * Display form to post a new thread
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadNewController extends DiscussController {
    /** @var disBoard $board */
    public $board;

    public function initialize() {
        $board = $this->getProperty('board',false);
        if (empty($board)) { $this->modx->sendErrorPage(); }
        $this->board = $this->modx->getObject('disBoard',$this->scriptProperties['board']);
        if (empty($this->board)) $this->discuss->sendErrorPage();

        $this->options = array_merge(array(
            'tplAttachmentFields' => 'post/disAttachmentFields',
            'textBreadcrumbsThreadNew' => $this->modx->lexicon('discuss.thread_new'),
            'textCheckboxLocked' => $this->modx->lexicon('discuss.thread_lock'),
            'textCheckboxSticky' => $this->modx->lexicon('discuss.thread_stick'),
        ),$this->options);
        
        $this->modx->lexicon->load('discuss:post');
    }

    public function getPageTitle() {
        return $this->modx->lexicon('discuss.thread_new');
    }

    public function checkPermissions() {
        return $this->board->canPost();
    }

    public function getSessionPlace() {
        return 'thread-new:'.$this->board->get('id');
    }

    public function process() {
        /* setup defaults */
        $this->setPlaceholders($this->board->toArray());

        $this->getButtons();
        $this->checkThreadPermissions();
        $this->handleAttachments();

        $this->modx->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('Error'));
        /* set placeholders for FormIt inputs */
        $this->modx->setPlaceholders($this->getPlaceholders(),'fi.');
    }

    public function getButtons() {
        $this->setPlaceholder('buttons',$this->discuss->getChunk('disPostButtons',array('buttons_url' => $this->discuss->config['imagesUrl'].'buttons/')));
    }

    
    public function getBreadcrumbs() {
        return $this->board->buildBreadcrumbs(array(array(
            'text' => $this->getOption('textBreadcrumbsThreadNew'),
            'active' => true,
        )),true);
    }

    /**
     * Hide or show inputs on the form depending on whether the user has the correct Thread permissions
     * @return void
     */
    public function checkThreadPermissions() {
        if ($this->board->canPostLockedThread()) {
            $locked = !empty($_POST['locked']) ? ' checked="checked"' : '';
            $this->setPlaceholders(array(
                'locked' => $locked,
                'locked_cb' => $this->discuss->getChunk('form/disCheckbox',array(
                    'name' => 'locked',
                    'value' => 1,
                    'text' => $this->getOption('textCheckboxLocked'),
                    'attributes' => $locked,
                )),
                'can_lock' => true,
            ));
        }
        if ($this->board->canPostStickyThread()) {
            $sticky = !empty($this->scriptProperties['sticky']) ? ' checked="checked"' : '';
            $this->setPlaceholders(array(
                'sticky' => $sticky,
                'sticky_cb' => $this->discuss->getChunk('form/disCheckbox',array(
                    'name' => 'sticky',
                    'value' => 1,
                    'text' => $this->getOption('textCheckboxSticky'),
                    'attributes' => $sticky,
                )),
                'can_stick' => true,
            ));
        }
    }
    
    /**
     * Handle the rendering and options of attachments being sent to the form
     * @return void
     */
    public function handleAttachments() {
        $this->setPlaceholder('max_attachments',$this->modx->getOption('discuss.attachments_max_per_post',null,5));
        $this->modx->regClientStartupHTMLBlock('<script type="text/javascript">
        $(function() { DIS.config.attachments_max_per_post = '.$this->getPlaceholder('max_attachments').'; });
        </script>');
        if ($this->board->canPostAttachments()) {
            $this->setPlaceholders(array(
                'attachments' => '',
                'attachmentCurIdx' => 1,
            ));
            $this->setPlaceholder('attachment_fields',$this->discuss->getChunk($this->getOption('tplAttachmentFields'),$this->getPlaceholders()));
        } else {
            $this->setPlaceholders(array(
                'attachment_fields' => '',
                'attachments' => '',
                'attachmentCurIdx' => 1,
            ));
        }
    }
}
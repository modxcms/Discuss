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
 * Display a thread of posts
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadController extends DiscussController {
    /** @var disThread $thread */
    public $thread;
    /** @var disBoard $board */
    public $board;
    /** @var array $posts */
    public $posts = array();
    /** @var boolean $isModerator */
    public $isModerator = false;
    /** @var boolean $isAdmin */
    public $isAdmin = false;
    /** @var boolean $isAuthor */
    public $isAuthor = false;
    /** @var disPost $lastPost */
    public $lastPost;
    /** @var array $answerPosts */
    public $answerPosts = array();

    public function initialize() {
        $integrated = $this->modx->getOption('i',$this->scriptProperties,false);
        if (!empty($integrated)) $integrated = true;
        $thread = $this->modx->getOption('thread',$this->scriptProperties,false);
        if (empty($thread)) $this->discuss->sendErrorPage();
        $this->thread = $this->modx->call('disThread', 'fetch', array(&$this->modx,$thread,'post',$integrated));
        if (empty($this->thread)) {
            if ($this->discuss->user->isLoggedIn) $this->discuss->sendErrorPage();
            else $this->discuss->sendUnauthorizedPage();
        }

        $this->isModerator = $this->thread->isModerator();
        $this->isAdmin = $this->discuss->user->isAdmin();

        $this->isAuthor = ($this->discuss->user->isLoggedIn && ($this->thread->get('author_first') == $this->discuss->user->get('id')));
        $this->setPlaceholder('discuss.user.isAuthor', $this->isAuthor);
        $canMarkAsAnswer = (($this->isAuthor || $this->isModerator || $this->isAdmin) &&
            ($this->thread->get('class_key') == 'disThreadQuestion') &&
            ($this->thread->get('replies') > 0) &&
            (!$this->thread->get('answered'))
        );
        $this->setPlaceholder('discuss.user.shouldMarkAnAnswer', ($canMarkAsAnswer) ? '1' : '0');

        $this->board = $this->thread->getOne('Board');
        if ($this->board) {
            $isModerator = $this->discuss->user->isModerator($this->board->get('id'));
            $this->setPlaceholder('discuss.user.isModerator',$isModerator);
        }
        $this->modx->lexicon->load('discuss:post');
    }

    /**
     * @return string
     */
    public function getPageTitle() {
        return $this->thread->get('title');
    }

    /**
     * @return string
     */
    public function getSessionPlace() {
        return 'thread:'. ( ($this->thread instanceof disThread) ? $this->thread->get('id') : $this->getProperty('thread') ).':'.$this->getProperty('page',1);
    }

    public function process() {
        /* up view count and mark read */
        $this->view();
        $this->markRead();
        
        /* get posts */
        $this->getAnswers();
        $this->getPosts();
        $this->getLastPost();

        $this->setPlaceholder('rtl',$this->board->get('rtl'));

        $threadArray = $this->thread->toArray('',true,true);
        $this->setPlaceholders($threadArray);
        $this->setPlaceholder('title',$this->thread->get('title'));
        $this->setPlaceholder('title_value',$threadArray['title']);
        $this->setPlaceholder('views',number_format($this->getPlaceholder('views',1)));
        $this->setPlaceholder('replies',number_format($this->getPlaceholder('replies',0)));
        $this->setPlaceholder('url',$this->thread->getUrl(false, array(), true));

        /* set css class of thread */
        $this->thread->buildCssClass();
        $this->getQuickReplyForm();

        if ($this->discuss->user->isLoggedIn && empty($this->scriptProperties['print'])) {
            $this->getActionButtons();
        }
        if ($this->discuss->user->isLoggedIn && ($this->isModerator || $this->isAdmin) && empty($this->scriptProperties['print'])) {
            $this->getModeratorActionButtons();
        }


        /* output */
        $this->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('Error'));
        $this->setPlaceholder('discuss.thread',$this->thread->get('title'));

        $this->buildPagination();
        $this->getViewing();
        $this->fireOnRenderThread();
    }

    /**
     * Get the Quick Reply form
     * @return string
     */
    public function getQuickReplyForm() {
        $form = '';
        if ($this->canQuickReply() && empty($this->scriptProperties['print'])) {
            $this->handleAttachments();
            $this->getQuickReplyButtons();
            $phs = $this->getPlaceholders();
            $phs['view'] = 'thread/reply';
            $phs['title_value'] = htmlentities($phs['title_value'], ENT_QUOTES, 'UTF-8');
            $phs['subscribed'] = $this->thread->hasSubscription() ? ' checked="checked"' : '';
            $form = $this->discuss->getChunk('post/disQuickReply',$phs);
        }
        $this->setPlaceholder('quick_reply_form',$form);
        return $form;
    }

    /**
     * Check to see if user has access to quick reply
     * @return boolean
     */
    public function canQuickReply() {
        $canReply = $this->thread->canReply() && $this->discuss->user->isLoggedIn;
        $this->setPlaceholder('can_reply',$canReply);
        return $canReply;
    }

    /**
     * Loads the quick reply wysiwyg buttons for the form
     * @return string
     */
    public function getQuickReplyButtons() {
        $buttonsTpl = $this->getOption('buttonsTpl','disPostButtons');
        $buttons = $this->discuss->getChunk($buttonsTpl,array('buttons_url' => $this->discuss->config['imagesUrl'].'buttons/'));
        $this->setPlaceholder('reply_buttons',$buttons);
        return $buttons;
    }

    /**
     * Get the last post of the thread and set it as a placeholder
     * @return void
     */
    public function getLastPost() {
        $this->lastPost = $this->thread->getOne('LastPost');
        $lastPostArray = $this->lastPost->toArray('lastPost.');
        $this->setPlaceholders($lastPostArray);
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
        $this->modx->regClientHTMLBlock('<script type="text/javascript">
        DIS.config.attachments_max_per_post = '.$this->getPlaceholder('max_attachments').';
        </script>');
    }

    /**
     * Get all posts for the thread
     * @return void
     */
    public function getPosts() {
        $this->posts = array('total' => 0,'limit' => 0);
        if (!empty($this->options['showPosts'])) {
            $options = array_merge($this->options,array(
                'thread' => &$this->thread,
                'controller' => &$this,
                'answers' => $this->answerPosts
            ));
            $this->posts = $this->discuss->hooks->load('post/getThread',$options);
            $this->setPlaceholder('posts',$this->posts['results']);
        }
    }

    public function fireOnRenderThread() {
        /* Render thread event */
        $placeholders = $this->getPlaceholders();
        $placeholders['top'] = '';
        $placeholders['bottom'] = '';
        $placeholders['aboveThread'] = '';
        $placeholders['belowThread'] = '';
        $placeholders = $this->discuss->invokeRenderEvent('OnDiscussRenderThread',$placeholders);
        $this->setPlaceholders($placeholders);
    }

    public function handleActions() {
        $this->thread->handleThreadViewActions($this->scriptProperties);
    }

    /**
     * Get the actively viewing users for this Thread
     * @return void
     */
    public function getViewing() {
        if (!empty($this->options['showViewing']) && empty($this->scriptProperties['print'])) {
            $this->setPlaceholder('readers',empty($this->scriptProperties['print']) ? $this->thread->getViewing() : '');
        }
    }

    /**
     * Mark the thread as read for the active user
     * @return void
     */
    public function markRead() {
        if (empty($this->scriptProperties['print'])) {
            $this->thread->read($this->discuss->user->get('id'));
        }
    }

    /**
     * Up view count for thread
     * @return void
     */
    public function view() {
        if (empty($this->scriptProperties['print'])) {
            $this->thread->view();
        }
    }

    /**
     * @return string
     */
    public function getBreadcrumbs() {
        if (!empty($this->options['showBreadcrumbs']) && empty($this->scriptProperties['print'])) {
            return $this->thread->buildBreadcrumbs(array(),$this->options['showTitleInBreadcrumbs']);
        }
        return '';
    }

    public function buildPagination() {
        if (empty($this->scriptProperties['print'])) {
            $this->discuss->hooks->load('pagination/build',array_merge(array(
                'count' => $this->posts['total'],
                'id' => $this->thread->get('id'),
                'view' => 'thread/',
                'limit' => $this->posts['limit'],
                'showPaginationIfOnePage' => $this->getOption('showPaginationIfOnePage',true,'isset'),
            ),$this->options));
        }
    }

    public function getActionButtons() {
        /* @var array $actionButtons Thread action buttons */
        $actionButtons = array();
        if ($this->board->canPost() && $this->thread->canReply()) {
            $this->setPlaceholder('actionlink_reply', $this->discuss->request->makeUrl('thread/reply',array('thread' => $this->thread->get('id'))));
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/reply',array('thread' => $this->thread->get('id'))), 'text' => $this->modx->lexicon('discuss.reply_to_thread'),'cls' => 'dis-action-reply dis-action-reply_to-thread');
        }
        $this->setPlaceholder('actionlink_unread',$this->thread->getUrl(false,array('unread' => 1)));
        $actionButtons[] = array('url' => $this->thread->getUrl(false,array('unread' => 1)), 'text' => $this->modx->lexicon('discuss.mark_unread'));
        $this->setPlaceholder('actionlink_subscribe', '');
        $this->setPlaceholder('actionlink_unsubscribe', '');
        if ($this->thread->canUnsubscribe()) {
            if (!empty($this->options['showSubscribeOption'])) {
                $actionButtons[] = array('url' => $this->thread->getUrl(false,array('unsubscribe' => 1)), 'text' => $this->modx->lexicon('discuss.unsubscribe'),'cls' => 'dis-action-unsubscribe');
                $this->setPlaceholder('actionlink_unsubscribe',$this->thread->getUrl(false,array('unsubscribe' => 1)));
            }
            $this->setPlaceholder('subscribed',true);
            $this->setPlaceholder('unsubscribeUrl',$this->thread->getUrl(false,array('unsubscribe' => 1)));
        } elseif ($this->thread->canSubscribe()) {
            if (!empty($this->options['showSubscribeOption'])) {
                $actionButtons[] = array('url' => $this->thread->getUrl(false,array('subscribe' => 1)), 'text' => $this->modx->lexicon('discuss.subscribe'),'cls' => 'dis-action-subscribe');
                $this->setPlaceholder('actionlink_subscribe',$this->thread->getUrl(false,array('subscribe' => 1)));
            }
            $this->setPlaceholder('subscribed',false);
            $this->setPlaceholder('subscribeUrl',$this->thread->getUrl(false,array('subscribe' => 1)));
        }
        /* TODO: Send thread by email - 1.1
         * if ($this->modx->hasPermission('discuss.thread_send') {
         *   $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $this->modx->lexicon('discuss.thread_send'));
         * }
         */
        if ($this->thread->canPrint() && !empty($this->options['showPrintOption'])) {
            $actionButtons[] = array('url' => $this->thread->getUrl(false,array('print' => 1)), 'text' => $this->modx->lexicon('discuss.print'),'cls' => 'dis-action-print');
            $this->setPlaceholder('actionlink_print', $this->thread->getUrl(false,array('print' => 1)));
        }
        $this->setPlaceholder('actionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function getModeratorActionButtons() {
        $actionButtons = array();
        if ($this->thread->canMove()) {
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/move',array('thread' => $this->thread->get('id'))), 'text' => $this->modx->lexicon('discuss.thread_move'),'cls' => 'dis-action-move dis-action-thread_move');
        }
        if ($this->thread->canRemove()) {
            $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/remove',array('thread' => $this->thread->get('id'))), 'text' => $this->modx->lexicon('discuss.thread_remove'),'cls' => 'dis-action-remove-thread dis-action-thread_remove');
            if (!empty($this->options['showMarkAsSpamOption'])) {
                $actionButtons[] = array('url' => $this->discuss->request->makeUrl('thread/spam',array('thread' => $this->thread->get('id'))), 'text' => $this->modx->lexicon('discuss.thread_spam'),'cls' => 'dis-action-spam dis-action-thread_spam');
            }
        }

        if ($this->thread->canUnlock() && !empty($this->options['showLockOption'])) {
            $actionButtons[] = array('url' => $this->thread->getUrl(false,array('lock' => 0)), 'text' => $this->modx->lexicon('discuss.thread_unlock'),'cls' => 'dis-action-unlock dis-action-thread_unlock');
        } else if ($this->thread->canLock() && !empty($this->options['showLockOption'])) {
            $actionButtons[] = array('url' => $this->thread->getUrl(false,array('lock' => 1)), 'text' => $this->modx->lexicon('discuss.thread_lock'),'cls' => 'dis-action-lock dis-action-thread_lock');
        }
        if ($this->thread->canUnstick() && !empty($this->options['showStickOption'])) {
            $actionButtons[] = array('url' => $this->thread->getUrl(false,array('sticky' => 0)), 'text' => $this->modx->lexicon('discuss.thread_unstick'),'cls' => 'dis-action-unstick dis-action-thread_unstick');
        } else if ($this->thread->canStick() && !empty($this->options['showStickOption'])) {
            $actionButtons[] = array('url' => $this->thread->getUrl(false,array('sticky' => 1)), 'text' => $this->modx->lexicon('discuss.thread_stick'),'cls' => 'dis-action-stick dis-action-thread-stick');
        }
        /**
         * TODO: Merge thread - 1.1
         * $actionButtons[] = array('url' => 'javascript:void(0);', 'text' => $this->modx->lexicon('discuss.thread_merge'));
         */
        $this->setPlaceholder('threadactionbuttons',$this->discuss->buildActionButtons($actionButtons,'dis-action-btns right'));
    }

    public function getAnswers() {
        if (($this->thread->get('class_key') == 'disThreadQuestion') && $this->thread->get('answered')) {
            $c = $this->modx->newQuery('disPost');
            $c->innerJoin('disUser','Author');
            $c->where(array(
                'board' => $this->thread->get('board'),
                'thread' => $this->thread->get('id'),
                'answer' => 1,
            ));
            $c->sortby('createdon', 'ASC');
            $c->select($this->modx->getSelectColumns('disPost','disPost'));
            $c->select($this->modx->getSelectColumns('disUser','Author','author_', array('password','hash_class'), true));

            $answers = $this->modx->getCollection('disPost', $c);
            if (!empty($answers)) {
                $urls = array();
                foreach ($answers as $post) {
                    /* @var disPost $post */
                    $urls[$post->get('id')] = $post->toArray();
                    $urls[$post->get('id')]['url'] = $post->getUrl();
                }
                $this->answerPosts = $urls;
            }
            $this->setPlaceholder('answer_count', count($this->answerPosts));
        }
    }
}

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
 * Theme manifest for default theme
 */
$manifest = array(
    'preview' => 'preview.png',
    'global' => array(
        'js' => array(
            'inline' => 'var DIS = {config: {}}; DIS.url = "'.$this->discuss->url.'";DIS.shJsUrl = "'.$this->discuss->config['jsUrl'].'sh/";DIS.config.connector = "'.$this->discuss->config['connectorUrl'].'"; DIS.config.forum_url = "'.$this->discuss->url.'"',
        ),
        'options' => array(
            'registerJsToScriptTags' => false,
            'showBreadcrumbs' => true,
            'showTitleInBreadcrumbs' => true,
            'showReaders' => true,
            'showModerators' => true,
            'showPaginationIfOnePage' => false,
            'showPrintOption' => false,
        ),
        'furl' => array(
            array(
                'condition' => array(
                    'type' => 'category',
                ),
                'data' => array(
                    array('type' => 'constant', 'value' => 'category'),
                    array('type' => 'variable-required', 'key' => 'category'),
                    array('type' => 'variable', 'key' => 'category_name'),
                    array('type' => 'allparameters'),
                ),
            ),
            array(
                'condition' => array(),
                'data' => array(
                    array('type' => 'action'),
                    array('type' => 'allparameters'),
                ),
            ),
        ),
    ),
    'print' => array(
        'css' => array(
            'header' => array(
                'print.css',
            ),
        ),
    ),
    'home' => array(
        'options' => array(
            'showBoards' => true,
            'showRecentPosts' => false,
            'showStatistics' => true,
            'showLoginForm' => false,
            'bypassUnreadCheck' => true,
            'checkUnread' => true,
            'showLogoutActionButton' => false,
            'hideIndexBreadcrumbs' => false,
            'subBoardSeparator' => '',
        ),
    ),
    'board' => array(
        'options' => array(
            'showSubBoards' => true,
            'showPosts' => true,
        ),
        'furl' => array(
            array(
                'condition' => array(),
                'data' => array(
                    array('type' => 'constant', 'value' => 'board'),
                    array('type' => 'variable-required', 'key' => 'board'),
                    array('type' => 'variable', 'key' => 'board_name'),
                    array('type' => 'allparameters'),
                ),
            ),
        ),
    ),
    'board.xml' => array(
        'options' => array(
            'showSubBoards' => false,
            'showPosts' => true,
            'showBreadcrumbs' => false,
            'showReaders' => false,
            'showModerators' => false,
            'useLastPost' => false,
        ),
    ),
    'thread' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showPosts' => true,
            'showTitleInBreadcrumbs' => false,
            'showViewing' => true,
            'showSubscribeOption' => true,
            'showStickOption' => true,
            'showLockOption' => true,
            'showMarkAsSpamOption' => true,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_talking' => '1',
                    'show_be_nice_box' => '1',
                )
            )
        ),
        'furl' => array(
            array(
                'condition' => array(),
                'data' => array(
                    array('type' => 'constant', 'value' => 'thread'),
                    array('type' => 'variable-required', 'key' => 'thread'),
                    array('type' => 'variable', 'key' => 'thread_name'),
                    array('type' => 'allparameters'),
                ),
            ),
        ),
    ),
    'thread/new' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            ),
        ),
        'options' => array(
            'pageTpl' => 'common/thread-with-form',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'form' => array(
                'tpl' => 'replyform',
                'options' => array(
                    'hook' => 'DiscussNewThread',
                    'action' => 'new',
                    'actionvar' => 'board',
                )
            )
        ),
    ),
    'thread/reply' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            ),
        ),
        'options' => array(
            'pageTpl' => 'common/thread-with-form',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'form' => array(
                'tpl' => 'replyform',
                'options' => array(
                    'hook' => 'DiscussReplyPost',
                    'action' => 'reply',
                    'actionvar' => 'post',
                )
            )
        ),
    ),
    'thread/modify' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            ),
        ),
        'options' => array(
            'pageTpl' => 'common/thread-with-form',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                    'show_talking' => '1',
                )
            ),
            'form' => array(
                'tpl' => 'replyform',
                'options' => array(
                    'hook' => 'DiscussModifyPost',
                    'action' => 'modify',
                    'actionvar' => 'post',
                )
            )
        ),
    ),
    'thread/move' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
        ),
    ),
    'thread/spam' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
        ),
    ),
    'thread/remove' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
        ),
    ),
    'post/report' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
        ),
    ),
    'post/spam' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
        ),
    ),
    'search' => array(
        'js' => array(
            'footer' => array(
                'dis.search.js',
            ),
        ),
    ),
    'user' => array(
        'options' => array(
            'showRecentPosts' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
        'furl' => array(
            array(
                'condition' => array(
                    'type' => 'username',
                ),
                'data' => array(
                    array('type' => 'constant', 'value' => 'u'),
                    array('type' => 'variable-required', 'key' => 'user'),
                    array('type' => 'allparameters'),
                ),
            ),
            array(
                'condition' => array(),
                'data' => array(
                    array('type' => 'constant', 'value' => 'user'),
                    array('type' => 'parameter', 'key' => 'user'),
                    array('type' => 'allparameters'),
                ),
            ),
        ),
    ),
    'user/subscriptions' => array(
        'js' => array(
            'footer' => array(
                'user/dis.user.subscriptions.js',
            )
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
    ),
    'user/ignoreboards' => array(
        'js' => array(
            'footer' => array(
                'user/dis.user.ignoreboards.js',
            )
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
    ),
    'user/ban' => array(
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
    ),
    'user/edit' => array(
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
    ),
    'user/merge' => array(
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
    ),
    'user/posts' => array(
        'options' => array(
            'postTpl' => 'post/disPostLi'
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
    ),
    'user/statistics' => array(
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'user-sidebar',
                'options' => array(
                )
            )
        ),
    ),
    'messages' => array(
        'options' => array(
            'showSubBoards' => true,
            'showPosts' => true,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            )
        ),
    ),
    'messages/new' => array(
        'options' => array(
            'pageTpl' => 'common/messages-with-form',
        ),
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            ),
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'form' => array(
                'tpl' => 'message-form',
                'options' => array(
                    'hook' => 'DiscussNewMessage',
                    'action' => 'new',
                    'formaction' => 'new',
                    'submit_message' => '[[%discuss.message_send]]',
                    'cancel_link' => 'messages',
                    'extra_validation' => ',add_participants:required',
                )
            ),
        ),
    ),
    'messages/reply' => array(
        'options' => array(
            'pageTpl' => 'common/messages-with-form',
        ),
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            ),
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'form' => array(
                'tpl' => 'message-form',
                'options' => array(
                    'hook' => 'DiscussReplyMessage',
                    'action' => 'reply',
                    'formaction' => 'reply?thread=[[!+fi.thread]]',
                    'submit_message' => '[[%discuss.message_send]]',
                    'cancel_link' => 'messages/view?thread=[[+thread]]',
                    'extra_validation' => '',
                )
            ),
        ),
    ),
    'messages/modify' => array(
        'options' => array(
            'pageTpl' => 'common/messages-with-form',
        ),
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            ),
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'form' => array(
                'tpl' => 'message-form',
                'options' => array(
                    'hook' => 'DiscussModifyMessage',
                    'action' => 'modify',
                    'formaction' => 'modify?post=[[!+fi.id]]',
                    'submit_message' => '[[%discuss.save_changes]]',
                    'cancel_link' => 'messages/view?message=[[+thread]]#dis-post-[[+id]]',
                    'extra_validation' => '',
                )
            ),
        ),
    ),
    'messages/view' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showPosts' => true,
            'showViewing' => true,
            'showSubscribeOption' => false,
            'showStickOption' => true,
            'showLockOption' => true,
            'showMarkAsSpamOption' => true,
            'showTitleInBreadcrumbs' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            )
        ),
    ),
    'thread/recent' => array(
        'options' => array(
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            )
        ),
    ),
    'thread/unread' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'navbar_extra' => array(
                'tpl' => 'navbar_extra-wrapper',
                'options' => array(
                    'content' => '<a href="[[~[[++discuss.forums_resource_id]]]]thread/unread_last_visit" class="action-buttons dis-action-unread_last_visit" title="[[%discuss.unread_posts_last_visit]]">[[%discuss.unread_posts_last_visit]]</a>
                        <a class="read" href="[[+actionlink_mark_read]]" title="[[%discuss.mark_all_as_read]]">[[%discuss.mark_all_as_read]]</a>'
                )
            )
        ),
    ),
    'thread/unread_last_visit' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'navbar_extra' => array(
                'tpl' => 'navbar_extra-wrapper',
                'options' => array(
                    'content' => '<a href="[[~[[++discuss.forums_resource_id]]]]thread/unread" class="action-buttons dis-action-unread" title="[[%discuss.unread_posts_all]]">[[%discuss.unread_posts_all]]</a>
                        <a class="read" href="[[+actionlink_mark_read]]" title="[[%discuss.mark_all_as_read]]">[[%discuss.mark_all_as_read]]</a>'
                )
            )
        )
    ),
    'thread/no_replies' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            )
        ),
    ),
    'thread/unanswered_questions' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            )
        ),
    ),
    'thread/new_replies_to_posts' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'show_be_nice_box' => '1',
                )
            ),
            'navbar_extra' => array(
                'tpl' => 'navbar_extra-wrapper',
                'options' => array(
                    'content' => '<a class="action-buttons" href="[[+actionlink_mark_read]]" title="[[%discuss.mark_all_as_read]]">[[%discuss.mark_all_as_read]]</a>'
                )
            )
        ),
    ),
    'thread/feed.xml' => array(

    )
);
return $manifest;

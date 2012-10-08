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
        'css' => array(
            'header' => array(
            ),
        ),
        'js' => array(
            'inline' => 'var DIS = {config: {}}; DIS.url = "'.$this->discuss->request->makeUrl().'";DIS.shJsUrl = "'.$this->discuss->config['jsUrl'].'sh/";DIS.config.connector = "'.$this->discuss->config['connectorUrl'].'"',
        ),
        'options' => array(
            'registerJsToScriptTags' => false,
        )
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
            'showBreadcrumbs' => true,
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
            'showBreadcrumbs' => true,
            'showReaders' => true,
            'showModerators' => true,
            'showPaginationIfOnePage' => false,
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
            'showBreadcrumbs' => true,
            'showViewing' => true,
            'showSubscribeOption' => true,
            'showPrintOption' => true,
            'showStickOption' => true,
            'showLockOption' => true,
            'showMarkAsSpamOption' => true,
            'showTitleInBreadcrumbs' => false,
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'thread-index',
                )
            )
        ),
    ),
    'thread/new' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            ),
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
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
            'showTitleInBreadcrumbs' => true,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
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
            'showTitleInBreadcrumbs' => true,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
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
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'thread/spam' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'thread/remove' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'post/report' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'post/spam' => array(
        'js' => array(
            'footer' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
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
            'showBreadcrumbs' => true,
            'showReaders' => true,
            'showModerators' => true,
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
                )
            )
        ),
    ),
    'messages/new' => array(
        'js' => array(
            'footer' => array(
                'messages/dis.message.new.js',
            ),
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
                )
            )
        ),
    ),
    'messages/reply' => array(
        'js' => array(
            'footer' => array(
                'messages/dis.message.reply.js',
            ),
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
                )
            )
        ),
    ),
    'messages/modify' => array(
        'js' => array(
            'footer' => array(
                'messages/dis.message.modify.js',
            ),
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
                )
            )
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
            'showBreadcrumbs' => true,
            'showViewing' => true,
            'showSubscribeOption' => false,
            'showPrintOption' => false,
            'showStickOption' => true,
            'showLockOption' => true,
            'showMarkAsSpamOption' => true,
            'showTitleInBreadcrumbs' => false,
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'new-message',
                )
            )
        ),
    ),
    'thread/recent' => array(
        'options' => array(
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'recent',
                )
            )
        ),
    ),
    'thread/unread' => array(
        'options' => array(
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => false,
            'pageTpl' => 'common/thread-table',
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'recent',
                )
            )
        ),
    ),
    'thread/unread_last_visit' => array(
        'options' => array(
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'recent',
                )
            )
        ),
    ),
    'thread/no_replies' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'recent',
                )
            )
        ),
    ),
    'thread/unanswered_questions' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'recent',
                )
            )
        ),
    ),
    'thread/new_replies_to_posts' => array(
        'options' => array(
            'pageTpl' => 'common/thread-table',
            'showPaginationIfOnePage' => false,
        ),
        'modules' => array(
            'sidebar' => array(
                'tpl' => 'post-sidebar',
                'options' => array(
                    'disection' => 'recent',
                )
            )
        ),
    ),
);
return $manifest;

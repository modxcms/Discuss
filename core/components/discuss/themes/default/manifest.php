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
                'index.css',
                'jquery-ui-1.8.16.custom.css',
            ),
        ),
        'js' => array(
            'header' => array(
                'jquery-1.6.2.min.js',
                'jquery-ui-1.8.16.custom.min.js',
                'discuss.js',
                'sh/shCore.js',
                'sh/shAutoloader.js',
                'sh/shDiscuss.js',
            ),
            'inline' => 'DIS.url = "'.$this->discuss->url.'";DIS.shJsUrl = "'.$this->discuss->config['jsUrl'].'sh/";',
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
        'js' => array(
            'header' => array(
                'dis.home.js',
            ),
        ),
        'options' => array(
            'showBoards' => true,
            'showBreadcrumbs' => true,
            'showRecentPosts' => true,
            'showStatistics' => true,
            'showLoginForm' => false,
            'bypassUnreadCheck' => true,
            'checkUnread' => true,
            'showLogoutActionButton' => true,
        ),
    ),
    'board' => array(
        'js' => array(
            'header' => array(
                'dis.board.js',
            ),
        ),
        'options' => array(
            'showSubBoards' => true,
            'showPosts' => true,
            'showBreadcrumbs' => true,
            'showReaders' => true,
            'showModerators' => true,
            'showPaginationIfOnePage' => false,
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
        ),
    ),
    'thread' => array(
        'js' => array(
            'header' => array(
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
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => true,
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
            'header' => array(
                'dis.thread.new.js',
                'dis.post.buttons.js',
            ),
        ),
    ),
    'thread/reply' => array(
        'js' => array(
            'header' => array(
                'dis.post.reply.js',
                'dis.post.buttons.js',
            ),
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'thread/modify' => array(
        'js' => array(
            'header' => array(
                'dis.post.modify.js',
                'dis.post.buttons.js',
            ),
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'thread/move' => array(
        'js' => array(
            'header' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'thread/spam' => array(
        'js' => array(
            'header' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'thread/remove' => array(
        'js' => array(
            'header' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'post/report' => array(
        'js' => array(
            'header' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'post/spam' => array(
        'js' => array(
            'header' => array(
                'dis.thread.js',
            )
        ),
        'options' => array(
            'showTitleInBreadcrumbs' => true,
        ),
    ),
    'search' => array(
        'js' => array(
            'header' => array(
                'dis.search.js',
            ),
            'inline' => '$(".date-picker").datepicker();',
        ),
        'css' => array(
            'header' => array(
                'search.css',
            ),
        )
    ),
    'user' => array(
        'options' => array(
            'showRecentPosts' => true,
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
            'header' => array(
                'user/dis.user.subscriptions.js',
            )
        ),
    ),
    'user/ignoreboards' => array(
        'js' => array(
            'header' => array(
                'user/dis.user.ignoreboards.js',
            )
        ),
    ),
    'messages/index' => array(
        'js' => array(
            'header' => array(
                'dis.board.js',
            ),
        ),
        'options' => array(
            'showSubBoards' => true,
            'showPosts' => true,
            'showBreadcrumbs' => true,
            'showReaders' => true,
            'showModerators' => true,
            'showPaginationIfOnePage' => true,
        ),
    ),
    'messages/view' => array(
        'js' => array(
            'header' => array(
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
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => true,
        ),
    ),
    'messages/new' => array(
        'js' => array(
            'header' => array(
                'messages/dis.message.new.js',
                'dis.post.buttons.js',
            ),
        ),
    ),
    'messages/reply' => array(
        'js' => array(
            'header' => array(
                'messages/dis.message.reply.js',
                'dis.post.buttons.js',
            ),
        ),
    ),
    'messages/modify' => array(
        'js' => array(
            'header' => array(
                'messages/dis.message.modify.js',
                'dis.post.buttons.js',
            ),
        ),
    ),
    'thread/recent' => array(
        'options' => array(
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => true,
        ),
    ),
    'thread/unread' => array(
        'options' => array(
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => true,
        ),
    ),
    'thread/unread_last_visit' => array(
        'options' => array(
            'showTitleInBreadcrumbs' => true,
            'showPaginationIfOnePage' => true,
        ),
    ),
);
return $manifest;
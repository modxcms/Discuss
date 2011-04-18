<?php
/**
 * Theme manifest for default theme
 */
$manifest = array(
    'global' => array(
        'css' => array(
            'header' => array(
                'index.css',
            ),
        ),
        'js' => array(
            'header' => array(
                'web/discuss.js',
            ),
        ),
    ),
    'home' => array(
        'js' => array(
            'header' => array(
                'web/dis.home.js',
            ),
        ),
    ),
    'board' => array(
        'js' => array(
            'header' => array(
                'web/dis.board.js',
            ),
        ),
    ),
    'thread/index' => array(
        'js' => array(
            'header' => array(
                'web/dis.thread.js',
            )
        ),
    ),
    'thread/new' => array(
        'js' => array(
            'header' => array(
                'web/dis.thread.new.js',
                'web/dis.post.buttons.js',
            ),
        ),
    ),
    'thread/reply' => array(
        'js' => array(
            'header' => array(
                'web/dis.post.reply.js',
                'web/dis.post.buttons.js',
            ),
        ),
    ),
    'thread/modify' => array(
        'js' => array(
            'header' => array(
                'web/dis.post.modify.js',
                'web/dis.post.buttons.js',
            ),
        ),
    ),
    'thread/remove' => array(
        'js' => array(
            'header' => array(
                'web/dis.thread.js',
            )
        ),
    ),
    'search' => array(
        'js' => array(
            'header' => array(
                'web/dis.search.js',
            ),
        ),
        'css' => array(
            'header' => array(
                'search.css',
            ),
        )
    ),
    'user/notifications' => array(
        'js' => array(
            'header' => array(
                'web/user/dis.user.notifications.js',
            )
        ),
    ),
    'user/ignoreboards' => array(
        'js' => array(
            'header' => array(
                'web/user/dis.user.ignoreboards.js',
            )
        ),
    ),
);
return $manifest;
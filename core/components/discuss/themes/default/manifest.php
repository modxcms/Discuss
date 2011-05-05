<?php
/**
 * Theme manifest for default theme
 */
$manifest = array(
    'preview' => 'preview.png',
    'global' => array(
        'css' => array(
            'header' => array(
                'index.css',
            ),
        ),
        'js' => array(
            'header' => array(
                'jquery-1.3.2.min.js',
                'discuss.js',
                'sh/shCore.js',
                'sh/shAutoloader.js',
                'sh/shDiscuss.js',
            ),
            'inline' => 'DIS.url = "'.$this->discuss->url.'";DIS.shJsUrl = "'.$this->discuss->config['jsUrl'].'sh/";',
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
    ),
    'board' => array(
        'js' => array(
            'header' => array(
                'dis.board.js',
            ),
        ),
    ),
    'thread/index' => array(
        'js' => array(
            'header' => array(
                'dis.thread.js',
            )
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
    ),
    'thread/modify' => array(
        'js' => array(
            'header' => array(
                'dis.post.modify.js',
                'dis.post.buttons.js',
            ),
        ),
    ),
    'thread/remove' => array(
        'js' => array(
            'header' => array(
                'dis.thread.js',
            )
        ),
    ),
    'search' => array(
        'js' => array(
            'header' => array(
                'dis.search.js',
            ),
        ),
        'css' => array(
            'header' => array(
                'search.css',
            ),
        )
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
);
return $manifest;
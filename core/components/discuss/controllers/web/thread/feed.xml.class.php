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
require_once dirname(__FILE__).'/index.class.php';
/**
 * Show Recent Posts
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadFeedXmlController extends DiscussThreadController {
    public $useWrapper = false;
    public function getSessionPlace() { return ''; }

    public function getDefaultOptions() {
        return array_merge(parent::getDefaultOptions(),array(
            'showPosts' => true,
            'postTpl' => 'post/disThreadPostXml',
            'discuss.post_sort_dir' => 'desc',
        ));
    }

    public function process() {
    	$this->modx->setOption('discuss.absolute_urls',true);

        /* get posts */
        $this->getPosts();

        $this->setPlaceholder('rtl',$this->board->get('rtl'));

        $threadArray = $this->thread->toArray('',true,true);
        $this->setPlaceholders($threadArray);
        $this->setPlaceholder('title',$this->thread->get('title'));
        $this->setPlaceholder('title_value',$threadArray['title']);
        $this->setPlaceholder('views',number_format($this->getPlaceholder('views',1)));
        $this->setPlaceholder('replies',number_format($this->getPlaceholder('replies',0)));
        $this->setPlaceholder('url',$this->thread->getUrl());

        /* output */
        $this->setPlaceholder('discuss.error_panel',$this->discuss->getChunk('Error'));
        $this->setPlaceholder('discuss.thread',$this->thread->get('title'));
    }
    public function postProcess() {
        @header('Content-type: application/xhtml+xml');
    }
}

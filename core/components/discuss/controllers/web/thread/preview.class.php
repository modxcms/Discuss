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
 * @package discuss
 * @subpackage controllers
 */
class DiscussThreadPreviewController extends DiscussController {
    public $useWrapper = false;
    public function getDefaultOptions() {
        return array(
            'tpl' => 'post/disPostPreview',
        );
    }
    public function getPageTitle() { return ''; }
    public function getSessionPlace() { return ''; }
    public function checkPermissions() {
        return $this->discuss->user->isLoggedIn;
    }
    public function process() {
        $postArray = $this->scriptProperties;
        $postArray['action_remove'] = '';
        $postArray['action_modify'] = '';
        $postArray['action_quote'] = '';
        $postArray['action_reply'] = '';

        $author = $this->discuss->user->toArray();
        foreach ($author as $k => $v) {
            $postArray['author.'.$k] = $v;
        }

        /** @var disPost $post */
        $post = $this->modx->newObject('disPost');
        $post->fromArray($postArray);
        $postArray = $post->toArray();
        /* handle MODX tags */
        $post->set('message',str_replace(array('[[',']]'),array('&#91;&#91;','&#93;&#93;'),$postArray['message']));

        /* get formatted content */
        $postArray['message'] = $post->getContent();
        $postArray['title'] = $post->parser->parse($postArray['title']);
        $postArray['createdon'] = strftime($this->discuss->dateFormat,time());

        $output = $this->discuss->getChunk($this->getOption('tpl'),$postArray);
        $this->setPlaceholder('post',$output);
    }
}
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
 * Downloads and tracks the download for an attachment
 *
 * @package discuss
 * @subpackage controllers
 */
class DiscussAttachmentDownloadController extends DiscussController {
    public function getPageTitle() {

    }
    public function getSessionPlace() {
        return 'downloadattachment:'.$this->getProperty('file','');
    }

    public function process() {
        $file = $this->getProperty('file',false);
        /* get attachment */
        if (empty($file)) $this->discuss->sendErrorPage();
        /** @var disPostAttachment $attachment */
        $attachment = $this->modx->getObject('disPostAttachment',$file);
        if (empty($attachment)) $this->discuss->sendErrorPage();

        $path = $attachment->getPath();
        if (file_exists($path)) {
            $downloads = $attachment->get('downloads');
            $downloads++;
            $attachment->set('downloads',$downloads);
            $attachment->save();

            $this->modx->sendRedirect($attachment->getUrl());
        } else {
            $this->modx->sendErrorPage();
        }
    }
}

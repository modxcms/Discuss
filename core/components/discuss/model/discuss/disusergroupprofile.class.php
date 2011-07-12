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
 * Metadata class for modUserGroups
 * @package discuss
 */
class disUserGroupProfile extends xPDOSimpleObject {
    /**
     * Return the URL for this user's badge, if they have one
     * @return string The URL to the badge
     */
    public function getBadge() {
        $badge = $this->get('image');
        if (!empty($badge)) {
            $badge = $this->getBadgeUrl().$badge;
        }
        return $badge;
    }

    /**
     * Get the absolute path to the badges directory
     * @return string The absolute path to the badges directory
     */
    public function getBadgePath() {
        return $this->xpdo->getOption('discuss.attachments_path').'badges/'.$this->get('id').'/';
    }

    /**
     * Get the full URL to the badges directory
     * @return string The full URL to the badges URL
     */
    public function getBadgeUrl() {
        return $this->xpdo->getOption('discuss.attachments_url').'badges/'.$this->get('id').'/';
    }

    /**
     * Upload a badge and associate with this user group
     * 
     * @param array $file A php FILE array for the badge
     * @param bool $removeOld If true, will remove the old badge if one was set
     * @return bool
     */
    public function uploadBadge($file,$removeOld = false) {
        $uploaded = false;
        $oldBadge = $this->get('image');

        $targetDir = $this->getBadgePath();
        $cacheManager = $this->xpdo->getCacheManager();
        /* if directory doesnt exist, create it */
        if (!file_exists($targetDir) || !is_dir($targetDir)) {
            if (!$cacheManager->writeTree($targetDir)) {
               $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] Could not create directory: '.$targetDir);
               return $uploaded;
            }
        }
        /* make sure directory is readable/writable */
        if (!is_readable($targetDir) || !is_writable($targetDir)) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] Could not write to directory: '.$targetDir);
            return $uploaded;
        }

        /* upload the file */
        $fileNameLower = strtolower($file['name']);
        $location = strtr($targetDir.'/'.$fileNameLower,'\\','/');
        $location = str_replace('//','/',$location);
        if (file_exists($location.$fileNameLower)) {
            @unlink($location.$fileNameLower);
        }
        if (!@move_uploaded_file($file['tmp_name'],$location)) {
            $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] An error occurred while trying to upload the file: '.$file['tmp_name'].' to '.$location);
        } else {
            $uploaded = true;
            $this->set('image',$fileNameLower);

            if (!empty($oldBadge) && $removeOld && $oldBadge != $fileNameLower) {
                $oldBadgePath = $this->getBadgePath().$oldBadge;
                if (file_exists($oldBadgePath)) {
                    @unlink($oldBadgePath);
                }
            }
        }

        return $uploaded;
    }
}
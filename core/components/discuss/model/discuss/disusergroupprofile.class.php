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
 * Related object to the modUserGroup class, used for storing custom data specific to Discuss.
 *
 * @property int $usergroup The ID of the modUserGroup tied to this group.
 * @property boolean $post_based Whether or not a certain number of posts is required to be a part of this group.
 * @property int $min_posts If post-based, the minimum number of posts required to be in this group.
 * @property string $color Optional. If set, users with this group as their primary will show this color when in lists.
 * @property string $image Optional. An image badge for the user group/
 * @property int $integrated_id If imported, the PK of the group from the imported system
 *
 * @property modUserGroup $UserGroup The related modUserGroup object
 * @property array $Boards A collection of related Boards this UserGroup can access
 *
 * @see modUserGroup
 * @package discuss
 */
class disUserGroupProfile extends xPDOSimpleObject {
    /**
     * Return the URL for this user's badge, if they have one
     * @return string The URL to the badge
     */
    public function getBadge() {
        $badge = $this->get('image');
        if ($badge == 'Array') {
            $badge = null;
            $this->set('badge','');
            $this->save();
        }
        if (!empty($badge)) {
            $badge = $this->getBadgeUrl().$badge;
        }
        return $badge;
    }

    public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
        $array = parent::toArray($keyPrefix,$rawValues,$excludeLazy,$includeRelated);
        $array['badge'] = $this->getBadge();
        return $array;
    }

    public function get($k, $format = null, $formatTemplate= null) {
        if ($k == 'badge') {
            $v = $this->getBadge();
        } else {
            $v = parent::get($k,$format,$formatTemplate);
        }
        return $v;
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
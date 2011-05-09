<?php
/**
 * Metadata class for modUserGroups
 * @package discuss
 */
class disUserGroupProfile extends xPDOSimpleObject {
    public function getBadge() {
        $badge = $this->get('image');
        if (!empty($badge)) {
            $badge = $this->getBadgeUrl().$badge;
        }
        return $badge;
    }

    public function getBadgePath() {
        return $this->xpdo->getOption('discuss.attachments_path').'badges/'.$this->get('id').'/';
    }

    public function getBadgeUrl() {
        return $this->xpdo->getOption('discuss.attachments_url').'badges/'.$this->get('id').'/';
    }

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
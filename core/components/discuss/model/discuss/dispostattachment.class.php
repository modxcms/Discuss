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
 */
class disPostAttachment extends xPDOSimpleObject {
    /**
     * Overrides xPDOObject::save to handle createdon date
     *
     * @param boolean $cacheFlag
     * @return boolean
     */
    public function save($cacheFlag = null) {
        if ($this->isNew()) {
            if (!$this->get('createdon')) {
                $this->set('createdon', strftime('%Y-%m-%d %H:%M:%S'));
            }
        }
        return parent::save($cacheFlag);
    }

    /**
     * Overrides the xPDOObject::remove method to remove the physical file for
     * the attachment
     *
     * @param array $ancestors
     * @return boolean
     */
    public function remove(array $ancestors = array()) {
        $filename = $this->get('filename');
        if (!empty($filename)) {
            $filename = $this->getPath();
            if (!@unlink($filename)) {
                $this->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[Discuss] An error occurred while trying to remove the attachment file at: '.$filename);
            }
        }
        return parent::remove($ancestors);
    }

    /**
     * Gets the path of the attachment (and optionally just the directory)
     *
     * @access public
     * @param boolean $appendFile If true, will append the filename to the
     * attachment path. Defaults to true
     * @return string The path to the file or directory of the file.
     */
    public function getPath($appendFile = true) {
        $path = $this->xpdo->getOption('discuss.attachments_path').$this->get('board').'/'.$this->get('post').'/';
        if ($appendFile) {
            $path .= $this->get('filename');
        }

        $path = str_replace(array(
            '{base_path}',
            '{core_path}',
            '{assets_path}',
        ),array(
            $this->xpdo->getOption('base_path'),
            $this->xpdo->getOption('core_path'),
            $this->xpdo->getOption('assets_path'),
        ),$path);
        return $path;
    }

    /**
     * Gets the url of the attachment (and optionally just the directory)
     *
     * @access public
     * @param boolean $appendFile If true, will append the filename to the
     * attachment url. Defaults to true
     * @return string The url to the file or directory of the file.
     */
    public function getUrl($appendFile = true) {
        $url = $this->xpdo->getOption('discuss.attachments_url').$this->get('board').'/'.$this->get('post').'/';
        if ($appendFile) {
            $url .= $this->get('filename');
        }

        $url = str_replace(array(
            '{base_url}',
            '{assets_url}',
        ),array(
            $this->xpdo->context->getOption('base_url'),
            $this->xpdo->context->getOption('assets_url'),
        ),$url);
        return $url;
    }

    /**
     * Handles uploads for the attachment
     *
     * @access public
     * @param array $file The POST file to upload.
     * @return boolean True if successful.
     */
    public function upload($file) {
        $uploaded = false;

        $targetDir = $this->getPath(false);
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
            $this->set('filename',$fileNameLower);
        }

        return $uploaded;
    }

    /**
     * Converts filesize to human-readable format
     *
     * @access public
     * @param int $precision The number of decimal points to round to.
     * @return string The formatted string.
     */
    public function convert($precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        $bytes = max($this->get('filesize'), 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
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
 * @package Discuss
 * @subpackage spam
 */
class disStopForumSpam {

    function __construct(Discuss &$discuss, array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;

        $this->config = array_merge(array(
            'host' => 'http://www.stopforumspam.com/',
            'path' => 'api',
            'method' => 'GET',
        ),$config);
    }

    /**
     * Check for spammer
     *
     * @access public
     * @param string $ip
     * @param string $email
     * @param string $username
     * @return array An array of errors
     */
    public function check($ip = '',$email = '',$username = '') {
        $params = array();
        if (!empty($ip)) {
            if (in_array($ip,array('127.0.0.1','::1','0.0.0.0'))) $ip = '72.179.10.158';
            $params['ip'] = $ip;
        }
        if (!empty($email)) $params['email'] = $email;
        if (!empty($username)) $params['username'] = $username;

        $xml = $this->request($params);
        $i = 0;
        $errors = array();
        foreach ($xml->appears as $result) {
            if ($result == 'yes') {
                $errors[] = ucfirst($xml->type[$i]);
            }
            $i++;
        }
        return $errors;
    }

    /**
     * Make a request to stopforumspam.com
     *
     * @access public
     * @param array $params An array of parameters to send
     * @return mixed The return SimpleXML object, or false if none
     */
    public function request($params = array()) {
        $loaded = $this->_getClient();
        if (!$loaded) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Could not load REST client.');
            return true;
        }

        $response = $this->modx->rest->request($this->config['host'],$this->config['path'],$this->config['method'],$params);
        $xml = simplexml_load_string($response);
        return $xml;
    }

    /**
     * Get the REST Client
     *
     * @access private
     * @return modRestClient/boolean
     */
    private function _getClient() {
        if (empty($this->modx->rest)) {
            $this->modx->getService('rest','rest.modRestClient');
            $loaded = $this->modx->rest->getConnection();
            if (!$loaded) return false;
        }
        return $this->modx->rest;
    }
}
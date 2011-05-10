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
 * Hooks loading class
 *
 * @package discuss
 * @subpackage hooks
 */
class disHooks {
    /**
     * @var modX $modx A reference to the modX instance.
     * @access public
     */
    public $modx = null;
    /**
     * @var Discuss $discuss A reference to the Discuss instance.
     * @access public
     */
    public $discuss = null;

    /**
     * The disHooks constructor.
     *
     * @param Discuss &$discuss A reference to the Discuss class
     * @param array $config An array of configuration options. May also pass in
     * a reference to the Discuss instance as 'discuss' which will be assigned
     * to the disHooks instance.
     */
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
    }

    /**
     * Load a hook.
     *
     * @access public
     * @param string $name The name of the hook to load
     * @param array $scriptProperties A configuration array of variables to run
     * the hook with
     * @return mixed The return value of the hook
     */
    public function load($name = '',array $scriptProperties = array()) {
        if (empty($name)) return false;

        $success = false;
        $hookFile = $this->discuss->config['hooksPath'].strtolower($name).'.php';
        if (file_exists($hookFile)) {
            $discuss =& $this->discuss;
            $modx =& $this->modx;

            $success = include $hookFile;
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[Discuss] Hook not found: '.$hookFile);
        }
        return $success;
    }
}
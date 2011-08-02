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
require_once dirname(__FILE__) . '/model/discuss/discuss.class.php';
class IndexManagerController extends modExtraManagerController {
    public static function getDefaultController() { return 'mgr/home'; }
}

abstract class DiscussManagerController extends modManagerController {
    /**
     * @var Discuss $discuss
     */
    public $discuss;
    
    public function initialize() {
        $this->discuss = new Discuss($this->modx);
        $this->modx->setLogTarget('ECHO');

        $this->addCss($this->discuss->config['mgrCssUrl'].'mgr.css');
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'discuss.js');
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'combos.js');
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'windows.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            Dis.config = '.$this->modx->toJSON($this->discuss->config).';
            Dis.config.connector_url = "'.$this->discuss->config['connectorUrl'].'";
            Dis.request = '.$this->modx->toJSON($_GET).';
        });
        </script>');
    }
    public function getLanguageTopics() {
        return array('discuss:default');
    }
    public function checkPermissions() { return true;}
}
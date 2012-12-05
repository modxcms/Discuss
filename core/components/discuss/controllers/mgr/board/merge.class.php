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
class DiscussMgrBoardMergeManagerController extends DiscussManagerController {
    /** @var disBoard $board */
    public $board;

    /**
     * @return bool|void
     */
    public function initialize() {
        parent::initialize();
        if (empty($this->scriptProperties['board'])) return $this->failure($this->modx->lexicon('discuss.usergroup_err_ns'));

        $this->board = $this->modx->getObject('disBoard',$this->scriptProperties['board']);
        return true;
    }

    public function process(array $scriptProperties = array()) {
        if ($this->board) {
            $arr = $this->board->toArray();
            $arr = $this->modx->toJSON($arr);
            $this->addHtml('<script type="text/javascript">
                Dis.record = '.$arr.';
            </script>');
        }
    }

    public function getPageTitle() { return $this->modx->lexicon('discuss.board_merge').': '.$this->board->get('name'); }
    public function loadCustomCssJs() {
        $this->addJavascript($this->discuss->config['mgrJsUrl'].'widgets/board/board.merge.panel.js');
        $this->addLastJavascript($this->discuss->config['mgrJsUrl'].'sections/board/merge.js');
    }
    public function getTemplateFile() { return $this->discuss->config['templatesPath'].'board/merge.tpl'; }
}

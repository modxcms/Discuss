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
 * Handles row-parsing of naive tree data.
 *
 * @todo lots of code cleanup.
 *
 * @package discuss
 */
class disTreeParser {
    /**
     * The last node
     * @var string
     */
    protected $last = '';
    /**
     * The current open tree node
     * @var array
     */
    protected $openThread = array();
    /**
     * The current output buffer
     * @var string
     */
    protected $output = '';
    /**
     * The tpl to use for each node
     * @var string
     */
    protected $tpl = '';

    /**
     * @param Discuss $discuss A reference to the Discuss instance
     * @param array $config An array of configuration options
     */
    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->config = $config;
    }

    /**
     * @param array $array The current data array
     * @param string $tpl The template for the node
     * @return array|string
     */
    public function parse(array $array,$tpl = '') {
        /* set a value not possible in a LEVEL column to allow the
         * first row to know it's "firstness" */
        $this->last = null;
        $this->tpl = $tpl;

        /* add a couple dummy "rows" to cap off formatting */
        $array[] = array();
        $array[] = array();

        /* invoke our formatting function via callback */
        $output = array_map(array($this,'_iterate'),$array);

        /* output the results */
        $output = implode("\n", $output);

        return $output;
    }

    /**
     * Iterate across the node
     * 
     * @access private
     * @param array $current The current array iteration
     * @return string
     */
    private function _iterate($current){
        $depth = $current['depth'];
        $parent = $current['parent'];
        $item = '';

        if (is_null($this->last)) {
            // Set first children in the tree
            $this->setOpenThread($current, $depth);

        } elseif ($this->last < $depth){

            //Set current as new openthread for the current depth
            $this->setOpenThread($current, $depth);

        } elseif ($depth == $this->last) {

            if ($depth == 0) {
                //If last thread is on the root, close it and add it the output item
                $item .= $this->getOpenThread(0);
            }
            //Set current as new openthread for the current depth
            $this->setOpenThread($current, $depth);

        } elseif ($this->last > $depth) {
            $nb = $depth > 0 ? $depth : 0;

            for ($i = $this->last; $i >= $nb; $i--) {
                if ($i > 0) {
                    //Process children and add id to parent children placeholder
                    $children = $this->getOpenThread($i);
                    $this->setOpenThreadChildren($i - 1, $children);
                }
            }

            if ($depth == 0) {
                // Close & chunkify the openThread only if we are on the root level
                $item .= $this->getOpenThread(0);
                //Set current thread as new root
                $this->setOpenThread($current,0);
            } else {
                //Set current thread as the last open thread
                $this->setOpenThread($current, $depth);
            }
        }

        $this->last = $depth;
        return $item;
    }

    /**
     * Set the current open thread
     *
     * @param string $string
     * @param int $depth
     * @return void
     */
    protected function setOpenThread($string, $depth) {
        if (!empty($this->openThread[$depth]) && $depth > 0) {
            $this->openThread[$depth - 1]['children'] .= $this->discuss->getChunk($this->tpl, $this->openThread[$depth]);
        }
        unset($this->openThread[$depth]);
        $this->openThread[$depth] = $string;
    }

    /**
     * Set the current open thread's children
     * @param int $depth
     * @param string $string
     * @return void
     */
    protected function setOpenThreadChildren($depth, $string) {
        $this->openThread[$depth]['children'] .= $string;
    }

    /**
     * Get the current open thread node in the tree
     * @param int $depth
     * @return string
     */
    protected function getOpenThread($depth) {
        $thread = $this->discuss->getChunk($this->tpl, $this->openThread[$depth]);
        unset($this->openThread[$depth]);
        return $thread;
    }
}
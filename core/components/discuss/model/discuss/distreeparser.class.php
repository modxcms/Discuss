<?php
/**
 * Handles row-parsing of naive tree data.
 *
 * @TODO: lots of code cleanup.
 *
 * @package discuss
 */
class disTreeParser {
    protected $last = '';
    protected $openThread = array();
    protected $output = '';

    function __construct(Discuss &$discuss,array $config = array()) {
        $this->discuss =& $discuss;
        $this->config = $config;
    }

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

    private function _iterate($current){
        $depth = $current['depth'];
        $parent = $current['parent'];

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

    protected function setOpenThread($string, $depth) {
        if (!empty($this->openThread[$depth]) && $depth > 0) {
            $this->openThread[$depth - 1]['children'] .= $this->discuss->getChunk($this->tpl, $this->openThread[$depth]);
        }
        unset($this->openThread[$depth]);
        $this->openThread[$depth] = $string;
    }

    protected function setOpenThreadChildren($depth, $string) {
        $this->openThread[$depth]['children'] .= $string;
    }

    protected function getOpenThread($depth) {
        $thread = $this->discuss->getChunk($this->tpl, $this->openThread[$depth]);
        unset($this->openThread[$depth]);
        return $thread;
    }
}
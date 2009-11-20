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
    public $openItem =      '<li class="[[+class]]" id="[[+idPrefix]][[+id]]">';
    public $closeItem =     '</li>';
    public $openChildren =  '<ol class="[[+olClass]]">';
    public $closeChildren = '</ol>';

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
        /* last = the previous row's level, or null on the first row */

        /* structural elements */
        $structure = '';

        /* set class/id for li */
        $openItem = str_replace('[[+id]]',$current['id'],$this->openItem);
        $idPrefix = !empty($current['idPrefix']) ? $current['idPrefix'] : 'dis-board-post-';
        $openItem = str_replace('[[+idPrefix]]',$idPrefix,$openItem);
        $class = !empty($current['class']) ? $current['class'] : 'dis-board-post';
        $openItem = str_replace('[[+class]]',$class,$openItem);

        /* set class for ol */
        $class = !empty($current['olClass']) ? $current['olClass'] : 'dis-board-thread';
        $openChildren = str_replace('[[+olClass]]',$class,$this->openChildren);

        $closeItem = $this->closeItem;
        $closeChildren = $this->closeChildren;

        if (!isset($current['depth'])) {
            /* add closing structure(s) equal to the very last row's level;
             * this will only fire for the "dummy" */
            return str_repeat($closeItem.$closeChildren,$this->last);
        }

        /* add the item itself */
        if (empty($this->tpl)) {
            $item = $current['title'];
        } else {
            $item = $this->discuss->getChunk($this->tpl,$current);
        }

        if (is_null($this->last)) {
            /* add the opening structure in the case of the first row */
            $structure .= $openChildren;
        } elseif ( $this->last < $current['depth'] ) {
            /* add the structure to start new branches */
            $structure .= $openChildren;
        } elseif ( $this->last > $current['depth'] ){
            /* add the structure to close branches equal to the difference
             * between the previous and current levels */
            $structure .= $closeItem
                . str_repeat($closeChildren . $closeItem,
                    $this->last - $current['depth']);
        } else {
            $structure .= $closeItem;
        }

        /* add the item structure */
        $structure .= $openItem;

        /* update last so the next row knows whether this row is really
         * its parent */
        $this->last = $current['depth'];

        return $structure.$item;
    }
}
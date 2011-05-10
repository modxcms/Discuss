<?php
/**
 * Abstract parser class that must be extended for creating custom parsing engines for Discuss Posts. Never
 * instantiate directly.
 *
 * @abstract
 */
abstract class disParser {
    /** @var modX\xPDO A reference to the modX object. */
    public $modx;
    /** @var Discuss $discuss A reference to the Discuss object. */
    public $discuss;
    
    function __construct(xPDO &$modx) {
        $this->modx =& $modx;
        $this->discuss =& $modx->discuss;
    }

    /**
     * Abstract parse method that is run for each post before rendering. Must be defined
     * in any derivative classes of disParser and determines the parsing of the post.
     *
     * @abstract
     * @param string $message
     * @return string The parsed content
     */
    abstract public function parse($message);

    /**
     * A better working nl2br
     *
     * @param string $str
     * @return string
     */
    protected function _nl2br2($str) {
        $str = str_replace("\r", '', $str);
        return preg_replace('/(?<!>)\n/', "<br />\n", $str);
    }

    /**
     * Convert BR tags to newlines
     *
     * @param string $str
     * @return string
     */
    public function br2nl($str) {
        return str_replace(array('<br>','<br />','<br/>'),"\n",$str);
    }
}
<?php
/**
 * The ancestor/descendant map of a disPost, with depth count.
 * @package discuss
 */
class disPostClosure extends xPDOObject {
    function disPostClosure(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
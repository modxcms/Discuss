<?php
/**
 * @package discuss
 */
class disPostRead extends xPDOSimpleObject {
    function disPostRead(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
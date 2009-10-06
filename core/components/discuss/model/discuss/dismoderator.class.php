<?php
/**
 * @package discuss
 */
class disModerator extends xPDOSimpleObject {
    function disModerator(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
<?php
/**
 * @package discuss
 */
class disReservedUsernames extends xPDOObject {
    function disReservedUsernames(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>
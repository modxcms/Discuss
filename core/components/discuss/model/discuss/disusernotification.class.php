<?php
/**
 * @package discuss
 */
class disUserNotification extends xPDOSimpleObject {
    function disUserNotification(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>
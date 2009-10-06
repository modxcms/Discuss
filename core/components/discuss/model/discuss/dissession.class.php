<?php
/**
 * Custom Discuss sessions for detailed user activity and session handling.
 * @package discuss
 */
class disSession extends xPDOObject {
    function disSession(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
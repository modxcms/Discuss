<?php
/**
 * @package discuss
 */
class disUserFriend extends xPDOSimpleObject {
    function disUserFriend(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
<?php
/**
 * If a user is moderated, they will have a row in this table
 * @package discuss
 */
class disUserModerated extends xPDOSimpleObject {
    function disUserModerated(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
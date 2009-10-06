<?php
/**
 * A list of reserved usernames to prevent users from registering with.
 *
 * @package discuss
 */
class disReservedUsername extends xPDOObject {
    function disReservedUsername(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
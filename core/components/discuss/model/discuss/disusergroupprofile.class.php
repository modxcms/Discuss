<?php
/**
 * Metadata class for modUserGroups
 * @package discuss
 */
class disUserGroupProfile extends xPDOSimpleObject {
    function disUserGroupProfile(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
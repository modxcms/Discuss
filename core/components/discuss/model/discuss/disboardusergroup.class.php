<?php
/**
 * Restricts access of a board to User Group(s)
 * @package discuss
 */
class disBoardUserGroup extends xPDOSimpleObject {
    function disBoardUserGroup(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
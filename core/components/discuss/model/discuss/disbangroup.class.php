<?php
/**
 * @package discuss
 */
class disBanGroup extends xPDOSimpleObject {
    function disBanGroup(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
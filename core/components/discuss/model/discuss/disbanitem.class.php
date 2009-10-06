<?php
/**
 * @package discuss
 */
class disBanItem extends xPDOSimpleObject {
    function disBanItem(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
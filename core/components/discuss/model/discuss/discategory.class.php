<?php
/**
 * Top-level aggregator for disBoard objects
 * @package discuss
 */
class disCategory extends xPDOSimpleObject {
    function disCategory(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
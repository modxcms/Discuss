<?php
/**
 * The ancestor/descendant map of a disBoard, with depth count.
 * @package discuss
 */
class disBoardClosure extends xPDOObject {
    function disBoardClosure(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
?>
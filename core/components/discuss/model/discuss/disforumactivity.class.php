<?php
/**
 * @package discuss
 */
class disForumActivity extends xPDOSimpleObject {
    function disForumActivity(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
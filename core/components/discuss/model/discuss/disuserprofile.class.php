<?php
/**
 * @package discuss
 */
define('DISCUSS_USER_INACTIVE',0);
define('DISCUSS_USER_ACTIVE',1);
define('DISCUSS_USER_UNCONFIRMED',2);
define('DISCUSS_USER_BANNED',3);
define('DISCUSS_USER_AWAITING_MODERATION',4);

/**
 * Metadata table for handling User Profile information
 * @package discuss
 */
class disUserProfile extends xPDOSimpleObject {
    function disUserProfile(& $xpdo) {
        $this->__construct($xpdo);
    }
    function __construct(& $xpdo) {
        parent :: __construct($xpdo);
    }
}
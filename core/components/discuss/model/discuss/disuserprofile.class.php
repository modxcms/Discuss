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
    const INACTIVE = 0;
    const ACTIVE = 1;
    const UNCONFIRMED = 2;
    const BANNED = 3;
    const AWAITING_MODERATION = 4;
}
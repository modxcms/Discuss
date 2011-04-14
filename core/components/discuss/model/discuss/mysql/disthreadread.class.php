<?php
/**
 * @package discuss
 */
require_once (strtr(realpath(dirname(dirname(__FILE__))), '\\', '/') . '/disthreadread.class.php');
class disThreadRead_mysql extends disThreadRead {}
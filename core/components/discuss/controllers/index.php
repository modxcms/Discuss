<?php
/**
 * @package discuss
 * @subpackage controllers
 */
require_once dirname(dirname(__FILE__)).'/model/discuss/discuss.class.php';
$discuss = new Discuss($modx);
return $discuss->initialize('mgr');
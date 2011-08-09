<?php
/**
 * @package discuss-test
 */
/**
 * Extends the basic PHPUnit TestCase class to provide Quip specific methods
 *
 * @package discuss-test
 */
class DiscussTestCase extends PHPUnit_Framework_TestCase {
    /**
     * @var modX $modx
     */
    protected $modx = null;
    /**
     * @var Discuss $discuss
     */
    protected $discuss = null;

    /**
     * Ensure all tests have a reference to the MODX and Discuss objects
     */
    public function setUp() {
        $this->modx =& DiscussTestHarness::_getConnection();
        $disCorePath = $this->modx->getOption('discuss.core_path',null,$this->modx->getOption('core_path',null,MODX_CORE_PATH).'components/discuss/');
        require_once $disCorePath.'model/discuss/discuss.class.php';
        $this->discuss = new Discuss($this->modx);
        /* set this here to prevent emails/headers from being sent */
        $this->discuss->inTestMode = true;
        /* make sure to reset MODX placeholders so as not to keep placeholder data across tests */
        $this->modx->placeholders = array();
    }

    /**
     * Remove reference at end of test case
     */
    public function tearDown() {
        $this->modx = null;
        $this->discuss = null;
    }
}
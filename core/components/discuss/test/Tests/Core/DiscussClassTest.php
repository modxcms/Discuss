<?php
/**
 * @package discuss-test
 */
/**
 * Tests related to basic Discuss class methods
 *
 * @package discuss-test
 * @group Core
 */
class DiscussClassTest extends DiscussTestCase {

    /**
     * Test loading of hooks
     * @return void
     */
    public function testLoadHooks() {
        $hooks = $this->discuss->loadHooks('unit');
        $this->assertInstanceOf('disHooks',$hooks);
        $this->assertInstanceOf('disHooks',$this->discuss->unitHooks);
        $this->discuss->unitHooks = null;
    }
}
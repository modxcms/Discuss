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
        $hooks = $this->discuss->loadHooks();
        $this->assertInstanceOf('disHooks',$hooks);
        $this->assertInstanceOf('disHooks',$this->discuss->hooks);
        $this->discuss->hooks = null;
    }


    /**
     * Test loading of tree parser
     * @return void
     */
    public function testLoadTreeParser() {
        $treeParser = $this->discuss->loadTreeParser();
        $this->assertInstanceOf('disTreeParser',$treeParser);
        $this->assertInstanceOf('disTreeParser',$this->discuss->treeParser);
        $this->discuss->treeParser = null;
    }


}
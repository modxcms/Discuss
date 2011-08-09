<?php
/**
 * @package discuss-test
 */
/**
 * Tests related to basic Discuss class methods
 *
 * @package discuss-test
 * @group Core
 * @group Board
 */
class disBoardTest extends DiscussTestCase {
    /** @var disBoard $board */
    public $board;

    /**
     * @var DiscussController|PHPUnit_Framework_MockObject_MockObject $controller
     */
    public $controller;

    public function setUp() {
        parent::setUp();
        $this->board = $this->modx->newObject('disBoard');
        $this->board->fromArray(array(
            'id' => 1,
            'name' => 'Unit Test Board',
        ),'',true,true);
    }

    public function tearDown() {
        parent::tearDown();
        //$this->board->remove();
    }

    /**
     * Test slug generation for boards
     *
     * @param string $alias
     * @param string $expected
     * @dataProvider providerGetSlug
     */
    public function testGetSlug($alias,$expected) {
        $this->board->set('name',$alias);
        $slug = $this->board->getSlug();
        $this->assertEquals($expected,$slug);
    }
    /**
     * @return array
     */
    public function providerGetSlug() {
        return array(
            array('test','test'),
            array('cApItAlS','capitals'),
            array('A Name With Spaces','a-name-with-spaces'),
        );
    }
    
}

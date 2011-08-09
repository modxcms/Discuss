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

    public function setUp() {
        parent::setUp();
        error_reporting(E_ALL);
        $this->board = $this->modx->newObject('disBoard');
        $this->board->fromArray(array(
            'id' => 12345,
            'name' => 'Unit Test Board',
        ),'',true,true);
        $this->discuss->loadRequest();
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

    /**
     * @param string $name
     * @param string $expected
     * @dataProvider providerGetUrl
     */
    public function testGetUrl($name,$expected) {
        $this->board->set('name',$name);
        $url = $this->board->getUrl();
        $url = str_replace($this->discuss->request->makeUrl(),'',$url);
        $this->assertEquals($expected,$url);
    }
    /**
     * @return array
     */
    public function providerGetUrl() {
        return array(
            array('Test Board','board/12345/test-board'),
            array('###A Really% Weird Board! Name^^^','board/12345/a-really-weird-board-name'),
        );
    }
}

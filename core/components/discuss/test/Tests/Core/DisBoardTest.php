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

    /**
     * @param string $name
     * @param string $expected
     * @dataProvider providerGetLastPostTitle
     */
    public function testGetLastPostTitle($name,$expected) {
        $this->board->set('unit_test_title',$name);
        $slug = $this->board->getLastPostTitle('unit_test_title');
        $this->assertEquals($expected,$slug);
        $this->assertEquals($this->board->get('unit_test_title'),$slug);
    }
    /**
     * @return array
     */
    public function providerGetLastPostTitle() {
        return array(
            array('Test Board','test-board/'),
            array('###A Really% Weird Board! Name^^^','a-really-weird-board-name/'),
        );
    }

    /**
     * Ensure the board correctly calculates the page for the latest post in it
     * 
     * @param int $replies
     * @param int $perPage
     * @param int $expected
     * @dataProvider providerCalcLastPostPage
     */
    public function testCalcLastPostPage($replies,$perPage,$expected) {
        $this->board->set('last_post_replies',$replies);
        $this->modx->setOption('discuss.post_per_page',$perPage);
        $page = $this->board->calcLastPostPage();
        $this->assertEquals($expected,$page);
    }
    /**
     * @return array
     */
    public function providerCalcLastPostPage() {
        return array(
            array(5,10,1),
            array(40,10,4),
            array(42,10,5),
            array(0,10,1),
            array(124,100,2),
        );
    }
}

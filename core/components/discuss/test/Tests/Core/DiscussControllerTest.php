<?php
/**
 * @package discuss-test
 */
/**
 * Tests related to basic Discuss class methods
 *
 * @package discuss-test
 * @group Core
 * @group Controllers
 */
class DiscussControllerTest extends DiscussTestCase {

    /**
     * @var DiscussController|PHPUnit_Framework_MockObject_MockObject $controller
     */
    public $controller;

    public function setUp() {
        parent::setUp();
        $this->modx->loadClass('DiscussController',$this->discuss->config['modelPath'].'discuss/',true,true);
        $this->controller = $this->getMockForAbstractClass('DiscussController',array(&$this->discuss));
        $this->controller->expects($this->any())
            ->method('process')
            ->will($this->returnValue(array()));
        $this->controller->expects($this->any())
            ->method('getSessionPlace')
            ->will($this->returnValue(''));
        $this->controller->expects($this->any())
            ->method('getPageTitle')
            ->will($this->returnValue(''));
    }

    /**
     * @param string $key
     * @param mixed $value
     * @dataProvider providerSetPlaceholder
     */
    public function testSetPlaceholder($key,$value) {
        $this->controller->setPlaceholder($key,$value);
        $this->assertEquals($value,$this->controller->placeholders[$key]);
    }
    public function providerSetPlaceholder() {
        return array(
            array('one',1),
        );
    }
}

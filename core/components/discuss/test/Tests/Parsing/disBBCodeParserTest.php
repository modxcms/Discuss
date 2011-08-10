<?php
/**
 * @package discuss-test
 */
/**
 * Test the BBCode parsing
 *
 * @package discuss-test
 * @group Core
 * @group Parser
 * @group BBCode
 */
class disBBCodeParserTest extends DiscussTestCase {
    /** @var disPost $post */
    public $post;
    /** @var disBBCodeParser $parser */
    public $parser;

    public function setUp() {
        parent::setUp();
        $this->post = $this->modx->newObject('disPost');
        $this->post->fromArray(array(
            'id' => 12345,
            'title' => 'Unit Test Parser Post',
        ),'',true,true);

        $this->post->loadParser();
        $this->parser =& $this->post->parser;
    }

    /**
     * Test [b] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerBold
     */
    public function testBold($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerBold() {
        return array(
            array('[b]Test[/b]','<strong>Test</strong>'),
            array('[b][/b]Test','<strong></strong>Test'),
            array('[b][i]Test[/b][/i]','<strong><em>Test</strong></em>'),
        );
    }

    /**
     * Test [i] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerItalic
     */
    public function testItalic($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerItalic() {
        return array(
            array('[i]Test[/i]','<em>Test</em>'),
            array('[i][/i]Test','<em></em>Test'),
            array('[b][i]Test[/b][/i]','<strong><em>Test</strong></em>'),
        );
    }

    /**
     * Test [u] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerUnderline
     */
    public function testUnderline($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerUnderline() {
        return array(
            array('[u]Test[/u]','<span style="text-decoration: underline;">Test</span>'),
            array('[u][/u]Test','<span style="text-decoration: underline;"></span>Test'),
            array('[b][u]Test[/b][/u]','<strong><span style="text-decoration: underline;">Test</strong></span>'),
        );
    }

    /**
     * Test [s] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerStrikeThrough
     */
    public function testStrikeThrough($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerStrikeThrough() {
        return array(
            array('[s]Test[/s]','<del>Test</del>'),
            array('[s][/s]Test','<del></del>Test'),
            array('[b][s]Test[/b][/s]','<strong><del>Test</strong></del>'),
        );
    }

    /**
     * Test [hr] tag
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerHr
     */
    public function testHr($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerHr() {
        return array(
            array('[hr]','<hr />'),
            array('Test[hr]','Test<hr />'),
            array('[b][hr][/b]','<strong><hr /></strong>'),
        );
    }

    /**
     * Test [sup] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerSuperScript
     */
    public function testSuperScript($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerSuperScript() {
        return array(
            array('[sup]Test[/sup]','<sup>Test</sup>'),
            array('[sup][/sup]Test','<sup></sup>Test'),
            array('[b][sup]Test[/b][/sup]','<strong><sup>Test</strong></sup>'),
        );
    }

    /**
     * Test [sub] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerSubScript
     */
    public function testSubScript($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerSubScript() {
        return array(
            array('[sub]Test[/sub]','<sub>Test</sub>'),
            array('[sub][/sub]Test','<sub></sub>Test'),
            array('[b][sub]Test[/b][/sub]','<strong><sub>Test</strong></sub>'),
        );
    }

    /**
     * Test [tt] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerTeletype
     */
    public function testTeletype($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerTeletype() {
        return array(
            array('[tt]Test[/tt]','<tt>Test</tt>'),
            array('[tt][/tt]Test','<tt></tt>Test'),
            array('[b][tt]Test[/b][/tt]','<strong><tt>Test</strong></tt>'),
        );
    }

    /**
     * Test [url] tags, along with the url parameter
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerUrl
     */
    public function testUrl($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result,'Result of [url] tests did not pass.');
    }
    /**
     * @return array
     */
    public function providerUrl() {
        return array(
            array('[url]http://modx.com/[/url]','<a href="http://modx.com/" target="_blank" rel="nofollow">http://modx.com/</a>'),
            array('[url=http://modx.com/]MODX[/url]','<a href="http://modx.com/" target="_blank" rel="nofollow">MODX</a>'),
            array('[url=ftp://modx.com/]MODX[/url]','<a href="http://modx.com/" target="_blank" rel="nofollow">MODX</a>'),
        );
    }

}
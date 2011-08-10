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

    /**
     * Test [color] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerColor
     */
    public function testColor($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result,'Result of [color] tests did not pass.');
    }
    /**
     * @return array
     */
    public function providerColor() {
        return array(
            array('[color=red]Test[/color]','<span style="color:red;">Test</span>'),
            array('[color=#abcdef]Test[/color]','<span style="color:#abcdef;">Test</span>'),
            array('[color]Test[/color]','<span>Test</span>'),
        );
    }

    /**
     * Test [email] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerEmail
     */
    public function testEmail($string,$expected) {
        $result = $this->parser->parse($string);
        $result = html_entity_decode($result,ENT_COMPAT,'UTF-8');
        $this->assertEquals($expected,$result,'Result of [email] test did not pass.');
    }
    /**
     * @return array
     */
    public function providerEmail() {
        return array(
            array('[email=hello@modx.com]MODX[/email]','<a href="mailto:hello@modx.com" rel="nofollow">MODX</a>'),
            array('[email]hello@modx.com[/email]','<a href="mailto:hello@modx.com" rel="nofollow">hello<em>@</em>modx.com</a>'),
        );
    }

    /**
     * Test [list] and [li] tags
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerList
     */
    public function testList($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result,'Result of [list] and [li] test did not pass.');
    }
    /**
     * @return array
     */
    public function providerList() {
        return array(
            array('[list][li]Test[/li][/list]','<ul class="dis-ul"><li>Test</li></ul>'),
            array('[list][li]Test[/li][li]Another[/li][/list]','<ul class="dis-ul"><li>Test</li><li>Another</li></ul>'),
            array('[list][li]Test[li]Another[/li][/list]','<ul class="dis-ul"><li>Test</li><li>Another</li></ul>'),
        );
    }

    /**
     * Test [code] tag
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerCode
     */
    public function testCode($string,$expected) {
        $result = $this->parser->parse($string);
        $result = html_entity_decode($result,ENT_COMPAT,'UTF-8');
        $this->assertEquals($expected,$result,'Result of [code] test did not pass.');
    }
    /**
     * @return array
     */
    public function providerCode() {
        return array(
            array('[code]<?php echo "test";[/code]','<div class="dis-code"><pre class="brush: php; toolbar: false"><?php echo "test";</pre></div>'),
            array('[code=php]<?php echo "test";[/code]','<div class="dis-code"><pre class="brush: php; toolbar: false"><?php echo "test";</pre></div>'),
            array('[code=sql]TRUNCATE my_table;[/code]','<div class="dis-code"><pre class="brush: sql; toolbar: false">TRUNCATE my_table;</pre></div>'),
            array('[code=js]alert("test");[/code]','<div class="dis-code"><pre class="brush: js; toolbar: false">alert("test");</pre></div>'),
        );
    }
}
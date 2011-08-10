<?php
/**
 * @package discuss-test
 */
/**
 * Test more complex BBCode parsing
 *
 * @package discuss-test
 * @group Core
 * @group Parser
 * @group BBCode
 */
class disBBCodeParserComplexTest extends DiscussTestCase {
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
     * Prevent broken or doubled [/code] tags
     * 
     * @param string $string
     * @param string $expected
     * @dataProvider providerBrokenCodeTags
     */
    public function testBrokenCodeTags($string,$expected) {
        $result = $this->parser->parse($string);
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerBrokenCodeTags() {
        return array(
            array('[code]code here','<div class="dis-code"><pre class="brush: php; toolbar: false">code here</pre></div>'),
            array('[code]code here[/code][/code][b]test[/b]','<div class="dis-code"><pre class="brush: php; toolbar: false">code here</pre></div><strong>test</strong>'),
        );
    }

    /**
     * Prevent attributes in html tags without quotes; usually malicious hacking attempts
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerAttributesWithoutQuotes
     */
    public function testAttributesWithoutQuotes($string,$expected) {
        $result = $this->parser->parse($string);
        $result = html_entity_decode($result,ENT_COMPAT,'UTF-8');
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerAttributesWithoutQuotes() {
        return array(
            array('<strong attr=1>Test</strong>','<strong>Test</strong>'),
            array('<strong attr=t>Test</strong>','<strong>Test</strong>'),
            array('<strong attr=0>Test</strong>','<strong>Test</strong>'),
        );
    }

    /**
     * Prevent script tags at all costs
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerStripScriptTags
     */
    public function testStripScriptTags($string,$expected = '') {
        $result = $this->parser->parse($string);
        $result = html_entity_decode($result,ENT_COMPAT,'UTF-8');
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerStripScriptTags() {
        return array(
            array('<script type="text/javascript">alert("test");</script>'),
            array('<script type="text/javascript">alert("test");</script>Test','Test'),
            array('<script type=text/javascript>alert("test");</script>'),
            array('<script type=javascript>alert("test");</script>'),
            array('<script>alert("test");</script>'),
            array('<script >alert("test");</script>'),
            array('<script zz>alert("test");</script>'),
            array('<script zz=>alert("test");</script>'),
            array('<script >alert("test");</script >'),
            array('<script>alert("test");</script zz="test">'),
        );
    }

    /**
     * Prevent malicious html
     *
     * @param string $string
     * @param string $expected
     * @dataProvider providerStripMaliciousHtml
     */
    public function testStripMaliciousHtml($string,$expected = '') {
        $result = $this->parser->parse($string);
        $result = html_entity_decode($result,ENT_COMPAT,'UTF-8');
        $this->assertEquals($expected,$result);
    }
    /**
     * @return array
     */
    public function providerStripMaliciousHtml() {
        return array(
            array('<iframe src="badpage.html"></iframe>'),
            array('<style>div { display: none; }</style>'),
            array('<style type="text/css">div { display: none; }</style>'),
            array('<form action="h4x.php" method="post"><input type="submit" /></form>'),
            array('<input type="button" name="clickToCrash" onclick="crash();" />'),
            array('<frame src="bad.html"></frame>'),
            array('<frame src="bad.htm" />'),
            array('<object data="boom.swf"></object>'),
            array('<embed src="h4x.c"></embed>'),
            array('<html>test hax</html>'),
        );
    }
}
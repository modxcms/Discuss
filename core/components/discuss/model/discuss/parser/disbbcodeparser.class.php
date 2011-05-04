<?php
/**
 * @package discuss
 * @subpackage parser
 */
class disBBCodeParser {
    public $modx;
    public $discuss;

    function __construct(xPDO &$modx) {
        $this->modx =& $modx;
        $this->discuss =& $modx->discuss;
    }

    /**
     * Parse BBCode in post and return proper HTML. Supports SMF/Vanilla formats.
     *
     * @param string $message The string to parse
     * @return string The parsed string with HTML instead of BBCode, and all code stripped
     */
    public function parse($message) {
        /* handle quotes better, to allow for citing */
        $message = $this->parseQuote($message);
        $message = $this->parseBasic($message);
        $message = $this->parseSmileys($message);
        $message = $this->parseList($message);
        $message = $this->convertLinks($message);

        /* auto-add br tags to linebreaks for pretty formatting */
        $message = $this->_nl2br2($message);

        $message = $this->parseSandboxed($message);

        /* strip MODX tags */
        $message = str_replace(array('[',']'),array('&#91;','&#93;'),$message);
        return $message;
    }

    /**
     * Parse BBCode from vanilla/smf boards BBCode formats
     * 
     * @param string $message
     * @return string
     */
    public function parseBasic($message) {
        $message = preg_replace("#\[b\](.*?)\[/b\]#si",'<strong>\\1</strong>',$message);
        $message = preg_replace("#\[i\](.*?)\[/i\]#si",'<em>\\1</em>',$message);
        $message = preg_replace("#\[u\](.*?)\[/u\]#si",'<span style="text-decoration: underline;">\\1</span>',$message);
        $message = preg_replace("#\[s\](.*?)\[/s\]#si",'<del>\\1</del>',$message);
        $message = str_ireplace("[hr]",'<hr />',$message);
        $message = preg_replace("#\[sup\](.*?)\[/sup\]#si",'<sup>\\1</sup>',$message);
        $message = preg_replace("#\[sub\](.*?)\[/sub\]#si",'<sub>\\1</sub>',$message);
        $message = preg_replace("#\[ryan\](.*?)\[/ryan\]#si",'<blink>\\1</blink>',$message);
        $message = preg_replace("#\[tt\](.*?)\[/tt\]#si",'<tt>\\1</tt>',$message);
        $message = preg_replace("#\[rtl\](.*?)\[/rtl\]#si",'<div dir="rtl">\\1</div>',$message);


        /* align tags */
        $message = preg_replace("#\[center\](.*?)\[/center\]#si",'<div style="text-align: center;">\\1</div>',$message);
        $message = preg_replace("#\[right\](.*?)\[/right\]#si",'<div style="text-align: right;">\\1</div>',$message);
        $message = preg_replace("#\[left\](.*?)\[/left\]#si",'<div style="text-align: left;">\\1</div>',$message);


        $message = preg_replace("#\[cite\](.*?)\[/cite\]#si",'<blockquote>\\1</blockquote>',$message);
        $message = preg_replace("#\[hide\](.*?)\[/hide\]#si",'\\1',$message);
        $message = preg_replace_callback("#\[email\]([^/]*?)\[/email\]#si",array('disBBCodeParser','parseEmailCallback'),$message);
        $message = preg_replace("#\[url\]([^/]*?)\[/url\]#si",'<a href="http://\\1">\\1</a>',$message);
        $message = preg_replace("#\[url\](.*?)\[/url\]#si",'\\1',$message);
        $message = preg_replace("#\[magic\](.*?)\[/magic\]#si",'<marquee>\\1</marquee>',$message);
        $message = preg_replace("#\[url=[\"']?(.*?)[\"']?\](.*?)\[/url\]#si",'<a href="\\1">\\2</a>',$message);
        $message = preg_replace("#\[php\](.*?)\[/php\]#si",'<pre class="brush:php">\\1</pre>',$message);
        $message = preg_replace("#\[mysql\](.*?)\[/mysql\]#si",'<pre class="brush:sql">\\1</pre>',$message);
        $message = preg_replace("#\[css\](.*?)\[/css\]#si",'<pre class="brush:css">\\1</pre>',$message);
        $message = preg_replace("#\[pre\](.*?)\[/pre\]#si",'<pre>\\1</pre>',$message);
        $message = preg_replace("#\[img=[\"']?(.*?)[\"']?\](.*?)\[/img\]#si",'<img src="\\1" alt="\\2" />',$message);
        $message = preg_replace("#\[img\](.*?)\[/img\]#si",'<img src="\\1" border="0" />',$message);
        $message = str_ireplace(array('[indent]', '[/indent]'), array('<div class="Indent">', '</div>'), $message);

        $message = preg_replace("#\[font=[\"']?(.*?)[\"']?\]#i",'<span style="font-family:\\1;">',$message);
        $message = preg_replace("#\[color=[\"']?(.*?)[\"']?\]#i",'<span style="color:\\1;">',$message);
        $message = preg_replace("#\[size=[\"']?(.*?)[\"']?\]#si",'<span style="font-size:\\1;">',$message);
        $message = str_ireplace(array("[/size]", "[/font]", "[/color]"), "</span>", $message);

        $message = preg_replace('#\[/?left\]#si', '', $message);

        return $message;
    }

    /**
     * Parse code blocks where we dont wan't linebreaks, strip them out
     *
     * @param string $message
     * @return mixed
     */
    public function parseSandboxed($message) {
        $message = preg_replace_callback("#\[code\](.*?)\[/code\]#si",array('disBBCodeParser','parseCodeCallback'),$message);
        $message = preg_replace_callback("#\[code=[\"']?(.*?)[\"']?\](.*?)\[/code\]#si",array('disBBCodeParser','parseCodeSpecificCallback'),$message);
        return preg_replace('#\[/?code\]#si', '', $message);
    }

    /**
     * Convert [list]/[li] tags
     * 
     * @param string $message
     * @return string
     */
    public function parseList($message) {
        /* convert [list]/[li] tags */
        $message = preg_replace("#\[li\](.*?)\[/li\]#si",'<li>\\1</li>',$message);
        return preg_replace_callback("#\[list\](.*?)\[/list\]#si",array('disBBCodeParser','parseListCallback'),$message);
    }
    public static function parseListCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',disBBCodeParser::stripBRTags($matches[1]));
        $message = '<ul style="margin-top:0;margin-bottom:0;">'.$message.'</ul>';
        return $message;
    }

    /**
     * Auto-convert links to <a> tags
     *
     * @param string $message
     * @return string
     */
    public function convertLinks($message) {
        return preg_replace_callback("/(?<!<a href=\")(?<!\")(?<!\">)((?:https?|ftp):\/\/)([\@a-z0-9\x21\x23-\x27\x2a-\x2e\x3a\x3b\/;\x3f-\x7a\x7e\x3d]+)/msxi",array('disBBCodeParser', 'parseLinksCallback'),$message);
    }
    public static function parseLinksCallback($matches) {
        $url = $matches[1].$matches[2];
        $noFollow = ' rel="nofollow"';
        return '<a href="'.$url.'" target="_blank"'.$noFollow.'>'.$url.'</a>';
    }


    public function stripBBCode($str) {
         $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
         $replace = '';
         return preg_replace($pattern, $replace, $str);
    }

    private function _nl2br2($str) {
        $str = str_replace("\r", '', $str);
        return preg_replace('/(?<!>)\n/', "<br />\n", $str);
    }


    public static function parseCodeCallback($matches) {
        $code = disBBCodeParser::stripBRTags($matches[1]);
        return '<div class="dis-code"><pre class="brush: php; toolbar: false">'.$code.'</pre></div>';
    }
    public static function parseCodeSpecificCallback($matches) {
        $type = !empty($matches[1]) ? $matches[1] : 'php';
        $availableTypes = array('applescript','actionscript3','as3','bash','shell','coldfusion','cf','cpp','c','c#','c-sharp','csharp','css','delphi','pascal','diff','patch','pas','erl','erlang','groovy','java','jfx','javafx','js','jscript','javascript','perl','pl','php','text','plain','py','python','ruby','rails','ror','rb','sass','scss','scala','sql','vb','vbnet','xml','xhtml','xslt','html');
        if (!in_array($type,$availableTypes)) $type = 'php';
        $code = disBBCodeParser::stripBRTags($matches[2]);
        return '<div class="dis-code"><pre class="brush: '.$type.'; toolbar: false">'.$code.'</pre></div>';
    }
    public static function parseEmailCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',$matches[1]);
        return disBBCodeParser::encodeEmail($message);
    }

    /**
     * Strip all BR tags from a string
     * @static
     * @param string $str
     * @return string
     */
    public static function stripBRTags($str) {
        return str_replace(array('<br>','<br />','<br/>'),'',$str);
    }

    /**
     * Encode an email address and return the a tag
     *
     * @static
     * @param string $email
     * @param string $emailText
     * @return string
     */
    public static function encodeEmail($email,$emailText = '') {
        $email = disBBCodeParser::obfuscate($email);
        if (empty($emailText)) {
            $emailText = str_replace(array('&#64;','@'),'<em>&#64;</em>',$email);
        }
        return '<a href="mailto:'.$email.'" rel="nofollow">'.$emailText.'</a>';
    }

    /**
     * Obfuscate a string to protect against spammers
     * 
     * @static
     * @param string $text
     * @return string
     */
    public static function obfuscate($text) {
        $result = '';
        for ($i = 0; $i < strlen($text); $i++) {
            $j = rand(0, 1);
            if ($j) {
                $result .= substr($text, $i, 1);
            } else {
                $k = rand(0, 1);
                if ($k) {
                    $result .= '&#' . ord(substr($text, $i, 1)) . ';';
                } else {
                    $result .= '&#x' . sprintf("%x", ord(substr($text, $i, 1))) . ';';
                }
            }
        }
        $k = rand(0, 1);
        if ($k) {
            return str_replace('@', '&#64;', $result);
        } else {
            return str_replace('@', '&#x40;', $result);
        }
    }

    /**
     * Parse a bbcode quote tag and return result
     *
     * @param $message The string to parse
     * @return string The quoted message
     */
    public function parseQuote($message) {
        preg_match_all("#\[quote=?(.*?)[\"']?\](.*?)\[/quote\]#si",$message,$matches);
        if (!empty($matches)) {
            $quotes = array();
            $replace = array();
            $meta = array();
            $with = array();
            if (!empty($matches[0])) {
                foreach ($matches[0] as $match) { $replace[] = $match; }
                foreach ($matches[1] as $match) { $meta[] = $match; }
                foreach ($matches[2] as $match) { $with[] = $match; }
            }
            for ($i=0;$i<count($replace);$i++) {
                $auth = array();
                $mt = explode(' ',$meta[$i]);
                foreach ($mt as $m) {
                    if (empty($m)) continue;
                    $m = explode('=',$m);
                    switch ($m[0]) {
                        case 'author': $auth['user'] = $m[1]; break;
                        case 'date': $auth['date'] = $m[1]; break;
                        case 'link': $auth['link'] = $m[1]; break;
                    }
                }
                $cite = '';
                if (!empty($auth['user']) || !empty($auth['date'])) {
                    $cite = '<cite>Quote';
                    if (!empty($auth['user'])) $cite .= ' from: '.$auth['user'];
                    if (!empty($auth['date'])) $cite .= ' at '.strftime($this->discuss->dateFormat,$auth['date']);
                    $cite .= '</cite>';
                }

                /* strip annoying starting br tags */
                $with[$i] = substr($with[$i],0,6) == '<br />' ? $with[$i] = substr($with[$i],6) : $with[$i];

                /* now insert our quote */
                $message = str_replace($replace[$i],$cite.'<blockquote>'.$with[$i].'</blockquote>',$message);
            }
        }
        return $message;
    }

    /**
     * Parse Smileys
     * 
     * @param string $message
     * @return string
     */
    public function parseSmileys($message) {
        $imagesUrl = $this->discuss->config['imagesUrl'].'smileys/';
        $smiley = array(
            '::)' => 'rolleyes',
            ':)' => 'smiley',
            ';)' => 'wink',
            ':D' => 'laugh',
            ';D' => 'grin',
            '>>:(' => 'angry2',
            '>:(' => 'angry',
            ':(' => 'sad',
            ':o' => 'shocked',
            '8)' => 'cool',
            '???' => 'huh',
            ':P' => 'tongue',
            ':-[' => 'embarrassed',
            ':-X' => 'lipsrsealed',
            ':-*' => 'kiss',
            ':-\\' => 'undecided',
            ":'(" => 'cry',
            '[hug]' => 'bear2',
            '[brew]' => 'brew',
            '[ryan2]' => 'ryan2',
            '[locke]' => 'locke',
            '[zelda]' => 'zelda',
            '[surrender]' => 'surrender',
            '[ninja]' => 'ninja',
            '[spam]' => 'spam',
            '[welcome]' => 'welcome',
            '[offtopic]' => 'offtopic',
            '[hijack]' => 'hijack',
            '[helpme]' => 'help',
            '[banned]' => 'banned',
        );
        $v = array_values($smiley);
        for ($i =0; $i < count($v); $i++) {
            $v[$i] = '<img src="'.$imagesUrl.$v[$i].'.gif" alt="" />';
        }
        return str_replace(array_keys($smiley),$v,$message);
    }

    /**
     * Convert BR tags to newlines
     *
     * @param string $str
     * @return string
     */
    public function br2nl($str) {
        return str_replace(array('<br>','<br />','<br/>'),"\n",$str);
    }

}
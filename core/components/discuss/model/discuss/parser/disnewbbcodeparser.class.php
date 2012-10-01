<?php
/**
 * Discuss
 *
 * Copyright 2010-11 by Shaun McCormick <shaun@modx.com>
 *
 * This file is part of Discuss, a native forum for MODx Revolution.
 *
 * Discuss is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Discuss is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Discuss; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
 * @package discuss
 * @subpackage parser
 */
require_once dirname(__FILE__).'/disparser.class.php';

/**
 * @package discuss
 * @subpackage parser
 */
class disNewBBCodeParser extends disParser {
    /**
     * Parse BBCode in post and return proper HTML. Supports SMF/Vanilla formats.
     *
     * @param string $message The string to parse
     * @param mixed $allowedTags
     * @return string The parsed string with HTML instead of BBCode, and all code stripped
     */
    public function parse($message, array $allowedTags = array()) {
        if (!is_array($allowedTags)) $allowedTags = array();
        $this->allowedTags = $allowedTags;
        $message = $this->checkImageSizes($message);
        $message = $this->cleanAndParse($message);

        /* Parse code & pre blocks separately */
        if ($this->isAllowed('code')) $message = $this->parseCodeBlocks($message);
        if ($this->isAllowed('pre')) $message = $this->parsePreBlocks($message);

        /* Escape all MODX tags */
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
        if ($this->isAllowed('b')) $message = preg_replace("#\[b\](.*?)\[/b\]#si",'<strong>$1</strong>',$message);
        if ($this->isAllowed('i')) $message = preg_replace("#\[i\](.*?)\[/i\]#si",'<em>$1</em>',$message);
        if ($this->isAllowed('u')) $message = preg_replace("#\[u\](.*?)\[/u\]#si",'<u>$1</u>',$message);
        if ($this->isAllowed('s')) $message = preg_replace("#\[s\](.*?)\[/s\]#si",'<del>$1</del>',$message);
        if ($this->isAllowed('hr')) $message = str_ireplace("[hr]",'<hr />',$message);
        if ($this->isAllowed('sup')) $message = preg_replace("#\[sup\](.*?)\[/sup\]#si",'<sup>$1</sup>',$message);
        if ($this->isAllowed('sub')) $message = preg_replace("#\[sub\](.*?)\[/sub\]#si",'<sub>$1</sub>',$message);
        if ($this->isAllowed('rtl')) $message = preg_replace("#\[rtl\](.*?)\[/rtl\]#si",'<div dir="rtl">$1</div>',$message);

        /* align tags */
        if ($this->isAllowed('center')) $message = preg_replace("#\[center\](.*?)\[/center\]#si",'<div style="text-align: center;">$1</div>',$message);
        if ($this->isAllowed('right')) $message = preg_replace("#\[right\](.*?)\[/right\]#si",'<div style="text-align: right;">$1</div>',$message);
        if ($this->isAllowed('left')) $message = preg_replace("#\[left\](.*?)\[/left\]#si",'<div style="text-align: left;">$1</div>',$message);


        if ($this->isAllowed('cite')) $message = preg_replace("#\[cite\](.*?)\[/cite\]#si",'<blockquote>$1</blockquote>',$message);
        $message = preg_replace("#\[hide\](.*?)\[/hide\]#si",'$1',$message);
        if ($this->isAllowed('email')) {
            $message = preg_replace_callback("#\[email=[\"']?(.*?)[\"']?\](.*?)\[/email\]#si",array($this,'parseComplexEmailCallback'),$message);
            $message = preg_replace_callback("#\[email\]([^/]*?)\[/email\]#si",array($this,'parseEmailCallback'),$message);
        }
        if ($this->isAllowed('url')) {
            $message = str_replace(array('[iurl','[/iurl]'),array('[url','[/url]'),$message);
            $message = preg_replace("#\[url\]([^/]*?)\[/url\]#si",'<a href="http://$1">$1</a>',$message);
            $message = preg_replace_callback("#\[url\](.*?)\[/url\]#si",array($this,'parseSimpleUrlCallback'),$message);
            $message = preg_replace_callback("#\[url=[\"']?(.*?)[\"']?\](.*?)\[/url\]#si",array($this,'parseUrlCallback'),$message);
        }
        if ($this->isAllowed('magic')) $message = preg_replace("#\[magic\](.*?)\[/magic\]#si",'<marquee>$1</marquee>',$message);
        if ($this->isAllowed('code')) {
            $message = preg_replace("#\[php\](.*?)\[/php\]#si",'<pre class="brush:php">$1</pre>',$message);
            $message = preg_replace("#\[mysql\](.*?)\[/mysql\]#si",'<pre class="brush:sql">$1</pre>',$message);
            $message = preg_replace("#\[sql\](.*?)\[/sql\]#si",'<pre class="brush:sql">$1</pre>',$message);
            $message = preg_replace("#\[css\](.*?)\[/css\]#si",'<pre class="brush:css">$1</pre>',$message);
        }
        if ($this->isAllowed('pre')) $message = preg_replace("#\[pre\](.*?)\[/pre\]#si",'<pre>$1</pre>',$message);

        if ($this->isAllowed('img')) {
            $message = preg_replace_callback("#\[img\s[\"']?(.*?)[\"']?\](.*?)\[/img\]#si",array($this,'parseImageCallback'),$message);
            $message = preg_replace("#\[img=[\"']?(.*?)[\"']?\](.*?)\[/img\]#si",'<img src="$1" alt="$2" />',$message);
            $message = preg_replace("#\[img\](.*?)\[/img\]#si",'<img src="$1" border="0" />',$message);
        }
        if ($this->isAllowed('indent')) $message = str_ireplace(array('[indent]', '[/indent]'), array('<div class="Indent">', '</div>'), $message);

        if ($this->isAllowed('font')) $message = preg_replace("#\[font=[\"']?(.*?)[\"']?\]#i",'<span style="font-family:$1;">',$message);
        if ($this->isAllowed('color')) $message = preg_replace("#\[color=[\"']?(.*?)[\"']?\]#i",'<span style="color:$1;">',$message);
        if ($this->isAllowed('size')) $message = preg_replace_callback("#\[size=[\"']?(.*?)[\"']?\]#si",array($this,'parseSizeCallback'),$message);
        $message = str_replace(array('[color]','[size]','[font]'),'<span>',$message);/* cleanup improper span/color/font tags */
        $message = str_ireplace(array("[/size]", "[/font]", "[/color]"), "</span>", $message);

        $message = preg_replace('#\[/?left\]#si', '', $message);

        return $message;
    }

    public static function parseImageCallback($matches) {
        $width = '';
        $height = '';
        if (!empty($matches[1])) {
            $p = explode(' ',$matches[1]);
            foreach ($p as $part) {
                $dim = explode('=',$part);
                if (!empty($dim[1])) {
                    $$dim[0] = $dim[0].'="'.$dim[1].'"';
                }
            }
        }
        return '<img src="'.$matches[2].'" alt="" '.$width.' '.$height.' />';
    }

    /**
     * Handle smf-style img heights, preventing too large images
     *
     * @static
     * @param string $message
     * @return string
     */
    public function checkImageSizes($message) {
        $maxWidth = $this->modx->getOption('discuss.max_image_width',null,500);
        $maxHeight = $this->modx->getOption('discuss.max_image_height',null,500);
		preg_match_all('~\[img(\s+width=\d+)?(\s+height=\d+)?(\s+width=\d+)?\](.+?)\[/img\]~is', $message, $matches, PREG_PATTERN_ORDER);
		$replaces = array();
		foreach ($matches[0] as $k => $v) {
		    /* handle preg foo */
			$matches[1][$k] = !empty($matches[3][$k]) ? $matches[3][$k] : $matches[1][$k];
			$adjustedWidth = !empty($matches[1][$k]) ? (int) substr(trim($matches[1][$k]), 6) : 0;
			$adjustedHeight = !empty($matches[2][$k]) ? (int) substr(trim($matches[2][$k]), 7) : 0;

			/* skip if ok */
			if ($adjustedWidth <= $maxWidth && $adjustedHeight <= $maxHeight)
				continue;

			/* if too wide */
			if ($adjustedWidth > $maxWidth && !empty($maxWidth)) {
				$adjustedHeight = (int) ($maxWidth * $adjustedHeight) / $adjustedWidth;
				$adjustedWidth = $maxWidth;
			}
			/* if too high */
			if ($adjustedHeight > $maxHeight && !empty($maxHeight)) {
				$adjustedWidth = (int) (($maxHeight * $adjustedWidth) / $adjustedHeight);
				$adjustedHeight = $maxHeight;
			}

			$replaces[$matches[0][$k]] = '[img' . (!empty($adjustedWidth) ? ' width=' . $adjustedWidth : '') . (!empty($adjustedHeight) ? ' height=' . $adjustedHeight : '') . ']' . $matches[4][$k] . '[/img]';
		}

		/* if we replaced tags */
		if (!empty($replaces)) {
			$message = strtr($message, $replaces);
        }
	    return $message;
    }

    /**
     * Strip all invalid HTML
     *
     * @param string $message The content to parse
     * @return string The stripped and cleaned content
     */
    public function stripHtml($message) {
        $message = preg_replace(array(
            "@<iframe[^>]*?>.*?</iframe>@siu",
            "@<iframe.*?/>@siu",
            "@<frameset[^>]*?>.*?</frameset>@siu",
            "@<frameset.*?/>@siu",
            "@<form[^>]*?>.*?</form>@siu",
            "@<input[^>]*?>.*?</input>@siu",
            "@<input.*?/>@siu",
            "@<select[^>]*?>.*?</select>@siu",
            "@<select.*?/>@siu",
            "@<frame[^>]*?>.*?</frame>@siu",
            "@<frame.*?/>@siu",
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            "@<script.*?/>@siu",
            '@<object[^>]*?.*?</object>@siu',
            "@<object.*?/>@siu",
            '@<embed[^>]*?.*?</embed>@siu',
            "@<embed.*?/>@siu",
            '@<applet[^>]*?.*?</applet>@siu',
            "@<applet.*?/>@siu",
            '@<noframes[^>]*?.*?</noframes>@siu',
            "@<noframes.*?/>@siu",
            '@<noscript[^>]*?.*?</noscript>@siu',
            "@<noscript.*?/>@siu",
            '@<noembed[^>]*?.*?</noembed>@siu',
            "@<noembed.*?/>@siu",
            /*'@<div[^>]*?.*?</div>@siu', These 2 lines prevent BBCodes from applying special formatting
            '@<span[^>]*?.*?</span>@siu',*/
            '@<body[^>]*?.*?</body>@siu',
            "@<body.*?/>@siu",
            '@<html[^>]*?.*?</html>@siu',
            "@<html.*?/>@siu",
        ),'',$message);

        /* strip HTML comments */
        $message = preg_replace("#\<!--(.*?)--\>#si",'',$message);
        $message = str_replace('<!--','',$message);
        $message = str_replace('-->','',$message);

        return $message;
    }

    /**
     * Parse [url]url here[/url] calls
     * @static
     * @param $matches
     * @return string
     */
    public static function parseSimpleUrlCallback($matches) {
        $url = str_replace(array('javascript:','ftp:'),'http:',strip_tags($matches[1]));
        return '<a href="'.$url.'" target="_blank" rel="nofollow">'.$url.'</a>';
    }

    /**
     * Prevent javascript:/ftp: injections via URLs
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseUrlCallback($matches) {
        $url = str_replace(array('javascript:','ftp:'),'http:',strip_tags($matches[1]));
        return '<a href="'.$url.'" target="_blank" rel="nofollow">'.$matches[2].'</a>';
    }

    /**
     * Parse [size] tags
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseSizeCallback($matches) {
        $size = intval(str_replace(array('pt','px','em'),'',$matches[1]));
        if ($size > 24) $size = 24;
        if ($size < 6) $size = 6;
        return '<span style="font-size:'.$size.'px;">';
    }

    /**
     * Parse [code] tags
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseCodeCallback($matches) {
        $code = $matches[1];
        return '<div class="dis-code"><pre class="brush: php; toolbar: false">'.$code.'</pre></div>';
    }
    /**
     * Parse [code=language] tags
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseCodeSpecificCallback($matches) {
        $type = !empty($matches[1]) ? $matches[1] : 'php';
        $availableTypes = array('applescript','actionscript3','as3','bash','shell','coldfusion','cf','cpp','c','c#','c-sharp','csharp','css','delphi','pascal','diff','patch','pas','erl','erlang','groovy','java','jfx','javafx','js','jscript','javascript','perl','pl','php','text','plain','py','python','ruby','rails','ror','rb','sass','scss','scala','sql','vb','vbnet','xml','xhtml','xslt','html');
        if (!in_array($type,$availableTypes)) $type = 'php';
        $code = $matches[2];
        return '<div class="dis-code"><pre class="brush: '.$type.'; toolbar: false">'.$code.'</pre></div>';
    }
    /**
     * Parse [email] tags
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseEmailCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',$matches[1]);
        return self::encodeEmail($message);
    }
    /**
     * Parse [email=] tags
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseComplexEmailCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',$matches[1]);
        if (empty($matches[2])) $matches[2] = $matches[1];
        return self::encodeEmail($message,$matches[2]);
    }

    /**
     * Parse code blocks where we dont wan't linebreaks, strip them out
     *
     * @param string $message
     * @return mixed
     */
    public function parseCodeBlocks($message) {
        $message = preg_replace_callback("#\[code\](.*?)\[/code\]#si",array($this,'parseCodeCallback'),$message);
        $message = preg_replace_callback("#\[code=[\"']?(.*?)[\"']?\](.*?)\[/code\]#si",array($this,'parseCodeSpecificCallback'),$message);
        return preg_replace('#\[/?code\]#si', '', $message);
    }
    /**
     * Parse pre blocks
     *
     * @param string $message
     * @return mixed
     */
    public function parsePreBlocks($message) {
        $message = preg_replace("#\[pre\](.*?)\[/pre\]#si","<pre>\\1</pre>",$message);
        return preg_replace('#\[/?pre\]#si', '', $message);
    }

    /**
     * Convert [list]/[olist]/[li] tags
     *
     * @param string $message
     * @return string
     */
    public function parseList($message) {
        /* convert [list]/[li] tags */
        $message = preg_replace_callback("#\[list\](.*?)\[/list\]#si",array($this,'parseListCallback'),$message);
        $message = preg_replace_callback("#\[ul\](.*?)\[/ul\]#si",array($this,'parseListCallback'),$message);
        $message = preg_replace_callback("#\[olist\](.*?)\[/olist\]#si",array($this,'parseOListCallback'),$message);
        $message = preg_replace_callback("#\[ol\](.*?)\[/ol\]#si",array($this,'parseOListCallback'),$message);
        return $message;
    }
    public static function parseListItems($message) {
        $message = preg_replace("#\[li\](.*?)\[/li\]#si",'<li>\\1</li>',$message);
        $message = preg_replace("#\[[\*\#]\](.*?)\[\/[\*\#]\]#si", '<li>\\1</li>', $message);
        $message = preg_replace("#\[[\*\#]\](.*?)($|\n|\[\/(list|ul|ol)\])#", '<li>\\1</li>\\2', $message);
        return $message;
    }
    /**
     * Parse [list] tags
     * @static
     * @param array $matches
     * @return mixed|string
     */
    public static function parseListCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',self::stripBRTags($matches[1]));
        $message = self::parseListItems($message);
        $message = '<ul class="dis-ul">'.$message.'</ul>';
        return $message;
    }
    /**
     * Parse [olist] tags
     * @static
     * @param array $matches
     * @return mixed|string
     */
    public static function parseOListCallback($matches) {
        if (empty($matches[1])) return '';
        $message = str_replace(array('<br>','<br />','<br/>'),'',self::stripBRTags($matches[1]));
        $message = self::parseListItems($message);
        $message = '<ol class="dis-ol">'.$message.'</ol>';
        return $message;
    }

    /**
     * Auto-convert links to <a> tags
     *
     * @param string $message
     * @return string
     */
    public function convertLinks($message) {
        return preg_replace_callback("/(?<!<a href=\")(?<!\")(?<!\">)((?:https?|ftp):\/\/)([\@a-z0-9\x21\x23-\x27\x2a-\x2e\x3a\x3b\/;\x3f-\x7a\x7e\x3d]+)/msxi",array($this, 'parseLinksCallback'),$message);
    }
    /**
     * Parse [url] tags
     * @static
     * @param $matches
     * @return string
     */
    public static function parseLinksCallback($matches) {
        $url = $matches[1].$matches[2];
        $hasQuote = false;
        if (substr($url, -strlen('&quot;')) == '&quot;') {
            $hasQuote = true;
            $url = substr($url, 0, strlen($url) - strlen('&quot;'));
        }
        $noFollow = ' rel="nofollow"';
        return '<a href="'.$url.'" target="_blank"'.$noFollow.'>'.$url.'</a>'. (($hasQuote) ? '&quot;' : '');
    }

    /**
     * Strip all BBCode from a string
     *
     * @param string $str
     * @return string
     */
    public function stripBBCode($str) {
         $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
         $replace = '';
         return preg_replace($pattern, $replace, $str);
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
        $email = self::obfuscate($email);
        if (empty($emailText)) {
            $emailText = $email;
        }
        $emailText = str_replace(array('&#64;','&#x40;','@'),'<em>&#64;</em>',$emailText);
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
        $new_string = str_replace('[/quote]', '</blockquote>', $message);
        $message = preg_replace_callback('/\[quote(.*?)\]/msi',array($this,'parseQuoteCallback'), $new_string);
        return $message;
    }
    /**
     * Parse [quote] tags and append information about them
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseQuoteCallback($matches) {
        $attributes = array();
        $attrs = explode(' ',$matches[1]);
        foreach ($attrs as $v) {
            if (empty($v)) continue;
            $as = explode('=',$v);
            if (!empty($as[1])) {
                $attributes[$as[0]] = $as[1];
            }
        }
        $citation = '';
        if (!empty($attributes)) {
            if (!empty($attributes['user']) || !empty($attributes['date']) || !empty($attributes['author'])) {
                $citation = '<cite>Quote';
                if (!empty($attributes['author'])) $citation .= ' from: '.$attributes['author'];
                if (!empty($attributes['user'])) $citation .= ' from: '.$attributes['user'];
                if (!empty($attributes['date'])) $citation .= ' at '.strftime('%b %d, %Y, %I:%M %p',$attributes['date']);
                $citation .= '</cite>';
            }
        }

        return $citation.'<blockquote>';
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
            ' ::)' => 'rolleyes',
            ' :)' => 'smiley',
            ' :-)' => 'smiley',
            ' ;)' => 'wink',
            ' :D' => 'laugh',
            ' ;D' => 'grin',
            ' >>:(' => 'angry2',
            ' >:(' => 'angry',
            ' :(' => 'sad',
            ' :o' => 'shocked',
            ' 8)' => 'cool',
            ' ???' => 'huh',
            ' :P' => 'tongue',
            ' :-[' => 'embarrassed',
            ' :x' => 'lipsrsealed',
            ' :X' => 'lipsrsealed',
            ' :-X' => 'lipsrsealed',
            ' :-*' => 'kiss',
            ' :-\\' => 'undecided',
            " :'(" => 'cry',
            ' [hug]' => 'bear2',
            ' [brew]' => 'brew',
            ' [ryan2]' => 'ryan2',
            ' [locke]' => 'locke',
            ' [zelda]' => 'zelda',
            ' [surrender]' => 'surrender',
            ' [ninja]' => 'ninja',
            ' [spam]' => 'spam',
            ' [welcome]' => 'welcome',
            ' [offtopic]' => 'offtopic',
            ' [hijack]' => 'hijack',
            ' [helpme]' => 'help',
            ' [banned]' => 'banned',
        );
        $v = array_values($smiley);
        for ($i =0; $i < count($v); $i++) {
            $v[$i] = ' <img src="'.$imagesUrl.$v[$i].'.gif" alt="'.$v[$i].'" />';
        }
        return str_replace(array_keys($smiley),$v,$message);
    }

    /**
     * Clean and parse the message with a custom BB Code parser.
     *
     * @param string $message
     * @return string
     */
    public function cleanAndParse ($message) {
        /* leave only \n linebreaks */
	    $message = strtr($message, array("\r" => ''));

	    /* convert from smf imported tags, entities */
        $message = str_replace('&nbsp;',' ',$message);
	    $message = html_entity_decode($message,ENT_COMPAT,'UTF-8');
	    $message = str_replace('&#039;',"'",$message);

        /* nuke any extra [/quote] tags */
        while (substr($message, -7) == '[quote]') {
            $message = substr($message, 0, -7);
        }
        while (substr($message, 0, 8) == '[/quote]') {
            $message = substr($message, 8);
        }

        $codeopen = preg_match_all('~(\[code(?:=[^\]]+)?\])~is', $message, $dummy);
        $codeclose = preg_match_all('~(\[/code\])~is', $message, $dummy);

        /* Close/open all code tags... */
        if ($codeopen > $codeclose)
            $message .= str_repeat('[/code]', $codeopen - $codeclose);
        elseif ($codeclose > $codeopen)
            $message = str_repeat('[code]', $codeclose - $codeopen) . $message;

        /* Fix tags outside of [code] tags */
        $parts = preg_split('~(\[/code|pre\]|\[code|pre(?:=[^\]]+)?\])~i', $message, -1, PREG_SPLIT_DELIM_CAPTURE);

        $nbs = '\xA0';
        $charset = $this->modx->getOption('modx_charset',null,'UTF-8');

        /* Only mess with stuff outside [code] tags. */
        $z = 0;
        for ($i = 0, $n = count($parts); $i < $n; $i++) {
            /* $z #s -> 0 = outside, 1 = begin tag, 2 = inside, 3 = close tag, repeat. */
            if ($z == 2) {
                /* convert all code in [code] to htmlent */
                $parts[$i] = htmlentities($parts[$i], null, 'UTF-8');
            }
            if ($z == 0 || $z == 4) {
                $z = 0;
                $parts[$i] = str_replace(array('<br>','<br />','<br/>'),"\n",$parts[$i]);

                /* Fix amount of open/closed link tags */
                $listOpen = substr_count($parts[$i], '[list]') + substr_count($parts[$i], '[list ');
                $listClose = substr_count($parts[$i], '[/list]');
                if ($listClose - $listOpen > 0) {
                    $parts[$i] = str_repeat('[list]', $listClose - $listOpen) . $parts[$i];
                }
                if ($listOpen - $listClose > 0) {
                    $parts[$i] = $parts[$i] . str_repeat('[/list]', $listOpen - $listClose);
                }

                /* Make sure all tags are lowercase. */
                $parts[$i] = preg_replace('~\[([/]?)(list|li)((\s[^\]]+)*)\]~ie', '\'[$1\' . strtolower(\'$2\') . \'$3]\'', $parts[$i]);

                /* get rid of malicious urls */
                $parts[$i] = preg_replace('~&lt;a\s+href=((?:&quot;)?)((?:https?://|ftps?://|mailto:)\S+?)\\1&gt;~i', '[url=$2]$2[/url]', $parts[$i]);
                $parts[$i] = preg_replace('~&lt;/a&gt;~i', '', $parts[$i]);

                /* Make every html so far (which should only be what the user entered) into entities */
                $parts[$i] = htmlspecialchars($parts[$i], ENT_QUOTES, 'UTF-8');

                /* Parse the rest of the BB Code stuff. */
                if ($this->isAllowed('quote')) $parts[$i] = $this->parseQuote($parts[$i]);
                if ($this->isAllowed('list')) $parts[$i] = $this->parseList($parts[$i]);
                $parts[$i] = $this->parseBasic($parts[$i]);
                if ($this->isAllowed('url')) $parts[$i] = $this->convertLinks($parts[$i]);
                $parts[$i] = $this->stripBadWords($parts[$i]);
                if ($this->isAllowed('smileys')) $parts[$i] = $this->parseSmileys($parts[$i]);

                /* Strip out possibly malicious html */
                $parts[$i] = $this->stripHtml($parts[$i]);
                $parts[$i] = $this->cleanupImg($parts[$i]);

                /* Make linebreaks visible */
                $parts[$i] = $this->_nl2br2($parts[$i]);
            }

            $z++;

            $message = implode('', $parts);
        }
        return $message;
    }

    /**
     * Cleanup and sanitize [img] tags to prevent injections
     * @param string $message
     * @return string
     */
    public function cleanupImg($message) {
        preg_match_all('~&lt;img\s+src=((?:&quot;)?)((?:https?://|ftps?://)\S+?)\\1(?:\s+alt=(&quot;.*?&quot;|\S*?))?(?:\s?/)?&gt;~i', $message, $matches, PREG_PATTERN_ORDER);
        if (!empty($matches[0])) {
            $replaces = array();
            foreach ($matches[2] as $match => $imgTag) {
                $alt = empty($matches[3][$match]) ? '' : ' alt=' . preg_replace('~^&quot;|&quot;$~', '', $matches[3][$match]);

                /* strip action from url to prevent redirection */
                if (preg_match('~action(=|%3d)(?!dlattach)~i', $imgTag) != 0) {
                    $imgTag = preg_replace('~action(=|%3d)(?!dlattach)~i', 'action-', $imgTag);
                }

                $replaces[$matches[0][$match]] = '[img' . $alt . ']' . $imgTag . '[/img]';
            }

            $message = strtr($message, $replaces);
        }
        return $message;
    }
}

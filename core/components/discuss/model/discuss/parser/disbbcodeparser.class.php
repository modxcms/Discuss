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
 */
require_once dirname(__FILE__).'/disparser.class.php';

/**
 * @package discuss
 * @subpackage parser
 */
class disBBCodeParser extends disParser {
    /**
     * Parse BBCode in post and return proper HTML. Supports SMF/Vanilla formats.
     *
     * @param string $message The string to parse
     * @return string The parsed string with HTML instead of BBCode, and all code stripped
     */
    public function parse($message) {

        $message = $this->preClean($message);
        
        /* handle quotes better, to allow for citing */
        $message = $this->parseQuote($message);
        $message = $this->parseBasic($message);
        $message = $this->parseSmileys($message);
        $message = $this->parseList($message);
        $message = $this->convertLinks($message);
        $message = $this->stripBadWords($message);

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
        $message = preg_replace_callback("#\[url=[\"']?(.*?)[\"']?\](.*?)\[/url\]#si",array('disBBCodeParser','parseUrlCallback'),$message);
        $message = preg_replace_callback("#\[email\]([^/]*?)\[/email\]#si",array('disBBCodeParser','parseEmailCallback'),$message);
        $message = preg_replace("#\[url\]([^/]*?)\[/url\]#si",'<a href="http://\\1">\\1</a>',$message);
        $message = preg_replace("#\[url\](.*?)\[/url\]#si",'\\1',$message);
        $message = preg_replace("#\[magic\](.*?)\[/magic\]#si",'<marquee>\\1</marquee>',$message);
        $message = preg_replace("#\[php\](.*?)\[/php\]#si",'<pre class="brush:php">\\1</pre>',$message);
        $message = preg_replace("#\[mysql\](.*?)\[/mysql\]#si",'<pre class="brush:sql">\\1</pre>',$message);
        $message = preg_replace("#\[css\](.*?)\[/css\]#si",'<pre class="brush:css">\\1</pre>',$message);
        $message = preg_replace("#\[pre\](.*?)\[/pre\]#si",'<pre>\\1</pre>',$message);
        $message = preg_replace("#\[img=[\"']?(.*?)[\"']?\](.*?)\[/img\]#si",'<img src="\\1" alt="\\2" />',$message);
        $message = preg_replace("#\[img\](.*?)\[/img\]#si",'<img src="\\1" border="0" />',$message);
        $message = str_ireplace(array('[indent]', '[/indent]'), array('<div class="Indent">', '</div>'), $message);

        $message = preg_replace("#\[font=[\"']?(.*?)[\"']?\]#i",'<span style="font-family:\\1;">',$message);
        $message = preg_replace("#\[color=[\"']?(.*?)[\"']?\]#i",'<span style="color:\\1;">',$message);
        $message = preg_replace_callback("#\[size=[\"']?(.*?)[\"']?\]#si",array('disBBCodeParser','parseSizeCallback'),$message);
        $message = str_ireplace(array("[/size]", "[/font]", "[/color]"), "</span>", $message);

        $message = preg_replace('#\[/?left\]#si', '', $message);

        return $message;
    }

    public function stripHtml($message) {
    	$message = $this->br2nl($message);
        $message = preg_replace(array(
            "@<iframe[^>]*?>.*?</iframe>@siu",
            "@<frameset[^>]*?>.*?</frameset>@siu",
            "@<form[^>]*?>.*?</form>@siu",
            "@<input[^>]*?>.*?</input>@siu",
            "@<select[^>]*?>.*?</select>@siu",
            "@<frame[^>]*?>.*?</frame>@siu",
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            '@<div[^>]*?.*?</div>@siu',
            '@<span[^>]*?.*?</span>@siu',
            '@<body[^>]*?.*?</body>@siu',
            '@<html[^>]*?.*?</html>@siu',
        ),'',$message);

        /* strip HTML comments */
        $message = preg_replace("#\<!--(.*?)--\>#si",'',$message);
        $message = str_replace('<!--','',$message);
        $message = str_replace('-->','',$message);

        /* convert all remaining HTML to entities */
        $message = htmlentities($message,null,'UTF-8');

        return $message;
    }

    /**
     * Prevent javascript:/ftp: injections via URLs
     * 
     * @static
     * @param array $matches
     * @return string
     */
    public static function parseUrlCallback($matches) {
        $url = str_replace(array('javascript:','ftp:'),'',strip_tags($matches[1]));
        return '<a href="'.$url.'">'.$matches[2].'</a>';
    }

    public static function parseSizeCallback($matches) {
        $size = intval(str_replace(array('pt','px','em'),'',$matches[1]));
        if ($size > 24) $size = 24;
        if ($size < 6) $size = 6;
        return '<span style="font-size:'.$size.'px;">';
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
        return '<a href="'.$url.'" target="_blank"'.$noFollow.'>'.$url.'</a>'."\n";
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
        $new_string = str_replace('[/quote]', '</blockquote>', $message);
        $message = preg_replace_callback('/\[quote(.*?)\]/msi',array('disBBCodeParser','parseQuoteCallback'), $new_string);
        return $message;
    }
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

        return $citation.'<blockquote class="dis-quote">';
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
     * Do some SMF-style BBCode cleaning
     * 
     * @param string $message
     * @return string
     */
    public function preClean($message) {
        /* leave only \n linebreaks */
	    $message = strtr($message, array("\r" => ''));

        /* nuke any extra [/quote] tags */
        while (substr($message, -7) == '[quote]') {
            $message = substr($message, 0, -7);
        }
        while (substr($message, 0, 8) == '[/quote]') {
            $message = substr($message, 8);
        }

        $codeopen = preg_match_all('~(\[code(?:=[^\]]+)?\])~is', $message, $dummy);
        $codeclose = preg_match_all('~(\[/code\])~is', $message, $dummy);

        // Close/open all code tags...
        if ($codeopen > $codeclose)
            $message .= str_repeat('[/code]', $codeopen - $codeclose);
        elseif ($codeclose > $codeopen)
            $message = str_repeat('[code]', $codeclose - $codeopen) . $message;

        // Now that we've fixed all the code tags, let's fix the img and url tags...
        $parts = preg_split('~(\[/code\]|\[code(?:=[^\]]+)?\])~i', $message, -1, PREG_SPLIT_DELIM_CAPTURE);

        $nbs = '\xA0';
        $charset = $this->modx->getOption('modx_charset',null,'UTF-8');
        $keepAttributes = $this->modx->getOption('discuss.allowed_html_attributes',null,'href,target,src,author,date');
        $keepAttributes = explode(',',$keepAttributes);
        
        /* Only mess with stuff outside [code] tags. */
        $z = 0;
        for ($i = 0, $n = count($parts); $i < $n; $i++) {
            /* $z #s -> 0 = outside, 1 = begin tag, 2 = inside, 3 = close tag, repeat. */
            if ($z == 2) {
                /* convert all code in [code] to htmlent */
                $parts[$i] = htmlentities($parts[$i]);
            }
            if ($z == 0 || $z == 4) {
                $z = 0;
                $parts[$i] = preg_replace('~(\[img.*?\])(.+?)\[/img\]~eis', '\'$1\' . preg_replace(\'~action(=|%3d)(?!dlattach)~i\', \'action-\', \'$2\') . \'[/img]\'', $parts[$i]);

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

                $mistakeFixes = array(
                    /* Look for properly opened [li]s which aren't closed. */
                    '~\[li\]([^\[\]]+?)\[li\]~s' => '[li]$1[_/li_][_li_]',
                    '~\[li\]([^\[\]]+?)$~s' => '[li]$1[/li]',
                    /* Lists - find correctly closed items/lists. */
                    '~\[/li\]([\s' . $nbs . ']*)\[/list\]~s' . ($charset == 'UTF-8' ? 'u' : '') => '[_/li_]$1[/list]',
                    /* Find list items closed and then opened.*/
                    '~\[/li\]([\s' . $nbs . ']*)\[li\]~s' . ($charset == 'UTF-8' ? 'u' : '') => '[_/li_]$1[_li_]',
                    /* Now, find any [list]s or [/li]s followed by [li].*/
                    '~\[(list(?: [^\]]*?)?|/li)\]([\s' . $nbs . ']*)\[li\]~s' . ($charset == 'UTF-8' ? 'u' : '') => '[$1]$2[_li_]',
                    /* Any remaining [li]s weren't inside a [list].*/
                    '~\[li\]~' => '[list][li]',
                    /* Any remaining [/li]s weren't before a [/list].*/
                    '~\[/li\]~' => '[/li][/list]',
                    /* Put the correct ones back how we found them. */
                    '~\[_(li|/li)_\]~' => '[$1]',
                );
                for ($j = 0; $j < 3; $j++) {
                    $parts[$i] = preg_replace(array_keys($mistakeFixes), $mistakeFixes, $parts[$i]);
                }


                $parts[$i] = preg_replace('~&lt;a\s+href=((?:&quot;)?)((?:https?://|ftps?://|mailto:)\S+?)\\1&gt;~i', '[url=$2]', $parts[$i]);
                $parts[$i] = preg_replace('~&lt;/a&gt;~i', '[/url]', $parts[$i]);

                // strip all unwanted html attributes
                preg_match_all('/[a-z]+=".+"/iU', $parts[$i], $attributes);
                foreach ($attributes[0] as $attribute) {
                    $attributeName = stristr($attribute, '=', true);
                    if (!in_array($attributeName, $keepAttributes)) {
                        $parts[$i] = str_replace(' ' . $attribute, '', $parts[$i]);
                    }
                }

                // now attributes without quotes
                preg_match_all('/[a-z]+=.+/iU', $parts[$i], $attributes);
                foreach ($attributes[0] as $attribute) {
                    $attributeName = stristr($attribute, '=', true);
                    if (!in_array($attributeName, $keepAttributes)) {
                        $parts[$i] = str_replace(' ' . $attribute, '', $parts[$i]);
                    }
                }

                // strip script tags properly
                $parts[$i] = preg_replace("@<script[^>]*>.+</script[^>]*>@i",'',$parts[$i]);
                $parts[$i] = $this->stripHtml($parts[$i]);

                $parts[$i] = $this->cleanupImg($parts[$i]);
            }

            $z++;


            $message = implode('', $parts);
        }
        return $message;
    }

    public function cleanupImg($message) {
        preg_match_all('~&lt;img\s+src=((?:&quot;)?)((?:https?://|ftps?://)\S+?)\\1(?:\s+alt=(&quot;.*?&quot;|\S*?))?(?:\s?/)?&gt;~i', $message, $matches, PREG_PATTERN_ORDER);
        if (!empty($matches[0])) {
            $replaces = array();
            foreach ($matches[2] as $match => $imgTag) {
                $alt = empty($matches[3][$match]) ? '' : ' alt=' . preg_replace('~^&quot;|&quot;$~', '', $matches[3][$match]);

                // Remove action= from the URL - no funny business, now.
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
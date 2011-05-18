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
/**
 * Abstract parser class that must be extended for creating custom parsing engines for Discuss Posts. Never
 * instantiate directly.
 *
 * @abstract
 */
abstract class disParser {
    /** @var modX\xPDO A reference to the modX object. */
    public $modx;
    /** @var Discuss $discuss A reference to the Discuss object. */
    public $discuss;
    
    function __construct(xPDO &$modx) {
        $this->modx =& $modx;
        $this->discuss =& $modx->discuss;
    }

    /**
     * Abstract parse method that is run for each post before rendering. Must be defined
     * in any derivative classes of disParser and determines the parsing of the post.
     *
     * @abstract
     * @param string $message
     * @return string The parsed content
     */
    abstract public function parse($message);

    /**
     * A better working nl2br
     *
     * @param string $str
     * @return string
     */
    protected function _nl2br2($str) {
        $str = str_replace("\r", '', $str);
        return preg_replace('/(?<!>)\n/', "<br />\n", $str);
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

    /**
     * Strip all bad words out of the parser
     *
     * @param string $message
     * @return mixed
     */
    public function stripBadWords($message) {
        $replace = $this->modx->getOption('discuss.bad_words_replace',null,true);
        $char = '';
        if (!empty($replace)) {
            $char = $this->modx->getOption('discuss.bad_words_replace_string',null,'****');
        }
        $badWords = $this->modx->getOption('discuss.bad_words',null,'');
        $badWords = explode(',',$badWords);
        if (!empty($badWords)) {
            $message = str_replace($badWords,'',$message);
            $message = preg_replace('/\b('.implode('|',$badWords).')\b/i',$char,$message);
        }
        return $message;
    }
}
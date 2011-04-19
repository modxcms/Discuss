<?php
/**
 * @package discuss
 */
class disUser extends xPDOSimpleObject {
    const INACTIVE = 0;
    const ACTIVE = 1;
    const UNCONFIRMED = 2;
    const BANNED = 3;
    const AWAITING_MODERATION = 4;

    /**
     * Gets the avatar URL for this user, depending on the avatar service.
     * @return string
     */
    public function getAvatarUrl() {
        $avatarUrl = '';

        $avatarService = $this->get('avatar_service');
        $avatar = $this->get('avatar');
        if (!empty($avatar) || !empty($avatarService)) {
            if (!empty($avatarService)) {
                if ($avatarService == 'gravatar') {
                    $avatarUrl = $this->xpdo->getOption('discuss.gravatar_url',null,'http://www.gravatar.com/avatar/').md5($this->get('email'));
                }
            } else {
                $avatarUrl = $this->xpdo->getOption('discuss.files_url').'/profile/'.$this->get('user').'/'.$this->get('avatar');
            }
        }
        return $avatarUrl;
    }

    public function parseSignature() {
        $message = $this->get('signature');
        if (!empty($message)) {
            $tags = array(
                '<br />','<br/>','<br>',
                  '[b]','[/b]',
                '[i]','[/i]',
                '[img]','[/img]',
                '[code]','[/code]',
                //'[quote','[/quote]',
                '[s]','[/s]',
                '[url="','[/url]',
                '[email="','[/email]',
                '[hr]',
                '[list]','[/list]','[li]','[/li]',
                '"]',
            );
            if ($this->xpdo->getOption('discuss.bbcode_enabled',null,true)) {
                $message = str_replace($tags,array(
                    "\n","\n","\n",
                    '<strong>','</strong>',
                    '<em>','</em>',
                    '<img src="','">',
                    '<div class="dis-code"><h5>Code</h5><pre>','</pre></div>',
                    //'<div class="dis-quote"><h5>Quote</h5><div>','</div></div>',
                    '<span class="dis-strikethrough">','</span>',
                    '<a href="','</a>',
                    '<a href="mailto:','</a>',
                    '<hr />',
                    '<ul>','</ul>','<li>','</li>',
                    '">',
                ),$message);
            } else {
                $message = str_replace($tags,'',$message);
            }
             $pattern = '|[[\/\!]*?[^\[\]]*?]|si';
             $replace = '';
             $message = preg_replace($pattern, $replace, $message);

        }
        return $message;
    }

    /* get a count of # of messages */
    public function countUnreadMessages() {
        $c = $this->xpdo->newQuery('disThread');
        $c->innerJoin('disThreadUser','Users');
        $c->leftJoin('disThreadRead','Reads','Reads.user = '.$this->get('id').' AND disThread.id = Reads.thread');
        $c->where(array(
            'disThread.private' => true,
            'Users.user' => $this->get('id'),
            'Reads.thread:IS' => null,
        ));
        return $this->xpdo->getCount('disThread',$c);
    }

    public function clearCache() {
        if (!defined('DISCUSS_IMPORT_MODE')) {
            $this->xpdo->getCacheManager();
            $this->xpdo->cacheManager->delete('discuss/user/'.$this->get('id'));
        }
    }

}
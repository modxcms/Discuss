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
 * @subpackage import
 */
/**
 * Handles importing data from SMF
 *
 * @package discuss
 * @subpackage import
 */
class DisSmfImport {
    /**
     * The default mysql datetime format.
     * @const DATETIME_FORMATTED
     */
    const DATETIME_FORMATTED = '%Y-%m-%d %H:%M:%S';
    /**
     * A reference to the modX instance.
     * @var modX $modx
     */
    public $modx;
    /**
     * A reference to the Discuss instance.
     * @var Discuss $discuss
     */
    public $discuss;
    /**
     * A reference to the PDO instance that connects to the old SMF database.
     * @var PDO $pdo
     */
    public $pdo;
    /**
     * The prefix of the tables in the SMF database.
     * @var string $tablePrefix
     */
    public $tablePrefix = 'smf_';
    /**
     * A collection of cached members for cross-reference
     * @var array $memberCache
     */
    protected $memberCache = array();
    /**
     * A collection of cached member usernames for cross-reference
     * @var array $memberNameCache
     */
    protected $memberNameCache = array();
    /**
     * A collection of cached member groups for cross-reference
     * @var array $memberGroupCache
     */
    protected $memberGroupCache = array();
    /**
     * A collection of cached posts for cross-reference
     * @var array $postCache
     */
    protected $postCache = array();

    /**
     * Whether or not this import will actually save and commit the data.
     * @var bool
     */
    public $live = true;

    public $config = array();

    /**
     * Left TODO:
     * - User Group management
     * - auto-assign all users to one group
     *
     * @param Discuss $discuss A reference to the Discuss instance
     */
    function __construct(Discuss &$discuss) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        if (!defined('DISCUSS_IMPORT_MODE')) {
            define('DISCUSS_IMPORT_MODE',true);
        }
        $this->_loadConfig();
        if (empty($this->config['attachments_path'])) {
            $this->config['attachments_path'] = $this->modx->getOption('assets_path').'attachments/';
        }
    }

    public function _loadConfig() {
        $systems = array();
        require $this->discuss->config['corePath'].'includes/systems.inc.php';
        $this->config = array_merge($this->config,array(
            'live' => false,
            
            'import_users' => false,
            'import_categories' => false,
            'import_private_messages' => false,
            'import_ignore_boards' => false,
            
            'default_user_group' => 'Forum Full Member',
            'usergroup_prefix' => 'Forum ',
            'attachments_path' => false,
        ),$systems);

    }

    /**
     * Log a message to the browser/console
     * @param string $msg
     * @return void
     */
    public function log($msg) {
        $this->modx->log(modX::LOG_LEVEL_INFO,$msg); flush();
    }

    /**
     * Get the connection to the SMF database and return a PDO instance
     * @return PDO
     */
    public function getConnection() {
        $systems = array();
        require $this->discuss->config['corePath'].'includes/systems.inc.php';
        if (empty($systems)) {
            $this->log('No config file.');
        } else {
            try {
                $this->pdo = new PDO($systems['smf']['dsn'], $systems['smf']['username'], $systems['smf']['password']);
                $this->tablePrefix = $systems['smf']['tablePrefix'];
            } catch (PDOException $e) {
                $this->log('Connection failed: ' . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    /**
     * Run the import.
     * 
     * @return void
     */
    public function run() {
        if ($this->getConnection()) {
            if ($this->config['import_users']) {
                $this->importUserGroups();
                $this->importUsers();
            } else {
                $this->collectUserCaches();
            }
            if ($this->config['import_categories']) {
                $this->importCategories();
            }
            if ($this->config['import_private_messages']) {
                $this->importPrivateMessages();
            }
            if ($this->config['import_ignore_boards']) {
                $this->migrateIgnoreBoards();
            }
        } else {
            $this->log('Could not start import because could not get connection to SMF database.');
        }
    }

    /**
     * Get the cache of Users and User Groups from the Discuss database
     * @return void
     */
    protected function collectUserCaches() {
        $this->log('Collecting User cache...');
        $userTable = $this->modx->getTableName('disUser');
        $stmt = $this->modx->query('SELECT id,username,integrated_id FROM '.$userTable.' ORDER BY username ASC');
        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->memberCache[$row['integrated_id']] = $row['id'];
                $this->memberNameCache[$row['integrated_id']] = $row['username'];
            }
            $stmt->closeCursor();
        }

        $this->log('Collecting User Group cache...');
        $userGroupTable = $this->modx->getTableName('disUserGroupProfile');
        $stmt = $this->modx->query('SELECT id,name,integrated_id FROM '.$userGroupTable.' ORDER BY name ASC');
        if ($stmt) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->memberGroupCache[$row['integrated_id']] = $row['id'];
            }
            $stmt->closeCursor();
        }
    }

    /**
     * Escape and prefix any table
     * 
     * @param string $table
     * @return string
     */
    public function getFullTableName($table) {
        return '`'.$this->tablePrefix.$table.'`';
    }

    /**
     * Import User Groups into the Discuss database
     * @return string
     */
    public function importUserGroups() {
        $stmt = $this->pdo->query('
            SELECT * FROM '.$this->getFullTableName('membergroups').'
            ORDER BY `groupName` ASC
        '.(!$this->config['live'] ? 'LIMIT 10' : ''));
        if (!$stmt) { return 'Failed grabbing members.'; }
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!$row) continue;

            /** @var modUserGroup $usergroup */
            $usergroup = $this->modx->newObject('modUserGroup');
            $usergroup->fromArray(array(
                'name' => $this->config['usergroup_prefix'].$row['groupName'],
            ));
            if ($this->config['live']) {
                $usergroup->save();
            }

            /** @var disUserGroupProfile $dug */
            $dug = $this->modx->newObject('disUserGroupProfile');
            $dug->fromArray(array(
                'usergroup' => $usergroup->get('id'),
                'post_based' => !empty($row['minPosts']) ? true : false,
                'min_posts' => $row['minPosts'],
                'color' => $row['onlineColor'],
                'integrated_id' => $row['ID_GROUP'],
            ));
            if ($this->config['live']) {
                $dug->save();
            }

            $this->log('Creating User Group: '.$row['groupName']);

            $this->memberGroupCache[$row['ID_GROUP']] = $usergroup->get('id');
        }
        $stmt->closeCursor();
    }

    /**
     * Import Users into the Discuss database
     * @return string
     */
    public function importUsers() {
        $stmt = $this->pdo->query('
            SELECT * FROM '.$this->getFullTableName('members').'
            ORDER BY `memberName` ASC
        '.(!$this->config['live'] ? 'LIMIT 10' : ''));
        if (!$stmt) { return 'Failed grabbing members.'; }
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!$row) continue;

            /* first create modUser objects if they dont already exist */
            $c = $this->modx->newQuery('modUser');
            $c->innerJoin('modUserProfile','Profile');
            $c->select($this->modx->getSelectColumns('modUser','modUser'));
            $c->select($this->modx->getSelectColumns('modUserProfile','Profile','',array('email')));
            $c->where(array(
                'modUser.username' => $row['memberName'],
            ));
            $modxUser = $this->modx->getObject('modUser',$c);
            if ($modxUser/* && $modxUser->get('email') != $row['emailAddress']*/) {
                /*
                 * Not the same user, so do not create a modUser object. We will create a disUser object
                 * that will, on authentication, check to see if the passwords match.
                 * If not, it will ask the user to choose a different username on a separate login page (a Discuss-specific one).
                 * If they match, it will auto-sync the disUser account to the modUser account.
                 *
                 * (On 2nd thought, this is such a small case, skipping for now)
                 */
                //$modxUser = false;
            } elseif (!$modxUser) {
                /**
                 * No modUser exists with that username and email, so we'll create one.
                 * @var modUser $modxUser
                 */
                $modxUser = $this->modx->newObject('modUser');
                $modxUser->fromArray(array(
                    'username' => $row['memberName'],
                    'password' => $row['passwd'], /* will do auth in plugin */
                    'salt' => $row['passwordSalt'],
                    'class_key' => 'modUser',
                    'active' => (boolean)$row['is_activated'],
                ));
                if ($this->config['live']) {
                    $modxUser->save();
                }
                /** @var modUserProfile $modxUserProfile */
                $modxUserProfile = $this->modx->newObject('modUserProfile');
                $modxUserProfile->fromArray(array(
                    'email' => $row['emailAddress'],
                    'gender' => $row['gender'],
                    'fullname' => $row['realName'],
                    'dob' => strtotime($row['birthdate']),
                    'website' => $row['websiteUrl'],
                    'address' => $row['location'],
                    'lastlogin' => $row['lastLogin'],
                ));
                if ($this->config['live']) {
                    $modxUserProfile->set('internalKey',$modxUser->get('id'));
                    $modxUserProfile->save();
                }
            } /* else we already have a modUser that matches the email and username. Auto-sync!

            /* now create disUser object */
            $name = explode(' ' ,$row['realName']);
            /** @var disUser $user */
            $user = $this->modx->newObject('disUser');
            $user->fromArray(array(
                'username' => $row['memberName'],
                'password' => $row['passwd'],
                'email' => $row['emailAddress'],
                'ip' => $row['memberIP'],
                'synced' => false, /* will sync on first login */
                'source' => 'smf',
                'createdon' => strftime(DisSmfImport::DATETIME_FORMATTED,$row['dateRegistered']),
                'name_first' => $name[0],
                'name_last' => isset($name[1]) ? $name[1] : '',
                'gender' => $row['gender'] == 1 ? 'm' : 'f',
                'birthdate' => $row['birthdate'],
                'website' => $row['websiteUrl'],
                'location' => $row['location'],
                'status' => $row['is_activated'] ? 1 : 0,
                'confirmed' => true,
                'confirmedon' => $this->now(),
                'signature' => $row['signature'],
                'title' => $row['usertitle'],
                'posts' => $row['posts'],
                'show_email' => $row['hideEmail'] ? 0 : 1,
                'show_online' => $row['showOnline'] ? 1 : 0,
                'salt' => $row['passwordSalt'],
                'ignore_boards' => $row['ignoreBoards'],
                'integrated_id' => $row['ID_MEMBER'],
            ));
            $this->log('Creating User '.$row['memberName']);
            $this->memberCache[$row['ID_MEMBER']] = $user->get('id');
            $this->memberNameCache[$row['memberName']] = $user->get('id');
            if ($this->config['live']) {
                if ($modxUser) {
                    $user->set('user',$modxUser->get('id'));
                }
                $user->save();
            }
            if ($modxUser) {
                $this->importUserGroupMemberships($user,$row);
            }

        } /* end while */
        $stmt->closeCursor();
    }

    /**
     * Import the UserGroup memberships for the specified User
     * 
     * @param disUser $user
     * @param array $row
     * @return void
     */
    public function importUserGroupMemberships(disUser $user,array $row) {
        $groups = array();
        /* if a default group */
        if (!empty($row['ID_GROUP'])) $groups[] = $row['ID_GROUP'];
        /* if any additional SMF groups */
        if (!empty($row['additionalGroups'])) {
            $groups = array_merge(explode(',',$row['additionalGroups']));
        }

        /* default user group import option */
        if (!empty($this->config['default_user_group'])) {
            /** @var modUserGroup $dug */
            $dug = $this->modx->getObject('modUserGroup',array('name' => $this->config['default_user_group']));
            if ($dug) {
                $groups[] = $dug->get('id');
            }
        }

        $groups = array_unique($groups);
        foreach ($groups as $group) {
            if (!empty($this->memberGroupCache[$group])) {
                /** @var modUserGroupMember $member */
                $member = $this->modx->newObject('modUserGroupMember');
                $member->set('user_group',$this->memberGroupCache[$group]);
                $member->set('member',$user->get('user'));
                if ($this->config['live']) {
                    $member->save();
                }
            }
        }
    }

    /**
     * Import Categories into Discuss
     * @return void
     */
    public function importCategories() {
        $stmt = $this->pdo->query('
            SELECT * FROM '.$this->getFullTableName('categories').'
            ORDER BY `catOrder` ASC
        ');
        if ($stmt) {
            $idx = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!$row) continue;
                /** @var disCategory $category */
                $category = $this->modx->getObject('disCategory',array(
                    'name' => $row['name'],
                ));
                if (!$category) {
                    $category = $this->modx->newObject('disCategory');
                    $category->fromArray(array(
                        'name' => $row['name'],
                        'collapsible' => $row['canCollapse'] ? true : false,
                        'rank' => $idx,
                        'integrated_id' => $row['ID_CAT'],
                    ));
                    $this->log('Importing category '.$row['name']);
                    if ($this->config['live']) {
                        $category->save();
                    }
                }

                $this->importBoards($category,$row);

                $idx++;
            }
            $stmt->closeCursor();
        }
    }

    /**
     * Import all Boards for this category and/or Board parent
     * 
     * @param disCategory $category
     * @param array $row
     * @param null $parentBoard
     * @param int $smfParent
     * @return array
     */
    public function importBoards(disCategory $category,array $row,$parentBoard = null,$smfParent = 0) {
        $bst = $this->pdo->query('
            SELECT * FROM '.$this->getFullTableName('boards').'
            WHERE
                `ID_CAT` = '.$row['ID_CAT'].'
            AND `ID_PARENT` = '.$smfParent.'
            ORDER BY boardOrder ASC
        '.(!$this->config['live'] ? 'LIMIT 3' : ''));
        if (!$bst) return array();
        $bIdx = 0;
        while ($brow = $bst->fetch(PDO::FETCH_ASSOC)) {
            if (!$brow) continue;

            $c = $this->modx->newQuery('disBoard');
            $c->innerJoin('disCategory','Category');
            $c->where(array(
                'disBoard.name' => $brow['name'],
                'Category.name' => $category->get('name'),
            ));
            /** @var disBoard $board */
            $board = $this->modx->getObject('disBoard',$c);

            if (!$board) {
                $board = $this->modx->newObject('disBoard');
                $board->fromArray(array(
                    'parent' => 0,
                    'name' => $brow['name'],
                    'description' => $brow['description'],
                    'num_topics' => $brow['numTopics'],
                    'num_replies' => $brow['numPosts']-$brow['numTopics'],
                    'total_posts' => $brow['numPosts'],
                    'ignoreable' => $brow['allowIgnore'],
                    'rank' => $bIdx,
                    'integrated_id' => $brow['ID_BOARD'],
                ));
                if ($parentBoard) {
                    $board->set('parent',$parentBoard->get('id'));
                }
                $this->log('Importing board '.$brow['name']);
                if ($this->config['live']) {
                    $board->set('category',$category->get('id'));
                    $board->save();
                }
            }
            $this->importTopics($board,$brow);
            $this->importBoards($category,$row,$board,$brow['ID_BOARD']);

            $c = $this->modx->newQuery('disPost');
            $c->where(array(
                'board' => $board->get('id'),
            ));
            $c->sortby('createdon','DESC');
            $c->limit(1);
            $lastPost = $this->modx->getObject('disPost',$c);
            if ($lastPost) {
                $board->set('last_post',$lastPost->get('id'));
                $board->save();
            }
            
            $bIdx++;
        }
        $bst->closeCursor();
    }

    /**
     * Import all topics for this Board
     * 
     * @param disBoard $board
     * @param array $brow
     * @return array
     */
    public function importTopics(disBoard $board,array $brow) {
        $sql = '
            SELECT
                FirstPost.*,
                Topic.locked AS locked,
                Topic.numReplies AS numReplies,
                Topic.numViews AS numViews,
                Topic.isSticky AS isSticky,
                Topic.ID_TOPIC AS ID_TOPIC,
                LastPost.posterTime AS latest_reply
            FROM '.$this->getFullTableName('topics').' AS `Topic`
            INNER JOIN '.$this->getFullTableName('messages').' AS `FirstPost` ON `FirstPost`.`ID_MSG` = `Topic`.`ID_FIRST_MSG`
            INNER JOIN '.$this->getFullTableName('messages').' AS `LastPost` ON `LastPost`.`ID_MSG` = `Topic`.`ID_LAST_MSG`
            WHERE
                `Topic`.`ID_BOARD` = '.$brow['ID_BOARD'].'
            ORDER BY `FirstPost`.`posterTime` ASC
        '.(!$this->config['live'] ? 'LIMIT 10' : '');
        $tst = $this->pdo->query($sql);
        if (!$tst) return array();
        $tIdx = 0;
        while ($trow = $tst->fetch(PDO::FETCH_ASSOC)) {
            $this->log('Importing Topic in '.$board->get('name').': '.$trow['subject']);

            /** @var disThread $thread */
            $thread = $this->modx->newObject('disThread');
            $thread->fromArray(array(
                'board' => $board->get('id'),
                'views' => $trow['numViews'],
                'locked' => $trow['locked'],
                'sticky' => $trow['isSticky'],
                'private' => false,
                'integrated_id' => $trow['ID_TOPIC'],
            ));
            if ($this->config['live']) {
                $thread->save();
            }

            /** @var disPost $threadPost */
            $threadPost = $this->modx->newObject('disPost');
            $threadPost->fromArray(array(
                'board' => $thread->get('board'),
                'thread' => $thread->get('id'),
                'parent' => 0,
                'title' => $trow['subject'],
                'message' => $trow['body'],
                'author' => isset($this->memberCache[$trow['ID_MEMBER']]) ? $this->memberCache[$trow['ID_MEMBER']] : 0,
                'createdon' => strftime(DisSmfImport::DATETIME_FORMATTED,$trow['posterTime']),
                'editedby' => !empty($trow['modifiedName']) && isset($this->memberNameCache[$trow['modifiedName']]) ? $this->memberNameCache[$trow['modifiedName']] : 0,
                'editedon' => !empty($trow['modifiedTime']) ? strftime(DisSmfImport::DATETIME_FORMATTED,$trow['modifiedTime']) : 0,
                'icon' => $trow['icon'],
                'rank' => $tIdx,
                'ip' => $trow['posterIP'],
                'integrated_id' => $trow['ID_MSG'],
                'depth' => 0,
            ));
            if ($this->config['live']) {
                $threadPost->save();
            }
            $tIdx++;
            $postsData = $this->importPosts($thread,$threadPost,$trow);

            $thread->set('post_first',$threadPost->get('id'));
            $thread->set('author_first',$threadPost->get('author'));
            $thread->set('replies',$postsData['total']);
            if ($postsData['lastPost']) {
                $thread->set('post_last',$postsData['lastPost']->get('id'));
                $thread->set('author_last',$postsData['lastPost']->get('author'));
            } else {
                $thread->set('post_last',$threadPost->get('id'));
                $thread->set('author_last',$threadPost->get('author'));
            }
            if ($this->config['live']) {
                $thread->save();
            }
        }
        $tst->closeCursor();
    }

    /**
     * Return the current time in MySQL datetime format
     * @return string
     */
    public function now() {
        return strftime(DisSmfImport::DATETIME_FORMATTED);
    }

    /**
     * Import all posts for the specified thread
     * 
     * @param disThread $thread
     * @param disPost $threadPost
     * @param array $trow
     * @return array
     */
    public function importPosts(disThread $thread,disPost $threadPost,array $trow = array()) {
        $sql = '
            SELECT
                *
            FROM '.$this->getFullTableName('messages').'
            WHERE
                `ID_TOPIC` = '.$trow['ID_TOPIC'].'
            AND `ID_MSG` != '.$trow['ID_MSG'].'
            AND `ID_BOARD` = '.$trow['ID_BOARD'].'
            ORDER BY posterTime ASC
        '.(!$this->config['live'] ? 'LIMIT 10' : '');
        $pst = $this->pdo->query($sql);
        if (!$pst) return array('total' => 0);
        $pIdx = 0;
        while ($prow = $pst->fetch(PDO::FETCH_ASSOC)) {
            $this->log('Importing response: '.$prow['subject']);
            /** @var disPost $post */
            $post = $this->modx->newObject('disPost');
            $post->set('thread',$thread->get('id'));
            $post->fromArray(array(
                'board' => $thread->get('board'),
                'thread' => $thread->get('id'),
                'parent' => $threadPost->get('id'),
                'title' => $prow['subject'],
                'message' => $prow['body'],
                'author' => isset($this->memberCache[$prow['ID_MEMBER']]) ? $this->memberCache[$prow['ID_MEMBER']] : 0,
                'createdon' => strftime(DisSmfImport::DATETIME_FORMATTED,$prow['posterTime']),
                'editedby' => !empty($prow['modifiedName']) && isset($this->memberNameCache[$prow['modifiedName']]) ? $this->memberNameCache[$prow['modifiedName']] : 0,
                'editedon' => !empty($prow['modifiedTime']) ? strftime(DisSmfImport::DATETIME_FORMATTED,$prow['modifiedTime']) : 0,
                'icon' => $prow['icon'],
                'allow_replies' => $thread->get('locked'),
                'rank' => $pIdx,
                'ip' => $prow['posterIP'],
                'integrated_id' => $prow['ID_MSG'],
                'depth' => 1,
            ));

            if ($this->config['live']) {
                $post->save();
            }

            $this->importAttachments($post,$prow);
            $pIdx++;
        }
        $pst->closeCursor();
        return array(
            'total' => $pIdx,
            'lastPost' => isset($post) ? $post : null,
        );
    }

    /**
     * Import all attachments for this post
     * @param disPost $post
     * @param array $prow
     * @return array
     */
    public function importAttachments(disPost $post,array $prow = array()) {
        $ast = $this->pdo->query('
            SELECT
                *
            FROM '.$this->getFullTableName('attachments').'
            WHERE
                `ID_MSG` = '.$prow['ID_MSG'].'
        ');
        if (!$ast) return array();
        $aIdx = 0;
        while ($arow = $ast->fetch(PDO::FETCH_ASSOC)) {
            $this->log('Adding attachment: '.$arow['filename']);
            /** @var disPostAttachment $attachment */
            $attachment = $this->modx->newObject('disPostAttachment');
            $attachment->fromArray(array(
                'post' => $post->get('id'),
                'board' => $post->get('board'),
                'filename' => $arow['filename'],
                'filesize' => $arow['size'],
                'downloads' => $arow['downloads'],
                'integrated_id' => $arow['ID_ATTACH'],
                'integrated_data' => $this->modx->toJSON(array(
                    'filename' => $arow['filename'],
                    'file_hash' => $arow['file_hash'],
                    'width' => $arow['width'],
                    'height' => $arow['height'],
                    'attachmentType' => $arow['attachmentType'],
                    'ID_MEMBER' => $arow['ID_MEMBER'],
                    'ID_MESSAGE' => $arow['ID_MESSAGE'],
                    'ID_THUMB' => $arow['ID_THUMB'],
                )),
            ));
            if ($this->config['live']) {
                $attachment->save();
            }
            $aIdx++;
        }
        $ast->closeCursor();
    }

    /**
     * Import all Private Messages into private Threads
     * 
     * @return array
     */
    public function importPrivateMessages() {
        $sql = '
            SELECT
                `Message`.*,
                `MessageThread`.`group_id` AS `group_id`,
                `MessageThread`.`subject2` AS `subject`,
                `MessageThread`.`from_id` AS `from_id`
                
            FROM '.$this->getFullTableName('personal_messages').' AS `Message`
                INNER JOIN '.$this->getFullTableName('smfpm').' AS `MessageThread`
                ON `MessageThread`.`id` = `Message`.`ID_PM`

            WHERE `MessageThread`.`group_id` IS NOT NULL

            ORDER BY `MessageThread`.`group_id`, `Message`.`msgtime` ASC
        ';
        $tst = $this->pdo->query($sql);
        if (!$tst) return array();
        $tIdx = 0;
        $rIdx = 0;
        $currentGroup = false;
        $thread = false;
        $post = false;
        $participants = array();
        while ($trow = $tst->fetch(PDO::FETCH_ASSOC)) {
            $isFirstPostOfThread = false;
            if ($currentGroup !== $trow['group_id']) {
                /* save old thread */
                if ($thread) {
                    $participants = array_unique($participants);
                    $thread->set('users',implode(',',$participants));
                    foreach ($participants as $participant) {
                        /** @var disThreadUser $pmUser */
                        $pmUser = $this->modx->newObject('disThreadUser');
                        $pmUser->set('thread',$thread->get('id'));
                        $pmUser->set('user',$participant);
                        $pmUser->set('author',$participant == $thread->get('author_first') ? 1 : 0);
                        $this->log('--- Adding Participant '.$participant.' to Thread');
                        if ($this->config['live']) {
                            $pmUser->save();
                        }

                        /* add read status so we can assume all messages are read */
                        /** @var disThreadRead $pmRead */
                        $pmRead = $this->modx->newObject('disThreadRead');
                        $pmRead->set('user',$participant);
                        $pmRead->set('thread',$thread->get('id'));
                        $pmRead->set('board',0);
                        if ($this->config['live']) {
                            $pmRead->save();
                        }

                        /* add email notification automatically to PM threads */
                        /** @var disUserNotification $pmNotify */
                        $pmNotify = $this->modx->newObject('disUserNotification');
                        $pmNotify->set('user',$participant);
                        $pmNotify->set('thread',$thread->get('id'));
                        $pmNotify->set('board',0);
                        if ($this->config['live']) {
                            $pmNotify->save();
                        }
                    }
                    $thread->set('replies',$rIdx-1);
                    if ($post) {
                        $thread->set('post_last',$post->get('id'));
                        $thread->set('author_last',$post->get('author'));
                    }
                    if ($this->config['live']) {
                        $thread->save();
                    }
                }

                /* create new thread */
                $participants = array();
                $rIdx = 0;
                $firstAuthorId = isset($this->memberCache[$trow['ID_MEMBER_FROM']]) ? $this->memberCache[$trow['ID_MEMBER_FROM']] : 0;
                /** @var disThread $thread */
                $thread = $this->modx->newObject('disThread');
                $thread->fromArray(array(
                    'replies' => 0,
                    'views' => 0,
                    'locked' => 0,
                    'sticky' => 0,
                    'private' => true,
                    'author_first' => $firstAuthorId,
                    'integrated_id' => $trow['ID_PM'],
                ));
                if ($this->config['live']) {
                    $thread->save();
                }
                $isFirstPostOfThread = true;
                $currentGroup = $trow['group_id'];
                $this->log('Importing Message Thread: '.$trow['group_id'].' - '.$trow['subject'].' by '.$firstAuthorId);
            }

            /* create post in thread */

            $this->log('- Importing Message: '.$trow['fromName']);
            $postAuthor = isset($this->memberCache[$trow['ID_MEMBER_FROM']]) ? $this->memberCache[$trow['ID_MEMBER_FROM']] : 0;
            /** @var disPost $post */
            $post = $this->modx->newObject('disPost');
            $post->fromArray(array(
                'board' => 0,
                'thread' => $thread->get('id'),
                'parent' => 0,
                'title' => $trow['subject'],
                'message' => $trow['body'],
                'author' => $postAuthor,
                'createdon' => strftime(DisSmfImport::DATETIME_FORMATTED,$trow['msgtime']),
                'allow_replies' => 1,
                'integrated_id' => $trow['ID_PM'],
            ));
            $participants[] = $postAuthor;
            if ($this->config['live']) {
                $post->save();
            }
            if ($isFirstPostOfThread) {
                if ($this->config['live']) {
                    $thread->set('post_first',$post->get('id'));
                    $thread->save();
                }
            }
            $tIdx++;
            $rIdx++;
        }
        /* save final PM thread */
        if ($thread) {
            $thread->set('users',implode(',',$participants));
            $thread->set('replies',$rIdx);
            if ($post) {
                $thread->set('post_last',$post->get('id'));
                $thread->set('author_last',$post->get('author'));
            }
            if ($this->config['live']) {
                $thread->save();
            }
        }
        $tst->closeCursor();
    }

    /**
     * Migrate ignore boards into Users
     * 
     * @return void
     */
    public function migrateIgnoreBoards() {
        $this->log('Migrating Ignore Boards...');
        $this->log('Collecting User cache...');
        $c = $this->modx->newQuery('disUser');
        $c->sortby('username','ASC');
        $c->where(array(
            'ignore_boards:!=' => '',
        ));
        $users = $this->modx->getCollection('disUser',$c);
        /** @var disUser $user */
        foreach ($users as $user) {
            $boards = explode(',',$user->get('ignore_boards'));
            if (!empty($boards)) {
                $this->log('Migrating '.count($boards).' boards for '.$user->get('username'));
                $newBoards = array();
                foreach ($boards as $board) {
                    /** @var disBoard $b */
                    $b = $this->modx->getObject('disBoard',array(
                        'integrated_id' => $board,
                    ));
                    if ($b) {
                        $newBoards[] = $b->get('id');
                    }
                }

                if (!empty($newBoards)) {
                    $user->set('ignore_boards',implode(',',$newBoards));
                    if ($this->config['live']) {
                        $user->save();
                    }
                }
            }
        }

    }
}

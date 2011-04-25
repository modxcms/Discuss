<?php
/**
 * Handles importing data from SMF
 *
 * @package discuss
 * @subpackage import
 */
class DisSmfImport {
    const DATETIME_FORMATTED = '%Y-%m-%d %H:%M:%S';
    public $modx;
    public $discuss;
    public $pdo;
    protected $memberCache = array();
    protected $memberNameCache = array();
    protected $memberGroupCache = array();
    protected $postCache = array();

    public $live = true;

    public $importOptions = array(
        'users' => false,
        'categories' => false,
        'private_messages' => true,
    );

    /**
     * Left TODO:
     * - Private Messages
     */

    function __construct(Discuss &$discuss) {
        $this->discuss =& $discuss;
        $this->modx =& $discuss->modx;
        if (!defined('DISCUSS_IMPORT_MODE')) {
            define('DISCUSS_IMPORT_MODE',true);
        }
    }

    protected function log($msg) {
        $this->modx->log(modX::LOG_LEVEL_INFO,$msg); flush();
    }

    public function getConnection() {
        $systems = array();
        require $this->discuss->config['corePath'].'includes/systems.inc.php';
        if (empty($systems)) {
            $this->log('No config file.');
        } else {
            try {
                $this->pdo = new PDO($systems['smf']['dsn'], $systems['smf']['username'], $systems['smf']['password']);
            } catch (PDOException $e) {
                $this->log('Connection failed: ' . $e->getMessage());
            }
        }
        return $this->pdo;
    }

    public function run() {
        if ($this->getConnection()) {
            if ($this->importOptions['users']) {
                $this->importUserGroups();
                $this->importUsers();
            } else {
                $this->collectUserCaches();
            }
            if ($this->importOptions['categories']) {
                $this->importCategories();
            }
            if ($this->importOptions['private_messages']) {
                $this->importPrivateMessages();
            }
        } else {
            $this->log('Could not start import because could not get connection to SMF database.');
        }
    }

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

    public function importUserGroups() {
        $stmt = $this->pdo->query('
            SELECT * FROM `smf_membergroups`
            ORDER BY `groupName` ASC
        '.(!$this->live ? 'LIMIT 10' : ''));
        if (!$stmt) { return 'Failed grabbing members.'; }
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (!$row) continue;

            $usergroup = $this->modx->newObject('modUserGroup');
            $usergroup->fromArray(array(
                'name' => 'Forum '.$row['groupName'],
            ));
            if ($this->live) {
                $usergroup->save();
            }

            $dug = $this->modx->newObject('disUserGroupProfile');
            $dug->fromArray(array(
                'usergroup' => $usergroup->get('id'),
                'post_based' => !empty($row['minPosts']) ? true : false,
                'min_posts' => $row['minPosts'],
                'color' => $row['onlineColor'],
                'integrated_id' => $row['ID_GROUP'],
            ));
            if ($this->live) {
                $dug->save();
            }

            $this->log('Creating User Group: '.$row['groupName']);

            $this->memberGroupCache[$row['ID_GROUP']] = $usergroup->get('id');
        }
        $stmt->closeCursor();
    }

    public function importUsers() {
        $stmt = $this->pdo->query('
            SELECT * FROM `smf_members`
            ORDER BY `memberName` ASC
        '.(!$this->live ? 'LIMIT 10' : ''));
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
                 */
                $modxUser = $this->modx->newObject('modUser');
                $modxUser->fromArray(array(
                    'username' => $row['memberName'],
                    'password' => $row['passwd'], /* will do auth in plugin */
                    'salt' => $row['passwordSalt'],
                    'class_key' => 'modUser',
                    'active' => (boolean)$row['is_activated'],
                ));
                if ($this->live) {
                    $modxUser->save();
                }
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
                if ($this->live) {
                    $modxUserProfile->set('internalKey',$modxUser->get('id'));
                    $modxUserProfile->save();
                }
            } /* else we already have a modUser that matches the email and username. Auto-sync!

            /* now create disUser object */
            $name = explode(' ' ,$row['realName']);
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
            if ($this->live) {
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

    public function importUserGroupMemberships(disUser $user,array $row) {
        $groups = array();
        if (!empty($row['ID_GROUP'])) $groups[] = $row['ID_GROUP'];
        if (!empty($row['additionalGroups'])) {
            $groups = array_merge(explode(',',$row['additionalGroups']));
        }
        $groups = array_unique($groups);

        foreach ($groups as $group) {
            if (!empty($this->memberGroupCache[$group])) {
                $member = $this->modx->newObject('modUserGroupMember');
                $member->set('user_group',$this->memberGroupCache[$group]);
                $member->set('member',$user->get('user'));
                if ($this->live) {
                    $member->save();
                }
            }
        }
    }

    public function importCategories() {
        $stmt = $this->pdo->query('
            SELECT * FROM `smf_categories`
            ORDER BY `catOrder` ASC
        ');
        if ($stmt) {
            $idx = 0;
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (!$row) continue;
                $category = $this->modx->newObject('disCategory');
                $category->fromArray(array(
                    'name' => $row['name'],
                    'collapsible' => $row['canCollapse'] ? true : false,
                    'rank' => $idx,
                    'integrated_id' => $row['ID_CAT'],
                ));
                $this->log('Importing category '.$row['name']);
                if ($this->live) {
                    $category->save();
                }

                $this->importBoards($category,$row);

                $idx++;
            }
            $stmt->closeCursor();
        }
    }


    public function importBoards(disCategory $category,array $row,$parentBoard = null,$smfParent = 0) {
        $bst = $this->pdo->query('
            SELECT * FROM `smf_boards`
            WHERE
                `ID_CAT` = '.$row['ID_CAT'].'
            AND `ID_PARENT` = '.$smfParent.'
            ORDER BY boardOrder ASC
        '.(!$this->live ? 'LIMIT 3' : ''));
        if (!$bst) return array();
        $bIdx = 0;
        while ($brow = $bst->fetch(PDO::FETCH_ASSOC)) {
            if (!$brow) continue;

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
            if ($this->live) {
                $board->set('category',$category->get('id'));
                $board->save();
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
            FROM `smf_topics` AS `Topic`
            INNER JOIN `smf_messages` AS `FirstPost` ON `FirstPost`.`ID_MSG` = `Topic`.`ID_FIRST_MSG`
            INNER JOIN `smf_messages` AS `LastPost` ON `LastPost`.`ID_MSG` = `Topic`.`ID_LAST_MSG`
            WHERE
                `Topic`.`ID_BOARD` = '.$brow['ID_BOARD'].'
            ORDER BY `FirstPost`.`posterTime` ASC
            LIMIT 25
        '.(!$this->live ? 'LIMIT 10' : '');
        $tst = $this->pdo->query($sql);
        if (!$tst) return array();
        $tIdx = 0;
        while ($trow = $tst->fetch(PDO::FETCH_ASSOC)) {
            $this->log('Importing Topic in '.$board->get('name').': '.$trow['subject']);

            $thread = $this->modx->newObject('disThread');
            $thread->fromArray(array(
                'board' => $board->get('id'),
                'views' => $trow['numViews'],
                'locked' => $trow['locked'],
                'sticky' => $trow['isSticky'],
                'private' => false,
                'integrated_id' => $trow['ID_TOPIC'],
            ));
            if ($this->live) {
                $thread->save();
            }
            
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
            if ($this->live) {
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
            if ($this->live) {
                $thread->save();
            }
        }
        $tst->closeCursor();
    }

    public function now() {
        return strftime(DisSmfImport::DATETIME_FORMATTED);
    }

    public function importPosts(disThread $thread,disPost $threadPost,array $trow = array()) {
        $sql = '
            SELECT
                *
            FROM `smf_messages`
            WHERE
                `ID_TOPIC` = '.$trow['ID_TOPIC'].'
            AND `ID_MSG` != '.$trow['ID_MSG'].'
            AND `ID_BOARD` = '.$trow['ID_BOARD'].'
            ORDER BY posterTime ASC
        '.(!$this->live ? 'LIMIT 10' : '');
        $pst = $this->pdo->query($sql);
        if (!$pst) return array('total' => 0);
        $pIdx = 0;
        while ($prow = $pst->fetch(PDO::FETCH_ASSOC)) {
            $this->log('Importing response: '.$prow['subject']);
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

            if ($this->live) {
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

    public function importAttachments(disPost $post,array $prow = array()) {
        $ast = $this->pdo->query('
            SELECT
                *
            FROM `smf_attachments`
            WHERE
                `ID_MSG` = '.$prow['ID_MSG'].'
        ');
        if (!$ast) return array();
        $aIdx = 0;
        while ($arow = $ast->fetch(PDO::FETCH_ASSOC)) {
            $this->log('Adding attachment: '.$arow['filename']);
            $attachment = $this->modx->newObject('disPostAttachment');
            $attachment->fromArray(array(
                'post' => $post->get('id'),
                'board' => $post->get('board'),
                'filename' => $arow['filename'],
                'filesize' => $arow['size'],
                'downloads' => $arow['downloads'],
            ));
            if ($this->live) {
                $attachment->save();
            }
            $aIdx++;
        }
        $ast->closeCursor();
    }

    public function importPrivateMessages() {
        $sql = '
            SELECT
                `Message`.*,
                `MessageThread`.`group_id` AS `group_id`,
                `MessageThread`.`subject2` AS `subject`,
                `MessageThread`.`from_id` AS `from_id`
                
            FROM `smf_personal_messages` AS `Message`
                INNER JOIN `smf_smfpm` AS `MessageThread`
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
                        $pmUser = $this->modx->newObject('disThreadUser');
                        $pmUser->set('thread',$thread->get('id'));
                        $pmUser->set('user',$participant);
                        $pmUser->set('author',$participant == $thread->get('author_first') ? 1 : 0);
                        $this->log('--- Adding Participant '.$participant.' to Thread');
                        if ($this->live) {
                            $pmUser->save();
                        }
                    }
                    $thread->set('replies',$rIdx);
                    if ($post) {
                        $thread->set('post_last',$post->get('id'));
                        $thread->set('author_last',$post->get('author'));
                    }
                    if ($this->live) {
                        $thread->save();
                    }
                }

                /* create new thread */
                $participants = array();
                $rIdx = 0;
                $firstAuthorId = isset($this->memberCache[$trow['ID_MEMBER_FROM']]) ? $this->memberCache[$trow['ID_MEMBER_FROM']] : 0;
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
                if ($this->live) {
                    $thread->save();
                }
                $isFirstPostOfThread = true;
                $currentGroup = $trow['group_id'];
                $this->log('Importing Message Thread: '.$trow['group_id'].' - '.$trow['subject'].' by '.$firstAuthorId);
            }

            /* create post in thread */

            $this->log('- Importing Message: '.$trow['fromName']);
            $postAuthor = isset($this->memberCache[$trow['ID_MEMBER_FROM']]) ? $this->memberCache[$trow['ID_MEMBER_FROM']] : 0;
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
            if ($this->live) {
                $post->save();
            }
            if ($isFirstPostOfThread) {
                if ($this->live) {
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
            if ($this->live) {
                $thread->save();
            }
        }
        $tst->closeCursor();
    }
}

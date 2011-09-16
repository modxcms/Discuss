<?php
/**
 * @package discuss
 * @subpackage setup
 */
/**
 * @package discuss
 * @subpackage setup
 */
class disSetup {
    /** @var xPDO|modX modX */
    public $modx;
    /** @var array $config */
    public $config = array();

    public function __construct(xPDO $modx,array $options = array()) {
        $this->modx =& $modx;
        $this->config = array_merge(array(
            
        ),$options);
    }

    public function run() {
        if (!empty($this->config['install_demodata'])) {
            $this->installDemoData();
        }

        if (!empty($this->config['install_resource'])) {
            $this->installResource();
        }
    }

    public function installResource() {
        $contextKey = $this->modx->getOption('context',$this->config,'web');
        /** @var modResource $resource */
        $resource = $this->modx->newObject('modResource');
        $resource->fromArray(array(
            'type' => 'document',
            'contentType' => 'text/html',
            'template' => 0,
            'pagetitle' => 'Forums',
            'longtitle' => '',
            'description' => '',
            'alias' => 'forums',
            'published' => true,
            'parent' => 0,
            'isfolder' => true,
            'richtext' => false,
            'searchable' => false,
            'cacheable' => true,
            'createdby' => $this->modx->user->get('id'),
            'published' => false,
            'hidemenu' => false,
            'menutitle' => 'Forums',
            'class_key' => 'modDocument',
            'context' => $contextKey,
            'content_type' => 1,
            'show_in_tree' => true,
            'hide_children_in_tree' => false,
            'content' => '[[!Discuss]]',
        ));
        return $resource->save();
    }


    /**
     * Install demo data
     * @return boolean
     */
    public function installDemoData() {

        $this->modx->log(modX::LOG_LEVEL_INFO,'Installing demo data...');

        /** @var disCategory $category */
        $category = $this->modx->newObject('disCategory');
        $category->fromArray(array(
            'name' => 'Welcome',
            'description' => 'The welcome section.',
            'collapsible' => true,
        ));
        $category->save();

        /** @var disBoard $board */
        $board = $this->modx->newObject('disBoard');
        $board->fromArray(array(
            'name' => 'Discuss 101',
            'category' => $category->get('id'),
            'description' => 'Introduce yourself to the community here.',
            'ignoreable' => true,
            'locked' => false,
        ));
        $board->save();

        if ($this->modx->user && $this->modx->user instanceof modUser) {
            $profile = $this->modx->user->getOne('Profile');
            if ($profile && $profile instanceof modUserProfile) {
                /** @var disUser $disUser */
                $disUser = $this->modx->newObject('disUser');
                $disUser->fromArray($profile->toArray());
                $name = $profile->get('fullname');
                $name = explode(' ',$name);
                $disUser->fromArray(array(
                    'user' => $this->modx->user->get('id'),
                    'username' => $this->modx->user->get('username'),
                    'createdon' => strftime('%Y-%m-%d %H:%M:%S'),
                    'ip' => $_SERVER['REMOTE_ADDR'],
                    'synced' => true,
                    'syncedat' => strftime('%Y-%m-%d %H:%M:%S'),
                    'source' => 'internal',
                    'confirmed' => true,
                    'confirmedon' => strftime('%Y-%m-%d %H:%M:%S'),
                    'status' => disUser::ACTIVE,
                    'name_first' => $name[0],
                    'name_last' => !empty($name[1]) ? $name[1] : '',
                    'salt' => $this->modx->user->get('salt'),
                ));
                $disUser->save();
            }
        }

        return true;
    }
}
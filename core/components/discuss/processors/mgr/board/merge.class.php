<?php

/**
 *
 */
class disBoardMergeProcessor extends modProcessor {
    /**
     * @var disBoard|null $oldBoard
     */
    public $oldBoard = null;
    /**
     * @var disBoard|null $oldBoard
     */
    public $newBoard = null;

    public $startTime = 0.00;

    /**
     * @return array|bool|string
     */
    public function initialize() {
        /* Attempt to set time limit to ensure long processing doesn't fail */
        @set_time_limit(0);

        /* Get the start time for nice stats */
        $this->startTime = microtime(true);

        /* Get the old and new boards from the request */
        $id = $this->getProperty('id');
        if (!empty($id) && is_numeric($id)) {
            $this->oldBoard = $this->modx->getObject('disBoard', (int)$id);
        }
        $mergeInto = $this->getProperty('merge_into');
        if (!empty($mergeInto) && is_numeric($mergeInto)) {
            $this->newBoard = $this->modx->getObject('disBoard', (int)$mergeInto);
        }

        if (!$this->oldBoard || !$this->newBoard) {
            return $this->failure('Missing old or new board ID.');
        }

        $this->modx->log(modX::LOG_LEVEL_INFO,'Merging all threads currently in "' . $this->oldBoard->get('name') . '" to "' . $this->newBoard->get('name').'"');
        return true;
    }

    /**
     * Run the processor and return the result. Override this in your derivative class to provide custom functionality.
     * Used here for pre-2.2-style processors.
     *
     * @return mixed
     */
    public function process() {

        if ($this->moveThreads()) {
            sleep(1);
            $this->updateCounts('oldBoard');
            $this->updateCounts('newBoard');
            sleep(1);
            $this->updateReadStates();
            $this->modx->log(modX::LOG_LEVEL_INFO,'Merge has finished.');
        } else {
            $this->modx->log(modX::LOG_LEVEL_INFO,'Merge finished with errors.');
        }

        /* Dump some stats on screen */
        $timeSpent = round(microtime(true) - $this->startTime, 4);
        $this->modx->log(modX::LOG_LEVEL_INFO,'Memory used: ' . round(memory_get_usage(true) / 1048576,2) . 'mb | Memory peaked at ' . round(memory_get_peak_usage(true) / 1048576,2) . 'mb');
        $this->modx->log(modX::LOG_LEVEL_INFO,'Time spent: ' . $timeSpent . 's');

        /* Shut it down */
        $this->modx->log(modX::LOG_LEVEL_INFO,'COMPLETED');
        sleep (2);
        return $this->success();
    }

    private function moveThreads() {
        $c = $this->modx->newQuery('disThread');
        $c->where(array(
            'board' => $this->oldBoard->get('id'),
        ));

        $this->modx->log(modX::LOG_LEVEL_INFO,'Moving Threads...');
        /* Move Threads */
        $affected = $this->modx->updateCollection('disThread', array(
            'board' => $this->newBoard->get('id')
        ), array(
            'board' => $this->oldBoard->get('id')
        ));

        if ($affected === false) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Error while attempting to move Threads.');
            sleep(1);
            $this->modx->log(modX::LOG_LEVEL_ERROR,'COMPLETED');
            sleep(1);
            return false;
        } else {
            $this->modx->log(modX::LOG_LEVEL_WARN,'Moved ' . $affected .' Threads.');
        }

        $this->modx->log(modX::LOG_LEVEL_INFO,'Moving Posts...');
        /* Move Posts */
        $affected = $this->modx->updateCollection('disPost', array(
            'board' => $this->newBoard->get('id')
        ), array(
            'board' => $this->oldBoard->get('id')
        ));
        if ($affected === false) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Error while attempting to move Posts.');
            sleep(1);
            $this->modx->log(modX::LOG_LEVEL_ERROR,'COMPLETED');
            sleep(1);
            return false;
        } else {
            $this->modx->log(modX::LOG_LEVEL_WARN,'Moved ' . $affected .' Posts.');
        }
        return true;
    }

    /**
     * @param string $board
     *
     * @return void
     */
    public function updateCounts($board = 'oldBoard') {
        if (!$this->$board || !($this->$board instanceof disBoard)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'While attempting to update counts, $this->' . $board . ' was not found.');
            return;
        }
        /* Update the num_topics count */
        $topicsCount = $this->modx->getCount('disThread', array(
            'board' => $this->$board->get('id'),
            'private' => false,
        ));
        $this->$board->set('num_topics', $topicsCount);
        $this->modx->log(modX::LOG_LEVEL_INFO,'Setting num_topics on ' . $board . ' to: ' . $topicsCount);

        /* Update the num_replies count */
        $replyCount = $this->modx->getCount('disPost', array(
            'board' => $this->$board->get('id'),
            'AND:parent:!=' => 0,
        ));
        $this->$board->set('num_replies', $replyCount);
        $this->modx->log(modX::LOG_LEVEL_INFO,'Setting num_replies on ' . $board . ' to: ' . $replyCount);

        /* Update the total_posts count */
        $postCount = $this->modx->getCount('disPost', array(
            'board' => $this->$board->get('id'),
        ));
        $this->$board->set('total_posts', $postCount);
        $this->modx->log(modX::LOG_LEVEL_INFO,'Setting total_posts on ' . $board . ' to: ' . $postCount);

        /* Re-set the last post on the board */
        $lastPostQuery = $this->modx->newQuery('disPost');
        $lastPostQuery->where(array(
            'board' => $this->$board->get('id'),
        ));
        $lastPostQuery->sortby('createdon','DESC');
        $lastPostQuery->limit(1);
        $lastPostQuery->select($this->modx->getSelectColumns('disPost','disPost','',array('id')));
        $lastPost = $this->modx->getObject('disPost', $lastPostQuery);
        if ($lastPost instanceof disPost) {
            $this->$board->set('last_post', $lastPost->get('id'));
            $this->modx->log(modX::LOG_LEVEL_INFO,'Setting last_post on ' . $board . ' to: ' . $lastPost->get('id'));
        } else {
            $this->$board->set('last_post',0);
            $this->modx->log(modX::LOG_LEVEL_INFO,'Setting last_post on ' . $board . ' to: 0');
        }

        /* Save the board */
        $this->$board->save();
    }

    private function updateReadStates() {
        $this->modx->log(modX::LOG_LEVEL_INFO,'Updating disThreadRead data...');
        $affected = $this->modx->updateCollection('disThreadRead', array(
            'board' => $this->newBoard->get('id'),
        ), array(
            'board' => $this->oldBoard->get('id'),
        ));
        if ($affected === false) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'Error updating disThreadRead table.');
        } else {
            $this->modx->log(modX::LOG_LEVEL_WARN,'Updated ' . $affected .' disThreadRead records.');
        }
    }
}
return 'disBoardMergeProcessor';

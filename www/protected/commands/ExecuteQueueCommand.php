<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 14-04-2014
 * Time: 22:56
 */

class ExecuteQueueCommand extends CConsoleCommand {

    public function run($args) {
        $queueModel = new CommandQueue();

        $waiting_time = Yii::app()->params['time_wait_cronjob_seconds'];
        $waiting_time = $waiting_time ? $waiting_time : 20;
        $max_attempts = 60 / $waiting_time;
        for ($i = 0; $i < $max_attempts-1; $i++) {
            $tasks = $queueModel->getTasksToExecute();
            if (!empty($tasks)) {
                $this->executeTask($tasks);
                return;
            } else {
                sleep($waiting_time);
            }
        }
    }

    protected function executeTask(array $tasks){
        /* @var CommandQueue $task */
        foreach ($tasks as $task){
            try {

                $user = User::model()->findByPk($task->fk_user);
                echo PHP_EOL . 'Running Task Id ' . $task->id_command . ' created at ' . $task->created_at . ' by user ' . $user->username;
                $task->executeTask();

            } catch (Exception $e) {
                echo PHP_EOL.'The following error occurred "'.$e->getMessage().'".'.PHP_EOL.'Please see file /protected/runtime/cron.log for more details'.PHP_EOL;
                $msg = $e->getMessage().PHP_EOL;
                $msg .= 'Stack Trace:'.PHP_EOL;
                $msg .= $e->getTraceAsString().PHP_EOL.'---';
                Yii::log($msg, CLogger::LEVEL_ERROR);
            }
        }
    }
}
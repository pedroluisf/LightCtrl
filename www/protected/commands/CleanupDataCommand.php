<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 19-05-2014
 * Time: 22:22
 */

class CleanupDataCommand extends CConsoleCommand {

    const DEFAULT_MAX_DAYS_STATUS_HIST = 90;
    const DEFAULT_MAX_DAYS_COMMAND_QUEUE = 90;

    public function run($args) {

        $startTime = microtime(TRUE);

        set_error_handler("CleanupDataCommand::catch_error");

        $this->clearOldStatus();

        $this->clearOldCommandQueue();

        $endTime = microtime(TRUE);
        echo 'Total time clearing old data: '.round($endTime - $startTime, 2).' sec'.PHP_EOL;
    }

    // Deletes all status older then x days
    protected function clearOldStatus()
    {
        $deleteStatusHist = (int) Yii::app()->params['delete_old_status_hist'];
        if (!$deleteStatusHist) {
            return;
        }

        $date = new DateTime();

        $maxDays = (int) Yii::app()->params['maximum_days_status_hist'];
        if (!$maxDays) {
            $maxDays = self::DEFAULT_MAX_DAYS_STATUS_HIST;
        }

        date_sub($date, new DateInterval('P'.$maxDays.'D'));

        echo 'Preparing to delete Status History older than: '.$date->format('Y-m-d').PHP_EOL;

        $totalDeleted = StatusHist::model()->clearDataOlderThan($maxDays);

        echo 'Total Status rows deleted in History: '.$totalDeleted.PHP_EOL;
    }

    // Deletes all (finished) commands in queue older then x days
    protected function clearOldCommandQueue()
    {
        $deleteQueueCommands = (int) Yii::app()->params['delete_old_queue_commands'];
        if (!$deleteQueueCommands) {
            return;
        }

        $date = new DateTime();

        $maxDays = (int) Yii::app()->params['maximum_days_command_queue'];
        if (!$maxDays) {
            $maxDays = self::DEFAULT_MAX_DAYS_COMMAND_QUEUE;
        }

        date_sub($date, new DateInterval('P'.$maxDays.'D'));

        echo 'Preparing to delete Commands in Queue older than: '.$date->format('Y-m-d').PHP_EOL;

        $totalDeleted = CommandQueue::model()->clearDataOlderThan($maxDays);

        echo 'Total Queue rows deleted in History: '.$totalDeleted.PHP_EOL;
    }

    /**
     * Error handler, converts errors to exceptions
     */
    static public function catch_error( $num, $str, $file, $line, $context = null )
    {
        throw new ErrorException( $str, 0, $num, $file, $line );
    }
}
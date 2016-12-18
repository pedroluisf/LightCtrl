<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 14-04-2014
 * Time: 22:56
 */

class CreateScheduleCommand extends CConsoleCommand {

    /**
     * This action should run everyday minute and add to the commandQueue the actions due to run on the next minute
     *
     * @param array $args
     * @return int|void
     */
    public function run($args) {
        $date2Use = new Datetime(date("Y-m-d\TH:i:00", time()));
        $date2Use->add(new DateInterval('PT1M')); // Add 1 Minute (To allow saving in the queue at the right time)

        $scheduleModel = new CommandSchedule();
        $schedulesToQueue = $scheduleModel->getRecordsForDateTime($date2Use);

        /* @var CommandSchedule $schedule */
        foreach ($schedulesToQueue as $schedule) {
            try {
                $schedule->execute();
            } catch (Exception $e){
                echo PHP_EOL.'The following error occurred while Executing a new Schedule Task "'.$e->getMessage().'".'.PHP_EOL.'Please see file /protected/runtime/cron.log for more details'.PHP_EOL;
                $msg = $e->getMessage().PHP_EOL;
                $msg .= 'Stack Trace:'.PHP_EOL;
                $msg .= $e->getTraceAsString().PHP_EOL.'---';
                Yii::log($msg, CLogger::LEVEL_ERROR);
            }
        }

    }

}
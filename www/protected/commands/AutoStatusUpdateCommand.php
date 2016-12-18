<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 19-05-2014
 * Time: 22:22
 */

class AutoStatusUpdateCommand extends CConsoleCommand {

    const WAIT_FOR_OTHER_JOBS_SECONDS = 10;

    public function run($args) {

        set_error_handler("AutoStatusUpdateCommand::catch_error");

        // Allow all other crons to run first. We are getting conflicts on the lock flag
        echo PHP_EOL.'Waiting '.self::WAIT_FOR_OTHER_JOBS_SECONDS.' seconds for other cronjobs to run'.PHP_EOL;
        sleep(self::WAIT_FOR_OTHER_JOBS_SECONDS);

        $startTime = microtime(TRUE);

        echo PHP_EOL.'Performing Status request for all Ethernets'.PHP_EOL;

        $this->requestAllStatus();

        $endTime = microtime(TRUE);
        echo 'Total time requesting status: '.round($endTime - $startTime, 2).' sec'.PHP_EOL;
    }

    // Requests status for all ethernet
    protected function requestAllStatus()
    {
        $ethernetArray = Ethernet::model()->findAll('inactive=0');
        /** @var Ethernet $ethernet */
        foreach ($ethernetArray as $ethernet) {
            echo 'Requesting Status for Ethernet «'.$ethernet->desc.'»';
            $this->requestStatus($ethernet);
            echo '... Done '.PHP_EOL;
        }
    }

    protected function requestStatus(Ethernet $ethernet) {
        if ($ethernet->isLocked()) {
            return;
        }

        $ethernet->setLock(null);

        try {

            /* @var StatusRequestCommand $status */
            $status = Factory::getCommandModelByName(Factory::COMMAND_STATUS_REQUEST);
            $status->performCommand(null, $ethernet->id_ethernet, $status->prepareCommand(array()));

        } catch (Exception $e){
            echo PHP_EOL.'The following error occurred while retrieving Status"'.$e->getMessage().'".'
                .PHP_EOL.'Please see file /protected/runtime/cron.log for more details'.PHP_EOL;
            $msg = $e->getMessage().PHP_EOL;
            $msg .= 'Stack Trace:'.PHP_EOL;
            $msg .= $e->getTraceAsString().PHP_EOL.'---';
            Yii::log($msg, CLogger::LEVEL_ERROR);
        }

        $ethernet->unlock();
    }

    /**
     * Error handler, converts errors to exceptions
     */
    static public function catch_error( $num, $str, $file, $line, $context = null )
    {
        throw new ErrorException( $str, 0, $num, $file, $line );
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 19-05-2014
 * Time: 22:22
 */

class StatusDebugCommand extends CConsoleCommand {

    public function run($args) {
        $startTime = microtime(TRUE);

        $ethernetArray = Ethernet::model()->findAll();
        foreach ($ethernetArray as $ethernet) {
            $this->debugStatus($ethernet);
        }

        $endTime = microtime(TRUE);
        echo 'Total time: '.round($endTime - $startTime, 2).' sec'.PHP_EOL;
    }

    protected function debugStatus(Ethernet $ethernet) {
        if ($ethernet->isLocked()) {
            echo PHP_EOL.PHP_EOL.'CAN ID:' . $ethernet->id_ethernet . ' is currently occupied. Not able to process request. Please try again later';
            return;
        }

        $ethernet->setLock(null);

        try {
            set_error_handler( "StatusDebugCommand::catch_error" );

            /* @var StatusRequestCommand $status */
            echo 'Performing Request for CAN ID:' . $ethernet->id_ethernet . PHP_EOL.PHP_EOL;
            $status = Factory::getCommandModelByName(Factory::COMMAND_STATUS_REQUEST);
            $response = $status->performCommand(null, $ethernet->id_ethernet, $status->prepareCommand(array()), false);
            echo print_r(json_encode($response), true).PHP_EOL.PHP_EOL;

        } catch (Exception $e){
            echo PHP_EOL.'The following error occurred while retrieving Status"'.$e->getMessage().'".'.PHP_EOL.'Please see file /protected/runtime/cron.log for more details'.PHP_EOL;
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
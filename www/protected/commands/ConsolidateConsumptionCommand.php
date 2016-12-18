<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 19-05-2014
 * Time: 22:22
 */

class ConsolidateConsumptionCommand extends CConsoleCommand {

    public function run($args) {

        ini_set('memory_limit', '1024M');

        $startTime = microtime(TRUE);

        $lastUpdate = new DateTime(Kvp::get(Kvp::LAST_CONSOLIDATED_STATUS_DATETIME, date('Y-m-d H:i:s')));
        $updateDate = new DateTime();

        try {

            set_error_handler( "ConsolidateConsumptionCommand::catch_error" );

            $consumption = new Consumption();
            $consumption->consolidate($lastUpdate, $updateDate);

        } catch (Exception $e){
            echo PHP_EOL.
                'The following error occurred while Consolidating Consumptions Data: "'.$e->getMessage().'".'.PHP_EOL.
                'Please see file /protected/runtime/cron.log for more details'.PHP_EOL;
            $msg = $e->getMessage().PHP_EOL;
            $msg .= 'Stack Trace:'.PHP_EOL;
            $msg .= $e->getTraceAsString().PHP_EOL.'---';
            Yii::log($msg, CLogger::LEVEL_ERROR);
        }

        Kvp::set(Kvp::LAST_CONSOLIDATED_STATUS_DATETIME, $updateDate->format('Y-m-d H:i:s'));

        $endTime = microtime(TRUE);
        echo PHP_EOL.'Total time: '.round($endTime - $startTime, 2).' sec'.PHP_EOL;
    }

    /**
     * Error handler, converts errors to exceptions
     */
    static public function catch_error( $num, $str, $file, $line, $context = null )
    {
        throw new ErrorException( $str, 0, $num, $file, $line );
    }
}
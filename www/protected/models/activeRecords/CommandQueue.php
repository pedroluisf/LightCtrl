<?php

/**
 * This is the model class for table "tbl_command_queue".
 *
 * The followings are the available columns in table 'tbl_command_queue':
 * @property integer $id_command
 * @property integer $ethernet_id
 * @property integer $cmd_name
 * @property string $cmd
 * @property string $hash
 * @property string $last_response
 * @property string $status
 * @property string $retry_counter
 * @property integer $fk_user
 * @property integer $created_at
 *
 */
class CommandQueue extends CActiveRecord
{
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAILED = 'failed';
    const STATUS_ERROR = 'error';

    // We must run the failed ones first, so that we do not override new orders with old ones
    protected $priorityTasks = array(
        'getPendingDaliCommands',           // New DALI
        'getRetryableCciCommands',          // Failed Schedules
        'getPendingCciCommands',            // New Schedules
        'getRetryableEmergencyCommands',    // Failed Emergency Tests
        'getPendingEmergencyCommands',      // New Emergency tests
        'getAttributeChangeCommands',       // Attribute Changes
        'getPendingStatusCommands'          // New Manual Status
    );

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'tbl_command_queue';
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return CommandQueue the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    protected function startProcessing(){
        $this->status = self::STATUS_PROCESSING;
        $this->save();
    }

    protected function endProcessing($response, $error = false){

        $this->last_response = $response;

        if ($error){
            $this->status = self::STATUS_ERROR;
            $this->retry_counter++;
            $this->sendUserMessage();
        } else {
            $response = json_decode($response);
            if (isset($response->rsp->status) && $response->rsp->status == 'NOK') {
                $this->status = self::STATUS_FAILED;
            } else {
                $this->status = self::STATUS_FINISHED;
            }
        }

        $this->save();
    }

    public function addToQueue($attributes) {
        $queuedCommand = $this->findByAttributes(array('hash'=>$attributes['hash'], 'status'=>'pending'));
        if ($queuedCommand) {
            return $queuedCommand->getPrimaryKey();
        }

        $this->setIsNewRecord(true);

        $schema  = $this->getTableSchema();
        foreach ($attributes as $key=>$value){
            if (in_array($key, $schema->columnNames)) {
                $this->$key = $value;
            }
        }
        $this->fk_user = Yii::app()->user->getId();

        $result = $this->save();
        if ($result) {
            return $this->getPrimaryKey();
        } else {
            throw new Exception('Error registering command for execution.');
        }
    }

    /**
     * This will return a set of records to be executed from the Queue.
     * Records will obey prioritization rules.
     */
    public function getTasksToExecute() {
        $tasks = array();

        // Get tasks according to Priority
        foreach ($this->priorityTasks as $priority) {
            $tasks = array_merge($tasks, $this->$priority());
        }

        return $tasks;
    }

    /**
     * This will retrieve all pending DALI Commands from the queue
     * @return CommandQueue[]
     */
    public function getPendingDaliCommands(){
        $daliCommandName = get_class(new DaliCommand());
        return $this->findAllByAttributes(array('status'=>self::STATUS_PENDING, 'cmd_name'=>$daliCommandName));
    }

    /**
     * This will retrieve all pending CCI Commands from the queue
     * @return CommandQueue[]
     */
    public function getPendingCciCommands(){
        $cciCommandName = get_class(new ClearContactInputCommand());
        return $this->findAllByAttributes(array('status'=>self::STATUS_PENDING, 'cmd_name'=>$cciCommandName));
    }

    /**
     * This will retrieve all pending Emergency Trigger Commands from the queue
     * @return CommandQueue[]
     */
    public function getPendingEmergencyCommands(){
        $emergencyCommandName = get_class(new EmergencyTriggerCommand());
        return $this->findAllByAttributes(array('status'=>self::STATUS_PENDING, 'cmd_name'=>$emergencyCommandName));
    }

    /**
     * This will retrieve all pending Status Commands from the queue
     * @return CommandQueue[]
     */
    public function getPendingStatusCommands(){
        $statusCommandName = get_class(new StatusRequestCommand());
        return $this->findAllByAttributes(array('status'=>self::STATUS_PENDING, 'cmd_name'=>$statusCommandName));
    }

    /**
     * This will return all attribute changing commands regardless of being pending or error.
     * Attribute changes must be retried until successfully executed
     */
    public function getAttributeChangeCommands(){
        $attributeChangeCommandName = get_class(new AttributeChangeCommand());
        $commands = $this->findAll(
            array(
                'order'=>'created_at ASC',
                'condition'=>'(status = :status_pending OR status = :status_error ) AND cmd_name = :cmd_name',
                'params' => array(
                    ':status_pending'=>self::STATUS_PENDING,
                    ':status_error'=>self::STATUS_ERROR,
                    ':cmd_name'=>$attributeChangeCommandName
                )
            )
        );
        return $commands;
    }

    /**
     * This will retrieve the last Cci Command for a given component that failed, but still are not more than 1 day old and are not marked to ignore (retry_counter = -1)
     * Since we can not identify a component on the sql table, we will have to remove by php
     * @return CommandQueue[]
     */
    public function getRetryableCciCommands(){
        $cciCommandName = get_class(new ClearContactInputCommand());
        $commands = $this->findAll(
            array(
                'order'=>'created_at DESC',
                'condition'=>'status = :status AND retry_counter <> -1 AND cmd_name = :cmd_name AND DATE_ADD(created_at, INTERVAL 1 DAY) >= now()',
                'params' => array(
                    ':status'=>self::STATUS_ERROR,
                    ':cmd_name'=>$cciCommandName
                )
            )
        );

        /**
         * Remove repeated components
         * @var CommandQueue[] $commands
         */
        $count = count($commands);
        $existingComponents = array();
        for ($i=0; $i<$count; $i++) {
            $command = $commands[$i];
            try {
                $parsedCommand = json_decode($command->cmd);
                $cmd = constant($command->cmd_name .'::CMD');
                $component = $command->ethernet_id.'.'.$parsedCommand->$cmd->lc_id.'.'.$parsedCommand->$cmd->dvc_id;
                if (!in_array($component, $existingComponents)) {
                    $existingComponents[] = $component;
                } else {
                    // Save Retry Counter to -1 to say that this CCI is to be ignore from now on.
                    // This will prevent the next iteration to pick it up after completing more recent ones
                    $command->retry_counter = -1;
                    $command->save();
                    unset($commands[$i]);
                }
            } catch (Exception $e) {
                Yii::log($e->getMessage(), CLogger::LEVEL_ERROR, 'exception.'.get_class($this));
            }
        }

        //Return a Reversed array of the Results to get the most recent last
        return array_reverse($commands);
    }

    /**
     * This will retrieve the last Emergency Trigger Command that failed less times than the maximum number of retries (configurable)
     * @return CommandQueue[]
     */
    public function getRetryableEmergencyCommands(){
        $emergencyCommandName = get_class(new EmergencyTriggerCommand());
        $maxRetries = Yii::app()->params['maximum_retry_ett'];
        $maxRetries = ($maxRetries ? : 3);
        return $this->findAll('status = :status AND cmd_name = :cmd_name AND retry_counter < :retry_counter',
            array(
                ':status'=>self::STATUS_ERROR,
                ':cmd_name'=>$emergencyCommandName,
                ':retry_counter'=>$maxRetries
            )
        );
    }

    public function executeTask(){
        /* @var Ethernet $ethernetDevice */
        $ethernetDevice = Ethernet::model()->findByPk($this->ethernet_id);
        if ($ethernetDevice->isLocked()) {
            return false;
        }

        $ethernetDevice->setLock($this->fk_user);
        $this->startProcessing();

        $storedExc = null;
        $response = '';
        try {
            set_error_handler( "CommandQueue::catch_error" );

            $command = Factory::getCommandModelByName($this->cmd_name);
            $response = $command->performCommand($this->fk_user, $this->ethernet_id, $this->cmd);
        } catch (Exception $e){
            $storedExc = $e;
        }

        $this->endProcessing($response, !is_null($storedExc));
        $ethernetDevice->unlock();

        if ($storedExc){
            throw $storedExc;
        }

        return true;
    }

    /**
     * This will validate if a user message is needed and send one if that is the case
     */
    protected function sendUserMessage() {
        $emergencyCommand = new EmergencyTriggerCommand();
        $cciCommand = new ClearContactInputCommand();
        $msg = null;

        switch ($this->cmd_name) {

            case get_class($emergencyCommand):
                if ($this->retry_counter >= Yii::app()->params['maximum_retry_ett']){
                    $msg = $this->prepareMessage($emergencyCommand);
                }
                break;

            case get_class($cciCommand):

                $nextTrigger = new DateTime();
                $nextTrigger->modify("+2 minutes"); // Cronjobs for status requests are for 2 minutes
                $cmdDateWithTolerance = new DateTime($this->created_at . ' + 1 day');
                if ($cmdDateWithTolerance < $nextTrigger){
                    $msg = $this->prepareMessage($cciCommand);
                }
                break;
        }

        if ($msg !== null ){
            Notification::sendNotification($this->fk_user, Notification::NOTIFICATION_LEVEL_ERROR, $msg);
        }
    }

    protected function prepareMessage(Command $command) {

        $parsedCommand = json_decode($this->cmd);
        $commandDescription = $this->prependCommandSubTypeToDescription($command->commandDescription(), $parsedCommand->{$command::CMD}->type);

        $msg = 'The command "'.$commandDescription.'" ';
        $msg .= $this->getCommandTargetDescription();
        $msg .= 'first triggered in ' .$this->created_at. ' failed after '. $this->retry_counter.' retries.'.PHP_EOL.PHP_EOL;
        return $msg;

    }

    protected function prependCommandSubTypeToDescription($commandDescription, $subType) {
        switch ($subType) {
            case Emergency::TEST_TYPE_DURATION:
                $commandDescription = 'Duration ' . $commandDescription;
                break;
            case Emergency::TEST_TYPE_FUNCTION:
                $commandDescription = 'Function ' . $commandDescription;
                break;
        }
        return $commandDescription;
    }

    protected function getCommandTargetDescription() {
        /** @var Ethernet $ethernet */
        $ethernet = Ethernet::model()->findByPk($this->ethernet_id);
        if ($ethernet instanceof Ethernet){
            return 'for "'.$ethernet->name.'" on "'.$ethernet->area->name.'" ';
        }
        return '';
    }

    /**
     * Clears all entries of devices that belong to a given Ethernet
     * @param $ethernet_id
     */
    public static function clearData($ethernet_id){
        static::model()->deleteAll('ethernet_id=:ethernet_id', array(':ethernet_id'=>$ethernet_id));
    }

    /**
     * Clears all entries of finished commands older than x days
     * @param $days
     * @return int the number of rows deleted
     */
    public static function clearDataOlderThan($days){
        return self::model()->deleteAll(
            'DATEDIFF(NOW(), created_at) > :days',
            array(':days'=>$days)
        );
    }

    /**
     * Error handler, converts errors to exceptions
     */
    static public function catch_error( $num, $str, $file, $line, $context = null )
    {
        throw new ErrorException( $str, 0, $num, $file, $line );
    }
}
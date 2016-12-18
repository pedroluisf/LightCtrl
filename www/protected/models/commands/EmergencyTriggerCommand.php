<?php

class EmergencyTriggerCommand extends Command {

    const CMD = 'ett';
    const CLEAR = 'clear';
    const ALL = 'all';

    /**
     * Gives a readable description of what this command is
     * @return string
     */
    public function commandDescription()
    {
        return 'Emergency Test Trigger';
    }

    public function addCommandToQueue(array $parameters) {
        $this->validateParameters($parameters);
        $this->appendQueueParameters($parameters);
        $taskId = CommandQueue::model()->addToQueue($parameters);
        // try to execute it immediately
        $task = CommandQueue::model()->findByPk($taskId);
        $task->executeTask();
        return $taskId;
    }

    public function prepareCommandForScheduling($parameters) {
        $this->validateParameters($parameters);
        $this->appendQueueParameters($parameters);
        return $parameters;
    }

    protected function validateParameters($parameters){
        if (!isset($parameters['ethernet_id'])){
            throw new Exception('Missing parameter ethernet_id');
        }
        if (!isset($parameters['type'])){
            throw new Exception('Missing parameter ETT type');
        }
        if (!isset($parameters['proc'])){
            throw new Exception('Missing parameter ETT proc');
        }
    }

    protected function appendQueueParameters(&$parameters){
        $parameters['cmd_name'] = Factory::COMMAND_EMERGENCY_TRIGGER;
        $parameters['cmd'] = $this->prepareCommand($parameters);
        $parameters['hash'] = $this->createQueueHash($parameters);
    }

    protected function prepareCommand(array $parameters) {
        return json_encode(array(
            self::CMD => array(
                'lc_id'  => (isset($parameters['lc_id']) ? ''.$parameters['lc_id']  : self::ALL),
                'dvc_id' => (isset($parameters['dvc_id']) ? ''.$parameters['dvc_id']  : self::ALL),
                'type'   => $parameters['type'],
                'proc'   => $parameters['proc'],
            )
        ));
    }

    protected function createQueueHash(array $parameters) {
        return md5($parameters['ethernet_id'].$parameters['cmd'].time()); // One unique
    }

    public function performCommand($user_id, $ethernet_id, $cmd) {
        /* @var Ethernet $ethernet */
        $ethernet = Ethernet::model()->findByPk($ethernet_id);
        $this->host = $ethernet->host;

        return parent::sendCommand($cmd);
    }

    public function sendClearFlagCommand ($ethernet_id, $lcId, $dvcId, $testType) {
        /* @var Ethernet $ethernet */
        $ethernet = Ethernet::model()->findByPk($ethernet_id);
        $this->host = $ethernet->host;

        $result = parent::sendCommand(json_encode(
            array(
                self::CMD => array(
                    'lc_id'  => $lcId,
                    'dvc_id' => $dvcId,
                    'type' => $testType,
                    'proc' => self::CLEAR
                )
            )
        ));

        $result = json_decode($result);
        if (isset($result->rsp->status) && $result->rsp->status == 'NOK') {
            $msg = "Clear flags command on ethernet_id=$ethernet_id, lc_id=$lcId, dvc_id=$dvcId returned error. ";
            if (isset($result->rsp->result->err)) {
                $msg .= ' Error Code = '.$result->rsp->result->err;
            }
            Yii::log($msg, CLogger::LEVEL_ERROR, 'status.emergency.clear_flags');
        }

    }

}
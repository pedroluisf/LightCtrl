<?php

class ClearContactInputCommand extends Command {

    const CMD = 'cci';
    const ALL = 'all';
    const FIRST_SWITCH = 1;
    const DEFAULT_DATA = 'momentary';

    /**
     * Gives a readable description of what this command is
     * @return string
     */
    public function commandDescription()
    {
        return 'Clear Contact Input';
    }

    public function addCommandToQueue(array $parameters) {
        $this->validateParameters($parameters);
        $this->appendQueueParameters($parameters);
        $commandQueue = new CommandQueue();
        return $commandQueue->addToQueue($parameters);
    }

    protected function appendQueueParameters(&$parameters){
        $parameters['cmd_name'] = Factory::CLEAR_CONTACT_INPUT;
        $parameters['cmd'] = $this->prepareCommand($parameters);
        $parameters['hash'] = $this->createQueueHash($parameters);
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
        if (!isset($parameters['cci_sw_num'])){
            throw new Exception('Missing parameter cci_sw_num');
        }
        if (!isset($parameters['cci_data'])){
            throw new Exception('Missing parameter cci_data');
        }
    }

    protected function prepareCommand(array $parameters) {
        return json_encode(array(
            self::CMD => array(
                'lc_id'  => (isset($parameters['lc_id']) ? ''.$parameters['lc_id'] : $this::ALL),
                'dvc_id' => (isset($parameters['dvc_id'])  ? ''.$parameters['dvc_id'] : $this::ALL),
                'sw_num'   => (isset($parameters['cci_data']) ? $parameters['cci_sw_num'] : $this::FIRST_SWITCH),
                'data'   => (isset($parameters['cci_data']) ? $parameters['cci_data'] : $this::DEFAULT_DATA)
            )
        ));
    }

    protected function createQueueHash(array $parameters) {
        return md5($parameters['ethernet_id'].$parameters['cmd']); // One unique per Ethernet / command
    }

    public function performCommand($user_id, $ethernet_id, $cmd) {
        /* @var Ethernet $ethernet */
        $ethernet = Ethernet::model()->findByPk($ethernet_id);
        $this->host = $ethernet->host;

        return parent::sendCommand($cmd);

        // In case we want to treat an NOK as a retryable error, we use the following code
//        if (isset($response->rsp->status) && $response->rsp->status == 'NOK') {
//            $msg = "An Error occurred. ";
//            if (isset($response->rsp->result->err)) {
//                $msg .= ' Error Code = '.$response->rsp->result->err;
//            }
//            throw new Exception($msg);
//        }
    }

}
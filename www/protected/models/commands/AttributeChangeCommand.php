<?php

class AttributeChangeCommand extends Command {

    const CMD = 'cfg';

    protected $commandAcceptedParameters = array(
        'sensitivity',
        'timeout',
        'scenes'
    );

    /**
     * Gives a readable description of what this command is
     * @return string
     */
    public function commandDescription()
    {
        return 'Attribute Change on CAN';
    }

    public function addCommandToQueue(array $parameters) {
        $this->validateParameters($parameters);
        $this->appendQueueParameters($parameters);
        $commandQueue = new CommandQueue();
        $commandQueue->addToQueue($parameters);
    }

    protected function appendQueueParameters(&$parameters){
        $parameters['cmd_name'] = Factory::ATTRIBUTE_CHANGE;
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
        if (!isset($parameters['lc_id'])){
            throw new Exception('Missing parameter lc_id');
        }
        if (!isset($parameters['dvc_id'])){
            throw new Exception('Missing parameter dvc_id');
        }
    }

    protected function prepareCommand(array $parameters) {
        $values = array();
        foreach ($parameters as $key => $value) {
            if (in_array($key, $this->commandAcceptedParameters)) {
                if ($key == 'scenes'){
                    foreach ($value as $sceneKey => $sceneValue) {
                        $values['scene'.($sceneKey+1)] = (int)$sceneValue;
                    }
                } else {
                    $values[$key] = $value;
                }
            }
        }

        $command = array (
            self::CMD => array(
                'lc_id' => $parameters['lc_id'],
                'dvc_id' => $parameters['dvc_id'],
                'values' => $values
            )
        );

        return json_encode($command);
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
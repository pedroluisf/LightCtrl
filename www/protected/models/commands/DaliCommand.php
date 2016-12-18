<?php

class DaliCommand extends Command {

    const CMD = 'msg';
    const ALL = 'all';

    private $_data;

    /**
     * Gives a readable description of what this command is
     * @return string
     */
    public function commandDescription()
    {
        return 'DALI';
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

    public function performCommand($user_id, $ethernet_id, $cmd) {
        /* @var Ethernet $ethernet */
        $ethernet = Ethernet::model()->findByPk($ethernet_id);
        $this->host = $ethernet->host;

        $response = json_decode(parent::sendCommand($cmd));
        $decodedResponse = json_decode($response);

        $decodedCmd = json_decode($cmd);
        if (Dictionary::getDaliRequiresResponseByCode($decodedCmd->{self::CMD}->cmd) == 'Y') {
            $decodedResponse->rsp->result->value = $this->getResultFromResponse($decodedResponse); // We get the response right away
        }
        return $response;
    }

    public function prepareCommand(array $parameters) {
        $command = array(
            self::CMD => array(
                'lc_id' => (isset($parameters['lc_id']) ? ''.$parameters['lc_id'] : $this::ALL),
                'dvc_id' => (isset($parameters['lc_id']) ? ''.$parameters['dvc_id']  : $this::ALL),
                'cmd' => dechex($parameters['cmd']),
                'rsp' => Dictionary::getDaliRequiresResponseByCode($parameters['cmd'])
            )
        );
        if (isset($parameters['data'])) {
            $param = Dictionary::getDaliMessageParamsByCode($parameters['cmd']);
            $command[self::CMD]['data'][$param['command']] = dechex($parameters['data']);
        }
        return json_encode($command);
    }

    protected function validateParameters($parameters){
        if (!isset($parameters['ethernet_id'])){
            throw new Exception('Missing parameter ethernet_id');
        }
        if (!isset($parameters['cmd'])){
            throw new Exception('Missing parameter Dali cmd');
        }
        if (!isset($parameters['data']) && Dictionary::getDaliMessageParamsByCode($parameters['cmd'])){
            throw new Exception('Missing parameter Dali data');
        }
    }

    protected function appendQueueParameters(&$parameters){
        $parameters['cmd_name'] = Factory::COMMAND_DALI;
        $parameters['cmd'] = $this->prepareCommand($parameters);
        $parameters['hash'] = $this->createQueueHash($parameters);
    }

    protected function createQueueHash(array $parameters) {
        return md5($parameters['ethernet_id'].$parameters['cmd'].time()); // One unique
    }

    /**
     * This would query the device for the changes after a dali command to update front end status
     * @deprecated We stopped requesting status after dali commands due to can lagging
     * @param $cmd
     * @return mixed
     * @throws Exception
     */
    protected function getStatusChangeConfirmation ($cmd) {

        $command = json_decode($cmd);
        $myCmd = self::CMD;
        $statusChangeConfirmationCommand = Dictionary::getDaliMessageStatusChangeConfirmationCommandByCode(hexdec($command->$myCmd->cmd));
        if (!$statusChangeConfirmationCommand) {
            return;
        }
        $response = parent::sendCommand(json_encode(
            array(
                self::CMD => array(
                    'lc_id'  => $command->$myCmd->lc_id,
                    'dvc_id' => $command->$myCmd->dvc_id,
                    'cmd' => dechex($statusChangeConfirmationCommand),
                    'rsp' => Dictionary::getDaliRequiresResponseByCode($statusChangeConfirmationCommand)
                )
            )
        ));

        if (Dictionary::getDaliRequiresResponseByCode($statusChangeConfirmationCommand) == 'Y') {
            return $this->getResultFromResponse(json_decode($response));
        }

    }

    protected function getResultFromResponse($response){
        if (isset($response->rsp->rsp_code) && $response->rsp->rsp_code == 'OK') {
            if (!isset($response->rsp->result)) {
                throw new Exception ('Dali Command returned response code "OK", and was expecting result, however no result was found.');
            }
            return $response->rsp->result->value;
        }
    }

}
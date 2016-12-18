<?php

class StatusRequestCommand extends Command {

    const CMD = 'sts';
    const ALL = 'all';
    const LATEST_STATUS = 'latest';
    const NEW_STATUS = 'new';

    /**
     * Gives a readable description of what this command is
     * @return string
     */
    public function commandDescription()
    {
        return 'Status Update';
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

    protected function appendQueueParameters(&$parameters){
        $parameters['cmd_name'] = Factory::COMMAND_STATUS_REQUEST;
        $parameters['cmd'] = $this->prepareCommand($parameters);
        $parameters['hash'] = $this->createQueueHash($parameters);
    }

    public function prepareCommand(array $parameters) {
        return json_encode(array(
            self::CMD => array(
                'lc_id'  => (isset($parameters['lc_id']) ? ''.$parameters['lc_id'] : self::ALL),
                'dvc_id' => (isset($parameters['dvc_id'])  ? ''.$parameters['dvc_id'] : self::ALL),
                'type'   => (isset($parameters['type']) ? $parameters['type'] : self::LATEST_STATUS)
            )
        ));
    }

    protected function validateParameters($parameters){
        if (!isset($parameters['ethernet_id'])){
            throw new Exception('Missing parameter ethernet_id');
        }
        if (!isset($parameters['type'])){
            throw new Exception('Missing parameter type');
        }
    }

    protected function createQueueHash(array $parameters) {
        return md5($parameters['ethernet_id'].$parameters['cmd']); // One unique per Ethernet / command
    }

    public function performCommand($user_id, $ethernet_id, $cmd, $saveResponse = true) {
        /* @var Ethernet $ethernet */
        $ethernet = Ethernet::model()->findByPk($ethernet_id);
        $this->host = $ethernet->host;

        $response = parent::sendCommand($cmd);
        $decodedResponse = json_decode($response);

        if (isset($decodedResponse->rsp->status) && $decodedResponse->rsp->status == 'OK') {
            if ($saveResponse) {
                $this->saveDevicesStatus($ethernet_id, $decodedResponse);
            }
        }elseif (!isset($decodedResponse->rsp->status) || $decodedResponse->rsp->status != 'NOK') {
            throw new Exception('Invalid Response');
        }

        return $response;
    }

    protected function saveDevicesStatus($ethernetId, $response) {
        if (!isset($response->rsp->result->lcs)) {
            return;
        }

        foreach ($response->rsp->result->lcs as $lightController) {
            if (!is_array($lightController->devices)) {
                continue;
            }
            foreach($lightController->devices as $device) {
                if (!isset($device->dvc_id) || !isset($device->status)) {
                    continue;
                }

                try {
                    // Gets Component by the DrawId of the response
                    /* @var DeviceTransfer $component */
                    $component = $this->getComponent($ethernetId, $lightController->lc_id, $device->dvc_id);

                    // If no component or value in exclusions list, we continue
                    if (!$component || $this->excludeStatusSave($device->status)) {
                        continue;
                    }

                    // Descriptions
                    $fkDescription = Description::getComponentDescriptionId($component);

                    // Saves Status
                    $deviceData = array(
                        'fk_ethernet' => $ethernetId,
                        'lc_id' => $lightController->lc_id,
                        'dvc_id' => $device->dvc_id,
                        'type' => $component->dev_type,
                        'fk_description' => $fkDescription
                    );

                    if (!$status = Status::model()->findByPk(array('fk_ethernet'=>$ethernetId, 'lc_id'=>$component->lc_id, 'dvc_id'=>$component->dvc_id))) {
                        $status = new Status('create');
                    }
                    $status->attributes = $deviceData;
                    $status->saveDeviceStatus($component->dev_type, $device->status);

                    // Saves Emergency
                    $emergency = new Emergency('create');

                    $emergency->fk_description = $fkDescription;
                    $emergency->lc_id = $lightController->lc_id;
                    $emergency->dvc_id = $device->dvc_id;
                    $emergency->fk_ethernet = $ethernetId;
                    $emergency->deviceType = $component->dev_type;
                    $emergency->saveEmergencyStatus($device->status);

                } catch (Exception $e) {
                    $msg = $e->getMessage().PHP_EOL;
                    $msg .= 'Stack Trace:'.PHP_EOL;
                    $msg .= $e->getTraceAsString().PHP_EOL.'---';
                    Yii::log($msg, CLogger::LEVEL_ERROR);
                }
            }
        }

        return true;
    }

    protected function getComponent($ethernetId, $lcId, $dvcId){
        $parser = new JsonComponentsParser($ethernetId);
        try {
            $component = $parser->getDeviceByLcIdAndDvcId($lcId, $dvcId);
            if ($component instanceof DeviceTransfer) {
                return $component;
            }
        } catch (Exception $e){
        }
    }

    protected function excludeStatusSave($status){
        $exclusions = explode(',', Yii::app()->params['exclusions_on_status_save']);

        return in_array(strtoupper($status), array_map('strtoupper', $exclusions));
    }

}
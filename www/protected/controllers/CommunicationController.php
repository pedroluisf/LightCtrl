<?php

class CommunicationController extends Controller
{

    /**
     * @return array action filters
     */
    public function filters()
    {
        return array(
            'accessControl', // perform access control for operations
            'postOnly', // we only allow access via POST request
        );
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow authenticated users to perform this actions
                'actions'=>array('requestStatus','getStatus','getDaliParams','sendDaliCommand','triggerEmergencyTest','getNotifications'),
                'users'=>array('@'),
            ),
            array('deny',  // deny all users
                'users'=>array('*'),
            ),
        );
    }

    /**
     * Sets a request for the latest known Status or a Status Update from one or more devices
     * @return int - The id of the queue where request was set
     */
    public function actionRequestStatus() {
        $component = $this->getComponentAccordingToRequest();
        $parameters = $component->toArray();
        $parameters['type'] = Yii::app()->request->getParam('type');

        $command = Factory::getCommandModelByName(Factory::COMMAND_STATUS_REQUEST);
        try {
            $response = $command->addCommandToQueue($parameters);
            $this->sendClientResponse(true, 'Status Command sent successfully', $response);
        } catch (Exception $e) {
            $msg = $e->getMessage().PHP_EOL;
            $msg .= 'Stack Trace:'.PHP_EOL;
            $msg .= $e->getTraceAsString().PHP_EOL.'---';
            Yii::log($msg, CLogger::LEVEL_ERROR);
            $this->sendClientResponse(false, 'Status Request Failed');
        }
    }

    /**
     * Request the status the server has stored for a given Area
     * @return array (ethernet_id => status)
     */
    public function actionGetStatus() {
        $area_id = Yii::app()->request->getParam('area_id');
        if ($area_id){
            $this->sendClientResponse(true, 'Status Request Successful', Status::getReadableStatusForAllDevicesByArea($area_id));
        } else {
            $this->sendClientResponse(false, 'Area Id not provided');
        }
    }

    /**
     * This action serves an Ajax request with additional parameters controls for a Dali Msg in a json format
     */
    public function actionGetDaliParams()
    {
        $code = Yii::app()->request->getParam('code');
        $ethernetId = Yii::app()->request->getParam('ethernet_id');
        $drawingId = Yii::app()->request->getParam('drawing_id');
        $jsonParser = new JsonComponentsParser($ethernetId);
        $component = $jsonParser->getDeviceByDrawId($drawingId);

        $params = Dictionary::getDaliMessageParamsByCode($code);
        $status = Status::model()->findByPk(array('fk_ethernet'=>$ethernetId, 'lc_id'=>$component->lc_id, 'dvc_id'=>$component->dvc_id));
        try {
            $html = $this->renderPartial('_'.$params['command'].'Params', array('code' => $code, 'status' => $status, 'component' => $component), true);
            Yii::app()->getClientScript()->renderBodyBegin($html);
            Yii::app()->getClientScript()->renderBodyEnd($html);
            $this->sendClientResponse(true, 'Dali Params Responded OK', $html);
        } catch (Exception $e){
            $this->sendClientResponse(false, $e->getMessage());
        }
    }

    /**
     * Sets a request for a DALI Command to one or more devices
     * @return int - The id of the queue where request was set
     */
    public function actionSendDaliCommand() {
        $component = $this->getComponentAccordingToRequest();
        $parameters = $component->toArray();
        $parameters['cmd'] = Yii::app()->request->getParam('cmd');
        $parameters['data'] = Yii::app()->request->getParam('data');

        $command = Factory::getCommandModelByName(Factory::COMMAND_DALI);
        try {
            $command->addCommandToQueue($parameters);
            $response = array();
            if ($component instanceof DeviceTransfer) {
                $response[$component->ethernet_id] = array();
                $response[$component->ethernet_id][$component->draw_id] = Status::getReadableStatusByPK($component->ethernet_id, $component->lc_id, $component->dvc_id);
            }
            $this->sendClientResponse(true, 'DALI Command sent successfully', $response);
        } catch (Exception $e) {
            $msg = $e->getMessage().PHP_EOL;
            $msg .= 'Stack Trace:'.PHP_EOL;
            $msg .= $e->getTraceAsString().PHP_EOL.'---';
            Yii::log($msg, CLogger::LEVEL_ERROR);
            $this->sendClientResponse(false, 'DALI Command Failed');
        }
    }

    /**
     * Sets a request for Trigger one or more devices to start/stop an Emergency Test
     * @return int - The id of the queue where request was set
     */
    public function actionTriggerEmergencyTest() {
        $component = $this->getComponentAccordingToRequest();
        $parameters = $component->toArray();
        $parameters['type'] = Yii::app()->request->getParam('type');
        $parameters['proc'] = Yii::app()->request->getParam('proc');

        $command = Factory::getCommandModelByName(Factory::COMMAND_EMERGENCY_TRIGGER);
        try {
            $response = $command->addCommandToQueue($parameters);
            $this->sendClientResponse(true, 'Emergency Command sent successfully', $response);
        } catch (Exception $e) {
            $msg = $e->getMessage().PHP_EOL;
            $msg .= 'Stack Trace:'.PHP_EOL;
            $msg .= $e->getTraceAsString().PHP_EOL.'---';
            Yii::log($msg, CLogger::LEVEL_ERROR);
            $this->sendClientResponse(false, 'Trigger Emergency Test Failed');
        }
    }

    /**
     * Gets a Transfer with the Information of the Component requested
     * @return TransferAbstract
     */
    protected function getComponentAccordingToRequest(){
        $ethernetId = Yii::app()->request->getParam('ethernet_id');
        $componentId = Yii::app()->request->getParam('drawing_id');
        try {
            /* @var Ethernet $ethernetModel */
            $parser = new JsonComponentsParser($ethernetId);
            $component = $parser->getDeviceByDrawId($componentId);
        } catch (Exception $e){
            $this->sendClientResponse(false, $e->getMessage());
        }
        return $component;
    }

    /**
     * Request Any new Notification
     * @return array (ethernet_id => status)
     */
    public function actionGetNotifications() {
        $fromDate = new DateTime();
        $fromDate->sub(new DateInterval('PT1M'));
        $notifications = Notification::getUnreadForUser($fromDate->format('Y-m-d H:i:s'));
        if ($notifications) {
            $this->sendClientResponse(true, 'There are new Notifications', $this->renderPartial('/partial/_notifications', array('notifications' => $notifications), true));
        } else {
            $this->sendClientResponse(false, 'There are No new Notifications');
        }
    }

    /**
     * Sends feedback to client
     * @param bool $success
     * @param string $message
     * @param mixed $data
     */
    protected function sendClientResponse($success, $message, $data = null){
        $result = array(
            'success' => $success,
            'message' => $message,
            'data' => $data
        );
        echo json_encode($result);
        Yii::app()->end();
    }

}
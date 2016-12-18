<?php

/**
 * ComponentForm class.
 * ComponentForm is the data structure for saving components in the configuration json
 */
class ComponentForm extends CFormModel
{
    public $altered_config = false;

    protected static $changeableAttributes = array(
        'EthernetTransfer' => array(),
        'LightctrTransfer' => array(
            'custom_location' => array(
                'view' => '_text',
                'CAN' => false),
            'custom_description' => array(
                'view' => '_text',
                'CAN' => false)
        ),
        'DeviceTransfer' => array(
            'scenes' => array(
                'view' => '_slider',
                'CAN' => true),
            'sensitivity' => array(
                'view' => '_number',
                'validator' => array(
                    'min'=>1,
                    'max'=>9
                ),
                'CAN' => true),
            'timeout' => array(
                'view' => '_number',
                'validator' => array(
                    'min'=>1,
                    'max'=>255
                ),
                'CAN' => true),
            'custom_location' => array(
                'view' => '_text',
                'CAN' => false),
            'custom_description' => array(
                'view' => '_text',
                'CAN' => false)
        ),
    );

	/**
	 * Declares the validation rules.
	 */
	public function rules()
	{
		return array(
			array('component', 'required'),
		);
	}

    public static function getChangeableAttributes(TransferAbstract $component) {
        $deviceType = get_class($component);
        return isset(self::$changeableAttributes[$deviceType]) ? self::$changeableAttributes[$deviceType] : null;
    }

    public function save($ethernetId, TransferAbstract $transfer, array $values) {
        $this->prepareValues($transfer, $values);

        if (!$this->isValid($transfer, $values)) {
            return false;
        }

        $result = $this->updateDevice($ethernetId, $transfer, $values);

        $this->sendValuesCan($transfer);

        return $result;
    }

    /**
     * Prepare values and remove all attributes that were not supposed to be updated by user.
     * This is to prevent user data manipulation
     */
    protected function prepareValues(TransferAbstract $transfer, array &$values) {
        $changeableAttributes = self::$changeableAttributes[get_class($transfer)];
        foreach ($values as $key => &$value) {
            if (!array_key_exists($key, $changeableAttributes)) {
                unset ($values[$key]);
            } elseif ($key == 'scenes') {
                foreach ($value as $sceneKey => $sceneValue) {
                    if (strtolower($sceneValue) == 'disabled') {
                        $value[$sceneKey] = 255;
                    } else {
                        $value[$sceneKey] = (int) $sceneValue;
                    }
                }
            } elseif ($changeableAttributes[$key]['view'] == '_number') {
                $value = (int) $value;
            }
        }
    }

    /**
     * Server side validation on the validators
     *
     * @param $transfer
     * @param array $values
     * @return bool
     */
    protected function isValid($transfer, array $values) {
        $changeableAttributes = self::$changeableAttributes[get_class($transfer)];
        foreach ($changeableAttributes as $attrKey => $attrRules) {
            if (!isset($values[$attrKey])) {
                continue;
            }
            if (isset($attrRules['validator']) && ($values[$attrKey] != '' || $values[$attrKey] != null) ) { // Only validates if has value to validate
                if ($values[$attrKey] < $attrRules['validator']['min'] || $values[$attrKey] > $attrRules['validator']['max']) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Saves the Updated Component
     *
     * @param $ethernetId
     * @param TransferAbstract $transfer
     * @param array $values
     * @return bool
     * @throws Exception
     */
    protected function updateDevice($ethernetId, TransferAbstract $transfer, array $values) {
        /** @var Ethernet $ethernetModel */
        $ethernetModel = Ethernet::model()->findByPk($ethernetId);

        foreach ($values as $attrKey => $attrValue) {
            $transfer[$attrKey] = $attrValue;
        }

        $jsonUpdater = new JsonComponentsUpdater($ethernetId);
        $jsonUpdater->updateDevice($transfer, array_keys(self::$changeableAttributes[get_class($transfer)]) );

        $ethernetModel->config = json_encode($jsonUpdater->getJsonComponents());
        if ($this->altered_config) {
            $ethernetModel->altered_config = true;
        } else {
            $ethernetModel->refresh_dependencies = true;
        }
        return $ethernetModel->save();
    }

    /**
     * Sends the new values over to the CAN
     *
     * @param TransferAbstract $transfer
     */
    protected function sendValuesCan(TransferAbstract $transfer) {
        $mustSendCAN = false;
        // If we do not have values to send, do not create command
        $changeableAttributes = self::$changeableAttributes[get_class($transfer)];
        foreach ($changeableAttributes as $attrKey => $attrRules) {
            if ($attrRules['CAN']) {
                $mustSendCAN = true;
                break;
            }
        }
        if ($mustSendCAN){
            $attributeChangeCommand = new AttributeChangeCommand();
            $attributeChangeCommand->addCommandToQueue($transfer->toArray());
        }
    }

}

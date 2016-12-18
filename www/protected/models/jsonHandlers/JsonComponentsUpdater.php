<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 16-09-2014
 * Time: 22:41
 */

class JsonComponentsUpdater extends JsonComponentsBase {

    /* This maps the fields received on a Transfer to the correct key on the json config
       Beware that we can add any key to the transfer, even if it does not exist on the original structure, as long
       as we make the mapping to the correct field on the json config.
       Later on when reading, it will be populated to the correct placeholder on the existing structure of the transfer.
       This also means we can have several fields pointing to the same destination */
    protected $attributeMapper = array(
        'EthernetTransfer' => array(),
        'LightctrTransfer' => array(
            self::UNIQUE_ID_KEY => self::UNIQUE_ID_KEY,
            self::SOURCE_CUSTOM_LOCATION => self::FINAL_CUSTOM_LOCATION,
            self::SOURCE_CUSTOM_DESCRIPTION => self::FINAL_CUSTOM_DESCRIPTION
        ),
        'DeviceTransfer' => array(
            self::UNIQUE_ID_KEY => self::UNIQUE_ID_KEY,
            self::SOURCE_SCENES => self::FINAL_SCENES,
            self::SOURCE_SENSITIVITY => self::FINAL_SENSITIVITY,
            self::SOURCE_TIMEOUT => self::FINAL_TIMEOUT,
            self::SOURCE_CUSTOM_LOCATION => self::FINAL_CUSTOM_LOCATION,
            self::SOURCE_CUSTOM_DESCRIPTION => self::FINAL_CUSTOM_DESCRIPTION,
            self::SOURCE_ENERGETIC_CLASS => self::FINAL_ENERGETIC_CLASS
        ),
    );

    /**
     * Updates the info on the json with the info received on the transfer based on the fields2Update
     *
     * @param TransferAbstract $device
     * @param array $fields2Update
     * @throws Exception
     */
    public function updateDevice(TransferAbstract $device, array $fields2Update) {
        $draw_id = strval($this->validateAndGetField($this->ethernet_config, self::UNIQUE_ID_KEY));
        if ($device['draw_id'] === $draw_id) {
            $this->updateDeviceInfo($this->ethernet_config, $device, $fields2Update);
            return;
        }
        foreach ($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST} as &$lightCtrl) {
            $draw_id = strval($this->validateAndGetField($lightCtrl, self::UNIQUE_ID_KEY));
            if ($device['draw_id'] === $draw_id) {
                $this->updateDeviceInfo($lightCtrl, $device, $fields2Update);
                return;
            }
            foreach ($lightCtrl->{self::FINAL_DEVICE_LIST} as &$dev) {
                $draw_id = strval($this->validateAndGetField($dev, self::UNIQUE_ID_KEY));
                if ($device['draw_id'] === $draw_id) {
                    $this->updateDeviceInfo($dev, $device, $fields2Update);
                    return;
                }
                if ($this->isVirtualSwitch($lightCtrl, $device) && isset($device->{self::FINAL_SCC})) {
                    foreach ($device->{self::FINAL_SCC} as &$virtualSwitch) {
                        $draw_id = strval($this->validateAndGetField($virtualSwitch, self::UNIQUE_ID_KEY));
                        if ($device['draw_id'] === $draw_id) {
                            $this->updateDeviceInfo($virtualSwitch, $device, $fields2Update);
                            return;
                        }
                    }
                }
            }
        }

        throw new Exception('Component not found «'.$device['draw_id'].'»');
    }


    /**
     * Updates the info on one json node with the info received on the transfer based on the fields2Update
     *
     * @param $node
     * @param TransferAbstract $device
     * @param array $fields2Update
     */
    protected function updateDeviceInfo(&$node, TransferAbstract $device, array $fields2Update) {
        $attributeMapper = $this->attributeMapper[get_class($device)];
        $deviceArray = $device->toArray();

        foreach($fields2Update as $field) {
            if (isset($attributeMapper[$field]) // Do we have a mapping?
                && isset($deviceArray[$field]) // Do we have it on the device?
                && $deviceArray[$field] !== null) { // Do we have a value to update?
                $node->$attributeMapper[$field] = $deviceArray[$field];
            }
        }

        // Update cache after any device update
        self::$ethernetConfigCache[$this->id_ethernet] = $this->ethernet_config;
    }

} 
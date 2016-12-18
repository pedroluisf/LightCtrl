<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 14-03-2014
 * Time: 0:03
 */

class JsonComponentsParser extends JsonComponentsBase {

    public function getEthernetComponent() {
        return $this->mapEthernet($this->ethernet_config);
    }

    /**
     * Gets a parsed Component by it's LC ID + Dvc ID
     * @param lcId - The ID of the Light controller
     * @param dvcId - The ID of the Device
     * @return TransferAbstract
     * @throws Exception
     */
    public function getDeviceByLcIdAndDvcId($lcId, $dvcId=null, $retrieveDisabled=false){

        $component = null;

        if (isset($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST}) && is_array($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST})) {
            foreach ($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST} as $lightCtrl) {
                if (($retrieveDisabled || $this->isActiveDevice($lightCtrl)) && $lcId == $lightCtrl->{self::FINAL_COMPONENT_ID}) {
                    if ($dvcId === null) {
                        return $this->mapLightCtrl($lightCtrl);
                    } else {
                        foreach ($lightCtrl->{self::FINAL_DEVICE_LIST} as $device) {
                            // We either get the device (using dvc_id)
                            if (($retrieveDisabled || $this->isActiveDevice($device)) && $dvcId == $device->{self::FINAL_COMPONENT_ID}) {
                                return $this->mapDevice($lightCtrl, $device);
                            }
                            // Or we get one of the scc, but for this case we need to use draw_id as they all have the same id
                            if ($this->isVirtualSwitch($lightCtrl, $device) && isset($device->{self::FINAL_SCC})) {
                                foreach ($device->{self::FINAL_SCC} as $virtualSwitch) {
                                    $draw_id = strval($this->validateAndGetField($virtualSwitch, self::UNIQUE_ID_KEY));
                                    if ($this->isActiveDevice($device) && $dvcId === $draw_id) { // This is correct!! We compare the dvc_id to the draw_id
                                        return $this->mapDevice($lightCtrl, $virtualSwitch);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        throw new Exception('Component not found « '.$lcId.' / '.$dvcId.' »');

    }

    /**
     * Gets a parsed Component by it's DrawId
     * @param id - the component to look for
     * @return TransferAbstract
     * @throws Exception
     */
    public function getDeviceByDrawId($id){

        $component = null;
        $draw_id = strval($this->validateAndGetField($this->ethernet_config, self::UNIQUE_ID_KEY));
        if ($this->isActiveDevice($this->ethernet_config) && $id === $draw_id) {
            return $this->mapEthernet($this->ethernet_config);
        }
        if (isset($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST}) && is_array($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST})) {
            foreach ($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST} as $lightCtrl) {
                $draw_id = strval($this->validateAndGetField($lightCtrl, self::UNIQUE_ID_KEY));
                if ($this->isActiveDevice($lightCtrl) && $id === $draw_id) {
                    return $this->mapLightCtrl($lightCtrl);
                }
                foreach ($lightCtrl->{self::FINAL_DEVICE_LIST} as $device) {
                    $draw_id = strval($this->validateAndGetField($device, self::UNIQUE_ID_KEY));
                    if ($this->isActiveDevice($device) && $id === $draw_id) {
                        return $this->mapDevice($lightCtrl, $device);
                    }
                    if ($this->isVirtualSwitch($lightCtrl, $device) && is_array($device->{self::FINAL_SCC})) {
                        foreach ($device->{self::FINAL_SCC} as $virtualSwitch) {
                            $draw_id = strval($this->validateAndGetField($virtualSwitch, self::UNIQUE_ID_KEY));
                            if ($this->isActiveDevice($virtualSwitch) && $id === $draw_id) {
                                return $this->mapDevice($lightCtrl, $virtualSwitch);
                            }
                        }
                    }
                }
            }
        }

        throw new Exception('Component not found «'.$id.'»');

    }

    /**
     * Gets all Devices that belong to a group
     * @param group - the group to look for
     * @return array[DeviceTransfer]
     * @throws Exception
     */
    public function getDevicesByGroup($group){

        $components = array();

        if (isset($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST}) && is_array($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST})) {
            foreach ($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST} as $lightCtrl) {
                foreach ($lightCtrl->{self::FINAL_DEVICE_LIST} as $device) {
                    if ($this->isActiveDevice($device)) {
                        if (isset($device->{self::FINAL_GROUPS})) {
                            foreach ($device->{self::FINAL_GROUPS} as $key => $value) {
                                if ($key + 1 == $group && $value) {
                                    $components[] = $this->mapDevice($lightCtrl, $device);
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $components;
    }

    /**
     * Gets all Devices that belong to a LightController
     * @param lc_id - the LC to look for
     * @return array[DeviceTransfer]
     * @throws Exception
     */
    public function getDevicesByLcID($lcId){

        $components = array();

        if (isset($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST}) && is_array($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST})) {
            foreach ($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST} as $lightCtrl) {
                if ($lcId == $lightCtrl->{self::FINAL_COMPONENT_ID}) {
                    foreach ($lightCtrl->{self::FINAL_DEVICE_LIST} as $device) {
                        if ($this->isActiveDevice($device)) {
                            $components[] = $this->mapDevice($lightCtrl, $device);
                        }
                    }
                }
            }
        }

        return $components;
    }

    /**
     * Gets all Devices
     * @return array[DeviceTransfer]
     * @throws Exception
     */
    public function getAllDevices(){

        $components = array();

        if (isset($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST}) && is_array($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST})) {
            foreach ($this->ethernet_config->{self::FINAL_LIGHTCTRL_LIST} as $lightCtrl) {
                foreach ($lightCtrl->{self::FINAL_DEVICE_LIST} as $device) {
                    if ($this->isActiveDevice($device)) {
                        $components[] = $this->mapDevice($lightCtrl, $device);
                    }
                }
            }
        }

        return $components;
    }

    protected function mapEthernet($eth){
        $component = new EthernetTransfer();

        $component->draw_id = $this->validateAndGetField($eth, self::UNIQUE_ID_KEY);
        $component->ethernet_id = $this->validateAndGetField($eth, self::FINAL_COMPONENT_ID);
        $component->description = $this->getOptionalField($eth, self::FINAL_DESCRIPTION, 'CAN Ethernet');

        return $component;
    }

    protected function mapLightCtrl($lightCtrl){
        $component = new LightctrTransfer();

        $component->ethernet_id = $this->id_ethernet;
        $component->draw_id = $this->validateAndGetField($lightCtrl, self::UNIQUE_ID_KEY);
        $component->lc_id = $this->validateAndGetField($lightCtrl, self::FINAL_COMPONENT_ID);
        $component->description = $this->getOptionalField($lightCtrl, self::FINAL_DESCRIPTION, 'Light Controller');
        $component->type = $this->getOptionalField($lightCtrl, self::FINAL_TYPE);
        $component->type_description = Dictionary::translateControllerTypeToDescription($component->type);
        $component->custom_location = $this->getOptionalField($lightCtrl, self::FINAL_CUSTOM_LOCATION);
        $component->custom_description = $this->getOptionalField($lightCtrl, self::FINAL_CUSTOM_DESCRIPTION);

        if (isset($lightCtrl->{self::FINAL_FIRMWARE_MIN_VERSION}) && isset($lightCtrl->{self::FINAL_FIRMWARE_MAX_VERSION})){
            $component->firmware_version = $this->getOptionalField($lightCtrl, self::FINAL_FIRMWARE_MAX_VERSION).'.'.$this->getOptionalField($lightCtrl, self::FINAL_FIRMWARE_MIN_VERSION);
        }

        return $component;
    }

    protected function mapDevice($lightCtrl, $device){
        $component = new DeviceTransfer();

        $component->ethernet_id = $this->id_ethernet;
        $component->lc_id = $this->validateAndGetField($lightCtrl, self::FINAL_COMPONENT_ID);
        $component->dvc_id = $this->validateAndGetField($device, self::FINAL_COMPONENT_ID);
        $component->draw_id = $this->validateAndGetField($device, self::UNIQUE_ID_KEY);

        $component->dev_type = $this->validateAndGetField($device, self::FINAL_TYPE);
        $component->description = $this->validateAndGetField($device, self::FINAL_DESCRIPTION);
        $component->custom_location = $this->getOptionalField($device, self::FINAL_CUSTOM_LOCATION);
        $component->custom_description = $this->getOptionalField($device, self::FINAL_CUSTOM_DESCRIPTION);
        $component->sensitivity = $this->getOptionalField($device, self::FINAL_SENSITIVITY, null);
        $component->timeout = $this->getOptionalField($device, self::FINAL_TIMEOUT, null);
        $component->energetic_class = $this->getOptionalField($device, self::FINAL_ENERGETIC_CLASS, null);

        if ($component->dev_type >= 128) { // We only show the firmware to our proprietary devices (type >= 128)
            if (isset($device->{self::FINAL_FIRMWARE_MIN_VERSION}) && isset($device->{self::FINAL_FIRMWARE_MAX_VERSION})){
                $component->firmware_version = $this->getOptionalField($device, self::FINAL_FIRMWARE_MAX_VERSION).'.'.$this->getOptionalField($device, self::FINAL_FIRMWARE_MIN_VERSION);
            }
        }

        // Scenes are optional within devices
        if (isset($device->{self::FINAL_SCENES})){
            $component->scenes = $device->{self::FINAL_SCENES};
        }

        // Groups are optional within devices
        if (isset($device->{self::FINAL_GROUPS})){
            $groups = array();
            foreach ($device->{self::FINAL_GROUPS} as $key => $value) {
                if ($value){
                    $groups[] = $key + 1;
                }
            }
            if (empty($groups)) {
                $component->groups = 'none';
            } else {
                $component->groups = implode(',', $groups);

                // Output Groups List from LightCtrl gives us information for a component in a group
                foreach ($lightCtrl->{self::FINAL_OUTPUT_GROUPS_LIST} as $outputGroupInfo) {
                    // Check for an Output Group for one of the groups we have. We will consider only the first as they shouldn't have more they one
                    foreach ($groups as $group){
                        if (isset($outputGroupInfo->{self::FINAL_OUTPUT_GROUP_NUMBER})
                            && $outputGroupInfo->{self::FINAL_OUTPUT_GROUP_NUMBER} == $group) {
                            // Assign the value for the component
                            if (isset($outputGroupInfo->{self::FINAL_OUTPUT_DAYLIGHT_SENSOR_TARGET})){
                                $component->target_lux = $outputGroupInfo->{self::FINAL_OUTPUT_DAYLIGHT_SENSOR_TARGET};
                            }
                            break 2; // Leave as we already found
                        }
                    }
                }
            }
        }

        return $component;
    }

}
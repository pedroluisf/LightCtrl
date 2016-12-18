<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 18-09-2014
 * Time: 23:46
 */

class JsonComponentsMinifier extends JsonComponentsBase {

    /**
     * @param int $id_ethernet
     */
    public function __construct($id_ethernet) {
        $this->id_ethernet = $id_ethernet;
    }

    /**
     * Receive a config json and minifies it in order to save a shorter version with only the needed elements / attributes
     * @param string $json
     * @return string
     */
    public function minifyJsonConfig($json) {

        $this->validateJson($json);

        $ethernet = $this->minifyEthernet($json);

        // Iterate all Light Controllers
        $lightCtrlList = array();
        foreach ($json->{self::SOURCE_LIGHTCTRL_LIST} as $lightCtrl) {
            $draw_id = strval($this->validateAndGetField($lightCtrl, self::UNIQUE_ID_KEY));
            if ($draw_id == '-1' && $lightCtrl->{self::SOURCE_LIGHTCTRL_ID} != 63) { // 63 needed for virtual CCIs
                continue;
            }
            $minifiedLightCtrl = $this->minifyLightCtrl($lightCtrl);

            // Iterate all Devices
            $deviceList = array();
            foreach ($lightCtrl->{self::SOURCE_DEVICE_LIST} as $device) {
                $draw_id = strval($this->validateAndGetField($device, self::UNIQUE_ID_KEY));
                if ($draw_id == '-1' && !($this->isVirtualSwitch($lightCtrl, $device) && isset($device->{self::SOURCE_SCC}))) {
                    continue;
                }
                $minifiedDevice = $this->minifyDevice($device);

                // Iterate Virtual CCIs if applicable
                if ($this->isVirtualSwitch($lightCtrl, $device) && isset($device->{self::SOURCE_SCC})) {
                    $sccList = array();
                    foreach ($device->{self::SOURCE_SCC} as $virtualSwitch) {
                        $sccList[] = $this->minifyDevice($virtualSwitch);
                    }
                    $minifiedDevice->{self::FINAL_SCC} = $sccList;
                }
                $deviceList[] = $minifiedDevice;
            }
            $minifiedLightCtrl->{self::FINAL_DEVICE_LIST} = $deviceList;
            $lightCtrlList[] = $minifiedLightCtrl;
        }

        $ethernet->{self::FINAL_LIGHTCTRL_LIST} = $lightCtrlList;

        return json_encode($ethernet);

    }

    /**
     * Validate the received json
     *
     * @param $json
     * @throws Exception
     */
    protected function validateJson($json) {

        // Validate if json has EthernetId
        if (!isset($json->{self::SOURCE_ETHERNET_ID})){
            throw new FileParsingException ('Missing the mandatory field "'.self::SOURCE_ETHERNET_ID.'"');
        }

        // Validate if json has the EthernetId like the id given
        if ($json->{self::SOURCE_ETHERNET_ID} != $this->id_ethernet){
            throw new FileParsingException ('EthernetId ('.$json->{self::SOURCE_ETHERNET_ID}.') is different from the given CANETH ID ('.$this->id_ethernet.')');
        }

        // Validates if drawIds are not repeated
        $validation = $this->validateDrawIds($json);
        if ($validation !== true) {
            throw new FileParsingException ('There are repeated DrawIds ('.$validation.'). Please make sure each drawId is unique');
        }
    }

    /**
     * This will validate we don't have repeated DrawIds
     * @param $config
     * @return bool
     */
    private function validateDrawIds($config) {
        $drawIds = array();

        try {

            // Add to list
            $drawIds[] = $config->{self::UNIQUE_ID_KEY};

            // Look in LightControllers
            foreach ($config->{self::SOURCE_LIGHTCTRL_LIST} as $lightCtrl) {

                if ($lightCtrl->{self::UNIQUE_ID_KEY} != -1){

                    // If we already have, validation fails
                    if (in_array($lightCtrl->{self::UNIQUE_ID_KEY}, $drawIds)) {
                        return $lightCtrl->{self::UNIQUE_ID_KEY};
                    }

                    // Add to list
                    $drawIds[] = $lightCtrl->{self::UNIQUE_ID_KEY};

                    // Look in devices
                    foreach ($lightCtrl->{self::SOURCE_DEVICE_LIST} as $device) {

                        if ($device->{self::UNIQUE_ID_KEY} != -1){

                            // If we already have, validation fails
                            if (in_array($device->{self::UNIQUE_ID_KEY}, $drawIds)) {
                                return $device->{self::UNIQUE_ID_KEY};
                            }

                            // Add to list
                            $drawIds[] = $device->{self::UNIQUE_ID_KEY};
                        }
                    }

                }
            }

        } catch (Exception $e) {
            // Ignore. It will be dealt later.
        }
        return true;
    }

    /**
     * Reads the relevant information from a json relative to a Ethernet and returns it as an Object
     * @param string $json
     * @return string
     */
    protected function minifyEthernet($json) {
        $component = new StdClass();
        $component->{self::UNIQUE_ID_KEY} = $this->validateAndGetField($json, self::UNIQUE_ID_KEY);
        $component->{self::FINAL_COMPONENT_ID} = $this->validateAndGetField($json, self::SOURCE_ETHERNET_ID);
        $component->{self::FINAL_DESCRIPTION} = $this->getOptionalField($json, self::SOURCE_DESCRIPTION);
        return $component;
    }

    /**
     * Reads the relevant information from a json relative to a LightController and returns it as an Object
     * @param string $json
     * @return string
     */
    protected function minifyLightCtrl($json) {
        $component = new StdClass();
        $component->{self::UNIQUE_ID_KEY} = $this->validateAndGetField($json, self::UNIQUE_ID_KEY);
        $component->{self::FINAL_COMPONENT_ID} = $this->validateAndGetField($json, self::SOURCE_LIGHTCTRL_ID);
        $component->{self::FINAL_DESCRIPTION} = $this->getOptionalField($json, self::SOURCE_DESCRIPTION);
        $component->{self::FINAL_CUSTOM_DESCRIPTION} = $this->getOptionalField($json, self::SOURCE_CUSTOM_DESCRIPTION);
        $component->{self::FINAL_CUSTOM_LOCATION} = $this->getOptionalField($json, self::SOURCE_CUSTOM_LOCATION);
        $component->{self::FINAL_TYPE} = $this->getOptionalField($json, self::SOURCE_LIGHTCTRL_TYPE, null);
        $component->{self::FINAL_FIRMWARE_MAX_VERSION} = $this->getOptionalField($json, self::SOURCE_LIGHTCTRL_FIRMWARE_MAX_VERSION, null);
        $component->{self::FINAL_FIRMWARE_MIN_VERSION} = $this->getOptionalField($json, self::SOURCE_LIGHTCTRL_FIRMWARE_MIN_VERSION, null);
        if (isset($json->{self::SOURCE_OUTPUT_GROUPS_LIST})){
            $outputGroupsList = array();
            foreach ($json->{self::SOURCE_OUTPUT_GROUPS_LIST} as $outputGroupInfo) {
                $outputGroup = new StdClass();
                if ($outputGroupInfo->{self::SOURCE_OUTPUT_GROUP_NUMBER} == 15) {
                    $outputGroup->{self::FINAL_OUTPUT_GROUP_NUMBER} = $outputGroupInfo->{self::SOURCE_OUTPUT_GROUP_NUMBER};
                    $outputGroup->{self::FINAL_DESCRIPTION} = $outputGroupInfo->{self::SOURCE_DESCRIPTION};
                } else {
                    $outputGroup->{self::FINAL_OUTPUT_GROUP_NUMBER} = $outputGroupInfo->{self::SOURCE_OUTPUT_GROUP_NUMBER   };
                    $outputGroup->{self::FINAL_OUTPUT_OFF_DELAY} = $outputGroupInfo->{self::SOURCE_OUTPUT_OFF_DELAY};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_FULL_LUX} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_FULL_LUX};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_SENSOR_TARGET} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_SENSOR_TARGET};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_PROPORTION} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_PROPORTION};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_MASTER_GROUP} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_MASTER_GROUP};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_MAX_LUX} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_MAX_LUX};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_MIN_LUX} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_MIN_LUX};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_OVERRIDE_OFF_LUX} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_OVERRIDE_OFF_LUX};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_OVERRIDE_OFF_DELAY} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_OVERRIDE_OFF_DELAY};
                    $outputGroup->{self::FINAL_OUTPUT_DAYLIGHT_OVERRIDE_OFF_CANCEL_LUX} = $outputGroupInfo->{self::SOURCE_OUTPUT_DAYLIGHT_OVERRIDE_OFF_CANCEL_LUX};
                }
                $outputGroupsList[] = $outputGroup;
            }
            $component->{self::FINAL_OUTPUT_GROUPS_LIST} = $outputGroupsList;
        }
        return $component;
    }

    /**
     * Reads the relevant information from a json relative to a Device and returns it as an Object
     * @param string $json
     * @return string
     */
    protected function minifyDevice($json) {
        $component = new StdClass();
        $component->{self::UNIQUE_ID_KEY} = $this->validateAndGetField($json, self::UNIQUE_ID_KEY);
        $component->{self::FINAL_COMPONENT_ID} = $this->validateAndGetField($json, self::SOURCE_DEVICE_ID);
        $component->{self::FINAL_DESCRIPTION} = $this->validateAndGetField($json, self::SOURCE_DEVICE_TYPE_DESCRIPTION);
        $component->{self::FINAL_CUSTOM_DESCRIPTION} = $this->getOptionalField($json, self::SOURCE_CUSTOM_DESCRIPTION);
        $component->{self::FINAL_CUSTOM_LOCATION} = $this->getOptionalField($json, self::SOURCE_CUSTOM_LOCATION);
        $component->{self::FINAL_TYPE} = $this->validateAndGetField($json, self::SOURCE_DEVICE_TYPE, null);
        $component->{self::FINAL_SENSITIVITY} = $this->getOptionalField($json, self::SOURCE_SENSITIVITY, null);
        $component->{self::FINAL_TIMEOUT} = $this->getOptionalField($json, self::SOURCE_TIMEOUT, null);
        $component->{self::FINAL_FIRMWARE_MAX_VERSION} = $this->getOptionalField($json, self::SOURCE_DEVICE_FIRMWARE_MAX_VERSION, null);
        $component->{self::FINAL_FIRMWARE_MIN_VERSION} = $this->getOptionalField($json, self::SOURCE_DEVICE_FIRMWARE_MIN_VERSION, null);
        $component->{self::FINAL_SCENES} = $this->getOptionalField($json, self::SOURCE_SCENES, null);
        $component->{self::FINAL_GROUPS} = $this->getOptionalField($json, self::SOURCE_GROUPS, null);
        $component->{self::FINAL_SCC} = $this->getOptionalField($json, self::SOURCE_SCC, null);
        $component->{self::FINAL_ENERGETIC_CLASS} = $this->getOptionalField($json, self::SOURCE_ENERGETIC_CLASS, null);
        return $component;
    }

} 
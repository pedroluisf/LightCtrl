<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 16-09-2014
 * Time: 22:38
 */

class JsonComponentsBase {

    const UNIQUE_ID_KEY = 'DrawId';

    // Constants given to us by the original JSON Config
    const SOURCE_ETHERNET_ID = 'EthernetId';
    const SOURCE_LIGHTCTRL_ID = 'ControllerId';
    const SOURCE_DEVICE_ID = 'ShortAddress';
    const SOURCE_LIGHTCTRL_LIST = 'LightCtlrList';
    const SOURCE_DEVICE_LIST = 'DevicesList';

    const SOURCE_LIGHTCTRL_TYPE = 'ControllerType';
    const SOURCE_LIGHTCTRL_FIRMWARE_MAX_VERSION = 'ControllerFirmwareVerMajor';
    const SOURCE_LIGHTCTRL_FIRMWARE_MIN_VERSION = 'ControllerFirmwareVerMinor';

    const SOURCE_DEVICE_TYPE = 'DevType';
    const SOURCE_DEVICE_TYPE_DESCRIPTION = 'TypeDescription';
    const SOURCE_DEVICE_FIRMWARE_MAX_VERSION = 'firmware_ver_major';
    const SOURCE_DEVICE_FIRMWARE_MIN_VERSION = 'firmware_ver_minor';

    const SOURCE_DESCRIPTION = 'Description';
    const SOURCE_CUSTOM_LOCATION = 'custom_location';
    const SOURCE_CUSTOM_DESCRIPTION = 'custom_description';

    const SOURCE_SENSITIVITY = 'sensitivity';
    const SOURCE_TIMEOUT = 'timeout';
    const SOURCE_SCENES = 'scenes';
    const SOURCE_GROUPS = 'Groups';
    const SOURCE_OUTPUT_GROUPS_LIST = 'OutputGroupList';
    const SOURCE_OUTPUT_GROUP_NUMBER = 'groupNo';
    const SOURCE_OUTPUT_OFF_DELAY = 'offDelay';
    const SOURCE_OUTPUT_DAYLIGHT_FULL_LUX = 'daylightFullLux';
    const SOURCE_OUTPUT_DAYLIGHT_SENSOR_TARGET = 'daylightSensorTarget';
    const SOURCE_OUTPUT_DAYLIGHT_PROPORTION = 'daylightProportion';
    const SOURCE_OUTPUT_DAYLIGHT_MASTER_GROUP = 'daylightMasterGroup';
    const SOURCE_OUTPUT_DAYLIGHT_MAX_LUX = 'daylightMaxLux';
    const SOURCE_OUTPUT_DAYLIGHT_MIN_LUX = 'daylightMinLux';
    const SOURCE_OUTPUT_DAYLIGHT_OVERRIDE_OFF_LUX = 'daylightOverrideOffLux';
    const SOURCE_OUTPUT_DAYLIGHT_OVERRIDE_OFF_DELAY = 'daylightOverrideOffDelay';
    const SOURCE_OUTPUT_DAYLIGHT_OVERRIDE_OFF_CANCEL_LUX = 'daylightOverrideOffCancelLux';
    const SOURCE_SCC = 'scc';
    const SOURCE_ENERGETIC_CLASS = 'EnergeticClass';

    // Constants used by us on the saved JSON Config
    const FINAL_COMPONENT_ID = 'id';
    const FINAL_LIGHTCTRL_LIST = 'lcLs';
    const FINAL_DEVICE_LIST = 'dvls';
    const FINAL_TYPE = 'type';
    const FINAL_DESCRIPTION = 'desc';
    const FINAL_FIRMWARE_MIN_VERSION = 'vmin';
    const FINAL_FIRMWARE_MAX_VERSION = 'vmax';
    const FINAL_CUSTOM_DESCRIPTION = 'cdsc';
    const FINAL_CUSTOM_LOCATION = 'clcn';
    const FINAL_SENSITIVITY = 'sens';
    const FINAL_TIMEOUT = 'time';
    const FINAL_SCENES = 'scns';
    const FINAL_GROUPS = 'grps';
    const FINAL_OUTPUT_GROUPS_LIST = 'ogls';
    const FINAL_OUTPUT_GROUP_NUMBER = 'grpn';
    const FINAL_OUTPUT_OFF_DELAY = 'oodl';
    const FINAL_OUTPUT_DAYLIGHT_FULL_LUX = 'odfl';
    const FINAL_OUTPUT_DAYLIGHT_SENSOR_TARGET = 'odst';
    const FINAL_OUTPUT_DAYLIGHT_PROPORTION = 'odpr';
    const FINAL_OUTPUT_DAYLIGHT_MASTER_GROUP = 'odmg';
    const FINAL_OUTPUT_DAYLIGHT_MAX_LUX = 'odmxl';
    const FINAL_OUTPUT_DAYLIGHT_MIN_LUX = 'odmnl';
    const FINAL_OUTPUT_DAYLIGHT_OVERRIDE_OFF_LUX = 'odol';
    const FINAL_OUTPUT_DAYLIGHT_OVERRIDE_OFF_DELAY = 'odod';
    const FINAL_OUTPUT_DAYLIGHT_OVERRIDE_OFF_CANCEL_LUX = 'odoc';
    const FINAL_SCC = 'scc';
    const FINAL_ENERGETIC_CLASS = 'ec';

    protected $id_ethernet;
    protected $ethernet_config;

    protected static $ethernetConfigCache = array();

    /**
     * @param int $id_ethernet
     */
    public function __construct($id_ethernet) {

        $modelEthernet = new Ethernet();

        $this->id_ethernet = $id_ethernet;

        // Try to get it from cache first
        if (!isset(self::$ethernetConfigCache[$id_ethernet])) {
            $ethernet = $modelEthernet->findByPk($this->id_ethernet);
            if (!isset($ethernet)) { return; }

            // Cache it
            self::$ethernetConfigCache[$id_ethernet] = json_decode($ethernet->config);
        }

        $this->ethernet_config = self::$ethernetConfigCache[$id_ethernet];

    }

    /**
     * Clears any cached value stored
     */
    public static function clearCache() {
        self::$ethernetConfigCache = array();
    }

    /**
     * Gets all the json info
     * @return string
     */
    public function getJsonComponents(){
        return $this->ethernet_config;
    }

    /**
     * Validates if a mandatory field exists and read it's value
     * @param $node - the node to read value from
     * @param $field - The field to read
     * @return string - The value read or throws an exception
     * @throws FileParsingException
     */
    public function validateAndGetField($node, $field) {
        if (!isset($node->$field)){
            throw new FileParsingException("Json file Error: $field is not Set!");
        }
        return $node->$field;
    }

    /**
     * Gets an optional value from a node if exists. If not exists returns ''
     * @param $node - the node to read value from
     * @param $field - The field to read
     * @param $defaultValue - The default value
     * @return string - The value read or default
     */
    public function getOptionalField($node, $field, $defaultValue = '') {
        if (isset($node->$field)){
            return $node->$field;
        }
        return $defaultValue;
    }

    /**
     * Determines if a given component is a virtual switch
     *
     * @param $lightCtrl
     * @param $device
     * @return bool
     */
    public function isVirtualSwitch($lightCtrl, $device)
    {
        $LightCtrlIdField = isset($lightCtrl->{self::SOURCE_LIGHTCTRL_ID}) ? self::SOURCE_LIGHTCTRL_ID : self::FINAL_COMPONENT_ID;
        $DeviceIdField = isset($device->{self::SOURCE_DEVICE_ID}) ? self::SOURCE_DEVICE_ID : self::FINAL_COMPONENT_ID;
        return $lightCtrl->$LightCtrlIdField == '63' && $device->$DeviceIdField > 0;
    }

    /**
     * Analyses the devices to see if it is a vlid one or not
     * @param $device
     * @return bool
     * @throws FileParsingException
     */
    public function isActiveDevice($device) {
        // Draw_id = -1 are not valid
        $draw_id = strval($this->validateAndGetField($device, self::UNIQUE_ID_KEY));
        if ($draw_id == '-1') {
            return false;
        }

        // Devices that do not belong to any group are not valid
        if (isset($device->{self::FINAL_GROUPS})) {
            if (!in_array(1, $device->{self::FINAL_GROUPS})) {
                return false;
            }
        }

        // All else is Active
        return true;
    }

}
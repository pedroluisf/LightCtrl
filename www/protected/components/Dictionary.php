<?php

class Dictionary {

    const VIRTUAL_LIGHT_CONTROLLER = 63;
    const INACTIVE_VIRTUAL_CLEAR_CONTACT_INPUT = "6300";

    protected  static $lightControllerTypeDescription = array(
        '1' => 'DALI only',
        '3' => 'TCAN only',
        '4' => 'DALI and TCAN combined',
        '9' => 'DALI only with message repeat',
        '11' => 'TCAN only with message repeat',
        '12' => 'DALI and TCAN combined with message repeat'
    );

    protected  static $attributeDescription = array(
        'draw_id' => 'Draw ID',
        'ethernet_id' => 'Floor ID',
        'ethernet_status' => 'Floor CAN Status',
        'lc_id' => 'Light Ctrl ID',
        'dvc_id' => 'Device ID',
        'dev_type' => 'Device Type ID',
        'type' => 'Light Ctrl Type',
        'type_description' => 'Light Ctrl Description',
        'description' => 'Description',
        'groups' => 'Groups',
        'scenes' => 'Scenes',
        'sensitivity' => 'Sensitivity',
        'timeout' => 'Timeout',
        'firmware_version' => 'Firmware Version',
        'custom_location' => 'Custom Location',
        'custom_description' => 'Custom Description',
        'target_lux' => 'Target Lux',
        'energetic_class' => 'Energetic Class',
    );

    protected static $typeToCssClass = array(
        'eth' => 'icon-eth-int',
        'ctr' => 'icon-lgt-ctr',
        '0'   => 'icon-flr-lamp',
        '1'   => 'icon-emg-lamp',
        '2'   => 'icon-dcg-lamp',
        '3'   => 'icon-lvh-lamp',
        '4'   => 'icon-inc-lamp',
        '5'   => 'icon-1-10v-out',
        '6'   => 'icon-led-mod',
        '7'   => 'icon-rsw-mod',
        '112' => 'icon-tcan-jb',
        '113' => 'icon-emg-tst-rly',
        '128' => 'icon-nkn-dev',
        '129' => 'icon-scn-plt',
        '130' => 'icon-cci',
        '131' => 'icon-pir-det',
        '132' => 'icon-dlt-det',
        'scc' => 'icon-scc'
    );

    protected static $lampTypes = array(
        '0'   => 'Fluorescent Lamp',
        '1'   => 'Emergency Lamp',
        '2'   => 'Discharge Lamp',
        '3'   => 'Low Voltage Halogen Lamp',
        '4'   => 'Incandescent Lamp',
        '5'   => '1-10V Output',
        '6'   => 'LED Module',
        '7'   => 'Relay Switch Module',
        '112' => 'TCAN JB Luminaire',
        '113' => 'Emergency Test Relay'
    );

    protected static $daliToCssClass = array(
        '0' => 'icon-off',
        '1' => 'icon-fade-up',
        '2' => 'icon-fade-down',
        '3' => 'icon-step-up',
        '4' => 'icon-step-down',
        '5' => 'icon-max-lux',
        '6' => 'icon-min-lux',
        '16' => 'icon-scene',
        '254' => 'icon-clear',
        '255' => 'icon-lux-ctrl'
    );

    protected static $daliMessagesForType = array(
        'eth' => array(),
        'ctr' => array(),
        '0'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254', '255'),
        '1'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '2'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '3'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '4'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '5'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '6'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '7'   => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '112' => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '113' => array('0', '1', '2', '3', '4', '5', '6', '16', '254',  '255'),
        '128' => array(),
        '129' => array(),
        '130' => array(),
        '131' => array(),
        '132' => array(),
    );

    protected static $daliMessageCodesList = array(
        '0' => 'Switch lamp off',
        '1' => 'Fade Up',
        '2' => 'Fade Down',
        '3' => 'Step Up',
        '4' => 'Step Down',
        '5' => 'Select Maximum lux',
        '6' => 'Select Minimum lux',
        '16' => 'Select Scene',
        '255' => 'Direct lux control',
//        '-1' => 'Spacer',
//        '254' => 'Clear Overrides'
    );

    protected static $daliMessageParameters = array (
        '16' => array(
            'command' => 'scene',
            'values' => array(
                '0'=>'scene 1',
                '1'=>'scene 2',
                '2'=>'scene 3',
                '3'=>'scene 4',
                '4'=>'scene 5',
                '5'=>'scene 6',
                '6'=>'scene 7',
                '7'=>'scene 8',
                '8'=>'scene 9',
                '9'=>'scene 10',
                '10'=>'scene 11',
                '11'=>'scene 12',
                '12'=>'scene 13',
                '13'=>'scene 14',
                '14'=>'scene 15',
                '15'=>'scene 16'
            )
        ),
        '255' => array(
            'command' => 'lux',
            'min' => '0',
            'max' => '255',
            'messages' => array(
                '0' => 'Off',
                '255' => 'No change'
            )
        ),
    );

    protected static $daliStatusConfirmationCommand = array (
        '0' => '160',   // Switch lamp off
        '1' => '160',   // Fade Up
        '2' => '160',   // Fade Down
        '3' => '160',   // Step Up
        '4' => '160',   // Step Down
        '5' => '160',   // Select Maximum lux
        '6' => '160',   // Select Minimum lux
        '16' => '144',  // Select Scene
        '254' => '160',  // Clear overrides
        '255' => '160'  // Direct lux control
    );

    protected static $daliRequiresResponse = array (
        '0' => 'N',   // Switch lamp off
        '1' => 'N',   // Fade Up
        '2' => 'N',   // Fade Down
        '3' => 'N',   // Step Up
        '4' => 'N',   // Step Down
        '5' => 'N',   // Select Maximum lux
        '6' => 'N',   // Select Minimum lux
        '16' => 'N',  // Select Scene
        '144' => 'Y',  // Query Status
        '160' => 'Y',  // Query Lux Level
        '254' => 'N',  // Clear Overrides
        '255' => 'N'  // Direct lux control
    );

    public static function translateControllerTypeToDescription($type) {

        if (isset(Dictionary::$lightControllerTypeDescription[$type])) {
            return Dictionary::$lightControllerTypeDescription[$type];
        }
        return '';
    }

    public static function translateAttributeDescription($attribute) {

        if (isset(Dictionary::$attributeDescription[$attribute])) {
            return Dictionary::$attributeDescription[$attribute];
        }
        return '';
    }

    public static function translateTypeToCssClass($type) {

        if (isset(Dictionary::$typeToCssClass[$type])) {
            return Dictionary::$typeToCssClass[$type];
        }
        return '';
    }

    public static function translateLampType($type) {

        if (isset(Dictionary::$lampTypes[$type])) {
            return Dictionary::$lampTypes[$type];
        }
        return '';
    }

    public static function translateDaliMessageToCssClass($type) {

        if (isset(Dictionary::$daliToCssClass[$type])) {
            return Dictionary::$daliToCssClass[$type];
        }
        return '';
    }

    public static function getDaliMessagesForType($type = null) {
        if ($type === null) {
            return Dictionary::$daliMessagesForType;
        } elseif (isset(Dictionary::$daliMessagesForType[$type])) {
            return Dictionary::$daliMessagesForType[$type];
        }
        return '';
    }

    public static function getDaliMessageStatusChangeConfirmationCommandByCode($code) {

        if (isset(Dictionary::$daliStatusConfirmationCommand[$code])) {
            return Dictionary::$daliStatusConfirmationCommand[$code];
        }
        return null;
    }

    public static function getDaliMessageCodesList() {

        return Dictionary::$daliMessageCodesList;
    }

    public static function getLampTypes() {

        return Dictionary::$lampTypes;
    }

    public static function getDaliMessageParamsByCode($code) {

        if (isset(Dictionary::$daliMessageParameters[$code])) {
            return Dictionary::$daliMessageParameters[$code];
        }
        return null;
    }

    public static function getDaliRequiresResponseByCode($code) {

        if (isset(Dictionary::$daliRequiresResponse[$code])) {
            return Dictionary::$daliRequiresResponse[$code];
        }
        return 'N';
    }

} 
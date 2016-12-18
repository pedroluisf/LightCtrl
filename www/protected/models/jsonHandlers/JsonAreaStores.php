<?php
/**
 * Created by PhpStorm.
 * User: PedroLF
 * Date: 14-03-2014
 * Time: 0:03
 */

class JsonAreaStores {

    const UNIQUE_ID_KEY = 'DrawId';

    protected $id_area;
    protected $tree;
    protected $properties;
    /* @var JsonComponentsParser $componentParser */
    protected $componentParser;

    /**
     * @param int $id_area
     */
    public function __construct($id_area) {

        $this->id_area = $id_area;
        $this->tree = array();
        $this->properties = array();

        if (is_null($id_area)) {
            return;
        }

        $this->execute();
    }

    /**
     * Get's the parsed json Tree
     * @return array
     */
    public function getTree(){
        return $this->tree;
    }

    /**
     * Get's the parsed json Properties
     * @return array
     */
    public function getProperties(){
        return $this->properties;
    }

    /**
     * Executes the parsing of all the ethernet json for this Area
     */
    protected function execute(){
        $modelEthernet = new Ethernet();
        $ethernetList = $modelEthernet->findAllByAttributes(array('fk_area' => $this->id_area, 'inactive'=>0));
        if (empty($ethernetList)) {
            return;
        }

        foreach ($ethernetList as $ethernet) {
            /* @var Ethernet $ethernet */
            $this->componentParser = new JsonComponentsParser($ethernet->id_ethernet);
            $node = $this->parseJson(json_decode($ethernet->config));
            if ($node) {
                $this->tree[] = $node;
            }
        }

    }

    /**
     * Parses one json
     * @param $json - The json to parse
     * @return array
     */
    protected function parseJson($json){
        // Level 1 - CAN Ethernet
        $treeNode = $this->parseLevel1Info($json);
        $children = array();

        foreach ($json->{JsonComponentsBase::FINAL_LIGHTCTRL_LIST} as $lightCtrl) {
            // Level 2 - Light Controllers
            $child = $this->parseLevel2Info($lightCtrl);
            if (empty($child)){
                continue;
            }
            $grandchildren = array();
            foreach ($lightCtrl->{JsonComponentsBase::FINAL_DEVICE_LIST} as $device) {
                // Level 3 - Devices
                if ($this->componentParser->isVirtualSwitch($lightCtrl, $device) && isset($device->scc)) {
                    $virtualGrandchildren = array();
                    foreach ($device->{JsonComponentsBase::FINAL_SCC} as $key => $virtualSwitch) {
                        $virtualGrandchildren[] = $this->parseVirtualSwitch($key + 1, $virtualSwitch);
                    }
                    $grandchildren[] = array(
                        'text' => strtoupper(JsonComponentsBase::FINAL_SCC) . ' (' . $device->id . ')',
                        'class' => Dictionary::translateTypeToCssClass(JsonComponentsBase::FINAL_SCC),
                        'children' => array_filter($virtualGrandchildren)
                    );
                } else {
                    $grandchildren[] = $this->parseLevel3Info($device);
                }
            }
            $child['children'] = array_values(array_filter($grandchildren));
            $children[] = $child;
        }
        $treeNode['children'] = $children;
        return $treeNode;
    }

    /**
     * Parses the info relative to the first level of json file (CAN Ethernet Adapter)
     * @param $json - the json with the info to extract from
     * @return array
     */
    protected function parseLevel1Info($json){
        if (!$this->componentParser->isActiveDevice($json)) {
            return;
        }

        $draw_id = ''.$json->{self::UNIQUE_ID_KEY};

        /* @var EthernetTransfer $component */
        $component = $this->componentParser->getDeviceByDrawId($draw_id);

        // Tree information
        $treeNode = array();
        $treeNode['data-id'] = $component->ethernet_id;
        $treeNode['data-drawing_id'] = $draw_id;
        $treeNode['text'] = $component->description . ' (' . $draw_id . ')';
        $treeNode['class'] = Dictionary::translateTypeToCssClass('eth');
        $treeNode['data-type'] = 'eth';

        // Properties information
        $this->properties[$draw_id] = array(
            Dictionary::translateAttributeDescription('ethernet_id') => $component->ethernet_id,
            Dictionary::translateAttributeDescription('description') => $component->description
        );

        return $treeNode;

    }

    /**
     * Parses the info relative to the second level of json file (Light Controllers)
     * @param $json - the json with the info to extract from
     * @return array
     */
    protected function parseLevel2Info($json){
        if (!$this->componentParser->isActiveDevice($json)) {
            return;
        }

        $draw_id = ''.$json->{self::UNIQUE_ID_KEY};

        /* @var LightctrTransfer $component */
        $component = $this->componentParser->getDeviceByDrawId($draw_id);

        // Tree information
        $lightCtrl = array();
        $lightCtrl['data-id'] = $component->lc_id;
        $lightCtrl['data-drawing_id'] = $draw_id;
        $lightCtrl['text'] = $component->description . ' (' . $draw_id . ')';
        $lightCtrl['class'] = Dictionary::translateTypeToCssClass('ctr');
        $lightCtrl['data-type'] = 'ctr';

        // Properties information
        $this->properties[$draw_id] = array(
            Dictionary::translateAttributeDescription('lc_id') => $component->lc_id,
            Dictionary::translateAttributeDescription('type_description') => $component->type_description,
            Dictionary::translateAttributeDescription('custom_location') => $component->custom_location,
            Dictionary::translateAttributeDescription('custom_description') => $component->custom_description,
            Dictionary::translateAttributeDescription('firmware_version') => $component->firmware_version
        );

        return $lightCtrl;
    }

    /**
     * Parses the info relative to the third level of json file (Devices)
     * @param $json - the json with the info to extract from
     * @return array
     */
    protected function parseLevel3Info($json){
        if (!$this->componentParser->isActiveDevice($json)) {
            return;
        }

        $draw_id = ''.$json->{self::UNIQUE_ID_KEY};

        /* @var DeviceTransfer $component */
        $component = $this->componentParser->getDeviceByDrawId($draw_id);

        // Tree information
        $device = array();
        $device['data-id'] = $component->dvc_id;
        $device['data-drawing_id'] = $draw_id;
        $device['text'] = $component->description . ' (' . $draw_id . ')';
        $device['class'] = Dictionary::translateTypeToCssClass($component->dev_type);
        $device["data-type"] = $component->dev_type;

        // Properties information
        $this->properties[$draw_id] = array(
            Dictionary::translateAttributeDescription('lc_id') => $component->lc_id,
            Dictionary::translateAttributeDescription('dvc_id') => $component->dvc_id,
            Dictionary::translateAttributeDescription('description') => $component->description,
            Dictionary::translateAttributeDescription('custom_location') => $component->custom_location,
            Dictionary::translateAttributeDescription('custom_description') => $component->custom_description,
            Dictionary::translateAttributeDescription('groups') => $component->groups,
        );

        // Timeout is optional within devices
        if ($component->timeout){
            $this->properties[$draw_id]['Time Out'] = $component->timeout;
        }

        // Firmware is optional within devices
        if ($component->firmware_version){
            $this->properties[$draw_id]['Firmware Version'] = $component->firmware_version;
        }

        // Groups are optional within devices
        if (isset($component->groups)){
            $this->properties[$draw_id]['Groups'] = $component->groups;
        }

        return $device;
    }

    protected function parseVirtualSwitch($swNum, $json)
    {
        if (!$this->componentParser->isActiveDevice($json)) {
            return;
        }

        $draw_id = ''.$this->componentParser->validateAndGetField($json, self::UNIQUE_ID_KEY);

        /* @var DeviceTransfer $component */
        $component = $this->componentParser->getDeviceByDrawId($draw_id);

        // Tree information
        $device = array();
        $device['data-id'] = $component->dvc_id;
        $device['data-drawing_id'] = $draw_id;
        $device['text'] = $component->description . ' (' . $swNum . ')';
        $device['class'] = Dictionary::translateTypeToCssClass($component->dev_type);
        $device["data-type"] = $component->dev_type;
        $device["data-sw_num"] = $swNum;

        return $device;
    }
}
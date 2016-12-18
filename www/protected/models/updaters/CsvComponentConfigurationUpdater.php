<?php
/**
 * Created by PhpStorm.
 * User: Luiixx
 * Date: 29-10-2014
 * Time: 22:34
 */

class CsvComponentConfigurationUpdater extends CsvConfigurationUpdater implements IComponentConfigurationUpdater {

    protected $ethernetId;

    /** @var Ethernet $ethernetModel */
    protected $ethernetModel;

    protected static $allowedHeaders = array(
        array (
            JsonComponentsBase::SOURCE_LIGHTCTRL_ID,
            JsonComponentsBase::SOURCE_DEVICE_ID,
            JsonComponentsBase::UNIQUE_ID_KEY,
            JsonComponentsBase::SOURCE_ENERGETIC_CLASS
        ),
        array (
            JsonComponentsBase::SOURCE_LIGHTCTRL_ID,
            JsonComponentsBase::SOURCE_DEVICE_ID,
            JsonComponentsBase::UNIQUE_ID_KEY
        )
    );

    /**
     * These are the fields we allow update.
     * @var array $fields2Update
     */
    protected $fields2Update = array(
        JsonComponentsBase::UNIQUE_ID_KEY,
        JsonComponentsBase::SOURCE_ENERGETIC_CLASS
    );

    /**
     * These are the fields we allow update.
     * @var array $fields2Update
     */
    protected $mapperToTransfer = array(
        JsonComponentsBase::UNIQUE_ID_KEY,
        JsonComponentsBase::SOURCE_ENERGETIC_CLASS
    );

    public function setEthernetId($ethernetId) {
        return $this->ethernetId = $ethernetId;
    }

    /**
     * @param string $filePath
     * @return array|void
     * @throws Exception
     */
    public function processFile($filePath)
    {
        $this->readFile($filePath);

        // Instanciate the Ethernet Model based on the given ID
        $this->ethernetModel = Ethernet::model()->findByPk($this->ethernetId);

        foreach($this->data as $data) {
            $this->updateComponent($data);
        }

        // Save model after getting the updated info
        $this->ethernetModel->altered_config = true;
        if (!$this->ethernetModel->save()) {
            throw new Exception ('Failed Saving Updated Config on Caneth');
        }
    }

    /**
     * Saves the info for the component based on the received data
     * @param $ethernetId
     * @param $data
     */
    protected function updateComponent($data)
    {
        $componentTag = '(CAN ID:'.$this->ethernetId.' Light Ctrl ID:'.$data['ControllerId'].' Device ID:'.$data['ShortAddress'].')';

        $jsonParser = new JsonComponentsParser($this->ethernetId);
        $jsonUpdater = new JsonComponentsUpdater($this->ethernetId);

        // Getting component
        try {
            $component = $jsonParser->getDeviceByLcIdAndDvcId($data['ControllerId'], $data['ShortAddress'], true);
            if (!isset($component)) {
                $this->errors[] = 'Device not found '.$componentTag;
                return;
            }
        } catch (Exception $e) {
            $this->errors[] = 'Exception found while retrieving device "'.$componentTag.'": '.$e->getMessage();
            return;
        }

        // Set allowed Values
        foreach ($data as $attrKey => $attrValue) {
            if (in_array($attrKey, $this->fields2Update)) {
                // Energetic Class needs validation
                if ($attrKey == JsonComponentsBase::SOURCE_ENERGETIC_CLASS){
                    if (empty($attrValue)) {
                        $this->errors[] = "Empty wattage class supplied for device \"$componentTag\"";
                        continue;
                    } elseif (!EnergeticClass::getClassByKey($attrValue)){
                        $this->errors[] = "The following wattage class was not found: \"$attrValue\" for device \"$componentTag\"";
                        continue;
                    }
                }
                $component[$attrKey] = $attrValue;
            }
        }

        // Update Component on jsonUpdater config (It will auto update its cache)
        try {
            $jsonUpdater->updateDevice($component, $this->fields2Update);
        } catch (Exception $e) {
            $this->errors[] = 'Exception found while Updating device '.$componentTag.' '.$e->getMessage();
        }

        // Set the latest version on model
        $this->ethernetModel->config = json_encode($jsonUpdater->getJsonComponents());

    }
}
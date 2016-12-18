<?php
/**
 * Created by PhpStorm.
 * User: Luiixx
 * Date: 29-10-2014
 * Time: 22:34
 */

class CsvEnergeticClassConfigurationUpdater extends CsvConfigurationUpdater implements IEnergeticClassConfigurationUpdater {

    protected $fullImport;

    const HEADER_CLASS_KEY = 'Key';
    const HEADER_CLASS_DESCRIPTION = 'Description';
    const HEADER_CLASS_CONSUMPTION_WATTS = 'ConsumptionWatts';

    protected static $allowedHeaders = array(
        self::HEADER_CLASS_KEY,
        self::HEADER_CLASS_DESCRIPTION,
        self::HEADER_CLASS_CONSUMPTION_WATTS
    );

    public function isFullImport($fullImport) {
        $this->fullImport = $fullImport;
    }

    /**
     * @param string $filePath
     * @return array|void
     * @throws Exception
     */
    public function processFile($filePath)
    {
        $this->readFile($filePath);

        if ($this->fullImport) {
            EnergeticClass::model()->deleteAll();
        }

        foreach($this->data as $data) {
            $this->saveEnergeticClass($data);
        }

    }

    protected function saveEnergeticClass($data)
    {
        try {

            $energeticClassModel = new EnergeticClass('create');
            $energeticClassModel->class_key = $data[self::HEADER_CLASS_KEY];
            $energeticClassModel->description = $data[self::HEADER_CLASS_DESCRIPTION];
            $energeticClassModel->consumption_watts = $data[self::HEADER_CLASS_CONSUMPTION_WATTS];
            $energeticClassModel->save();

        } catch (Exception $e) {
            $duplicateEntryPattern = '/Duplicate entry (.*) for key \'class_key\'/';
            if (preg_match($duplicateEntryPattern, $e->getMessage())) {
                $msg = 'Duplicate entry for class key "'.$data[self::HEADER_CLASS_KEY].'"';
            } else {
                $msg = 'Exception found while saving the following line "'.
                    implode(',', $data).
                    '". Please see file /protected/runtime/main.log for more details';
                // Log Error and Stack trace
                $logMsg = $e->getMessage().PHP_EOL;
                $logMsg .= 'Stack Trace:'.PHP_EOL;
                $logMsg .= $e->getTraceAsString().PHP_EOL.'---';
                Yii::log($logMsg, CLogger::LEVEL_ERROR);
            }
            $this->errors[] = $msg;
            return;
        }

    }
}
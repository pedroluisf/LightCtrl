<?php

/**
 * This is the model class for table "tbl_status".
 *
 * The followings are the available columns in table 'tbl_status':
 * @property string $fk_ethernet
 * @property integer $lc_id
 * @property integer $dvc_id
 * @property string $type
 * @property integer $fk_description
 * @property string $status_hex
 * @property string $lamp_status
 * @property integer $lux_level
 * @property string $emergency_mode
 * @property string $battery_status
 * @property string $input_status
 * @property integer $current_scene
 * @property boolean $alternate_scene
 * @property boolean $switch_input_1
 * @property boolean $switch_input_2
 * @property boolean $switch_input_3
 * @property boolean $switch_input_4
 * @property boolean $switch_input_5
 * @property boolean $switch_input_6
 * @property boolean $switch_input_7
 * @property boolean $switch_input_8
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property Ethernet $ethernet
 * @property Description $description
 */
class Status extends EActiveRecord
{
    public $status_display;

    protected $statusFunctionByDeviceType = array(
        '0' => 'getLampStatus', // Fluorescent Lamp
        '1' => 'getEmergencyLampStatus', // Emergency Lamp
        '2' => 'getLampStatus', // Discharge Lamp
        '3' => 'getLampStatus', // Low Voltage Halogen Lamp
        '4' => 'getLampStatus', // Incandescent Lamp
        '5' => 'getLampStatus', // 1-10V Output
        '6' => 'getLampStatus', // LED Module
        '7' => 'getSpecialLampStatus', // Relay Switch Module
        '112' => 'getSpecialLampStatus', // TCAN JB Luminaire
        '113' => 'getSpecialLampStatus', // Emergency Test Relay
        //'128' => '', // Unkown Device
        '129' => 'getScenePlateStatus', // Scene Plates
        '130' => 'getClearContactInputStatus', // Clean Contact Inputs
        '131' => 'getPIRStatus', // PIR Presence Detectors
        '132' => 'getDaylightDetectorStatus' // Daylight Detectors
    );

    protected static $relevantAttributes = array(
        'lamp_status',
        'lux_level',
        'emergency_mode',
        'battery_status',
        'input_status',
        'current_scene',
        'alternate_scene',
        'switch_input_1',
        'switch_input_2',
        'switch_input_3',
        'switch_input_4',
        'switch_input_5',
        'switch_input_6',
        'switch_input_7',
        'switch_input_8'
    );

    protected static $myLabels = array(
            'fk_ethernet' => 'Floor',
            'ethernet_name' => 'Floor',
            'lc_id' => 'Light Ctrl',
            'dvc_id' => 'Device',
            'type' => 'Dev Type',
            'fk_description' => 'Dev Type',
            'type_description' => 'Dev Type',
            'status_hex' => 'Status Hex',
            'status_display' => 'Status',
            'status_string' => 'Status',
			'lamp_status' => 'Lamp Status',
			'lux_level' => 'Lux Level',
			'emergency_mode' => 'Emergency Mode',
			'battery_status' => 'Battery Status',
			'input_status' => 'Input Status',
			'current_scene' => 'Current Scene',
			'alternate_scene' => 'Alternate Scene',
			'switch_input_1' => 'Switch Input 1',
			'switch_input_2' => 'Switch Input 2',
			'switch_input_3' => 'Switch Input 3',
			'switch_input_4' => 'Switch Input 4',
			'switch_input_5' => 'Switch Input 5',
			'switch_input_6' => 'Switch Input 6',
			'switch_input_7' => 'Switch Input 7',
			'switch_input_8' => 'Switch Input 8',
			'created_at' => 'Reported At',
		);

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_status';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fk_ethernet, lc_id, dvc_id, type, status_hex', 'required'),
			array('fk_ethernet, lc_id, dvc_id, fk_description, lux_level, current_scene', 'numerical', 'integerOnly'=>true),
            array('alternate_scene, switch_input_1, switch_input_2, switch_input_3, switch_input_4, switch_input_5, switch_input_6, switch_input_7, switch_input_8', 'boolean'),
			array('fk_ethernet', 'length', 'max'=>20),
			array('status_hex, battery_status', 'length', 'max'=>8),
			array('lamp_status', 'length', 'max'=>7),
			array('emergency_mode', 'length', 'max'=>25),
			array('input_status', 'length', 'max'=>13),
			// The following rule is used by search().
			array('dvc_id, fk_ethernet, fk_description, created_at, status_display', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'ethernet' => array(self::BELONGS_TO, 'Ethernet', 'fk_ethernet'),
            'description' => array(self::BELONGS_TO, 'Description', 'fk_description'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
        return self::$myLabels;
	}

    public function getReportFields(){
        return array(
            'ethernet_name',
            'lc_id',
            'dvc_id',
            'type_description',
            'status_string',
            'created_at'
        );
    }

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search($pagination=true)
	{
		$criteria=new CDbCriteria;
        $criteria->with = array( 'ethernet' , 'description' );

        $criteria->compare('fk_ethernet', $this->fk_ethernet);
        $criteria->compare('lc_id',$this->lc_id);
		$criteria->compare('dvc_id',$this->dvc_id);
        $criteria->compare('fk_description',$this->fk_description);
		$criteria->compare('created_at',$this->created_at,true);
        $criteria->compare(
            "CONCAT(
                CASE WHEN lamp_status IS NOT NULL
                    THEN CONCAT('lamp_', lamp_status)
                    ELSE ''
                END,'__',
                CASE WHEN input_status IS NOT NULL
                    THEN CONCAT('input_', REPLACE(input_status, ' ' , '_'))
                    ELSE ''
                END, '__'
            )",
            $this->status_display, true);

        $config = array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'created_at DESC',
                'multiSort'=>true,
            )
        );
        if ($pagination){
            $config['pagination'] = array(
                'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']),
            );
        } else {
            $config['pagination'] = false;
        }

		return new CActiveDataProvider($this, $config);
	}

    /**
    * PHP getter magic method.
    * This method is overridden so that AR attributes can be accessed like properties.
    * @param string $name property name
    * @return mixed property value
    * @see getAttribute
    */
    public function __get($name)
    {
        if ($name == 'ethernet_name'){
            return $this->ethernet->name;
        }elseif ($name == 'type_description') {
            return $this->description->description;
        }elseif ($name == 'status_string'){
            return $this->getReadableStatus();
        }else{
            return parent::__get($name);
        }
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Status the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Saving method override, because we should only save if the status conditions have somehow vary through time
     * @param bool $runValidation
     * @param null $attributes
     * @return bool
     */
    public function save($runValidation=true,$attributes=null) {
        throw new Exception('Method save() is deprecated in this model. Use saveDeviceStatus() instead');
    }

    public function saveDeviceStatus($type, $status) {
        $this->status_hex = $status;
        $statusBlocks = str_split($status, 2);

        if  ($function = $this->statusFunctionByDeviceType[$type]){
            $mappedStatus = $this->$function($statusBlocks);
            foreach ($mappedStatus as $attr => $value) {
                $this->$attr = $value;
            }
        };

        if ($this->relevantDataChanged()){
            $this->created_at = date("Y-m-d H:i:s");
            /** @var CDbTransaction $transaction */
            $transaction = Yii::app()->db->beginTransaction();
            try {
                if (parent::save(true)) {
                    $this->saveFailureFlags(); // Must run after save command, because we need the trigger to save the info on StatusHist
                    $transaction->commit();
                } else {
                    $transaction->rollback();
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }

        return true;
    }

    /**
     * Iterate through fields to see if any change justifies a saving of a new record
     * @return bool
     */
    protected function relevantDataChanged() {
        if (!$oldRec = $this->findByPk(array('fk_ethernet'=>$this->fk_ethernet, 'lc_id'=>$this->lc_id, 'dvc_id'=>$this->dvc_id))){
            return true; // Nothing found, we must save
        }
        foreach (self::$relevantAttributes as $attr) {
            if ($oldRec->$attr != $this->$attr) {
                return true;
            }
        }
        return false;
    }

    /**
     * Every time a failure occurs, we have to set the new_failure flag. That will allow us to get in reports, when did that failure occurred
     * We only set the flag if is a new failure (no other flag found). If no failure happened, then we clear all flags.
     * Flags are set on the StatusHist
     */
    protected function saveFailureFlags() {
        if ($this->lamp_status == 'failure') {
            $flagSet = Yii::app()->db->createCommand(
                "SELECT COUNT(new_lamp_failed)
                FROM tbl_status_hist
                WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id AND new_lamp_failed=1"
            )->queryScalar();
            if (!$flagSet) {
                Yii::app()->db->createCommand(
                    "UPDATE tbl_status_hist
                    SET new_lamp_failed=1
                    WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id
                    AND id_status_hist=(
                      SELECT latest_id FROM (
                        SELECT MAX(id_status_hist) AS latest_id
                        FROM tbl_status_hist
                        WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id
                      ) AS T
                    )"
                )->execute();
            }
        } else {
            Yii::app()->db->createCommand(
                "UPDATE tbl_status_hist
                SET new_lamp_failed=0
                WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id "
            )->execute();
        }
    }

    /**
     * Clears all entries of devices that belong to a given Ethernet
     * @param $ethernet_id
     */
    public static function clearData($ethernet_id){
        self::model()->deleteAll('fk_ethernet=:fk_ethernet', array(':fk_ethernet'=>$ethernet_id));
    }

    /**
     * Gets all entries that belong to a given Area on a human readable format
     * @param $id_area
     * @return array (ethernet_id => array (dvc_id => status))
     */
    public static function getReadableStatusForAllDevicesByArea($id_area){
        $status = array(''=>''); // this first entry will assure that when encoding it will create a json object and not an array
        $ethernetModel = new Ethernet();
        $ethernetList = $ethernetModel->findAll('fk_area=:fk_area', array(':fk_area'=>$id_area));
        foreach ($ethernetList as $ethernet) {
            $status[$ethernet->id_ethernet] = self::getReadableStatusForAllDevicesByEthernet($ethernet->id_ethernet);
        }
        return $status;
    }

    /**
     * Gets all entries that belong to a given Ethernet on a human readable format
     * @param $ethernet_id
     * @return array (dvc_id => status)
     */
    public static function getReadableStatusForAllDevicesByEthernet($ethernet_id){
        $rows = self::model()->findAll('fk_ethernet=:fk_ethernet', array(':fk_ethernet'=>$ethernet_id));
        $status = array(''=>''); // this first entry will assure that when encoding it will create a json object and not an array
        $jsonParser = new JsonComponentsParser($ethernet_id);
        /* @var Status $row */
        foreach ($rows as $row) {
            /** @var DeviceTransfer $component */
            $component = $jsonParser->getDeviceByLcIdAndDvcId($row->lc_id, $row->dvc_id);
            $status[$component->draw_id] = self::mapStatus($row);
        }
        return $status;
    }

    /**
     * Gets the entries for a given Device on a human readable format
     * @param $fk_ethernet
     * @param $lc_id
     * @param $dvc_id
     * @return array
     */
    public static function getReadableStatusByPK($fk_ethernet, $lc_id, $dvc_id){
        if (!$row = self::model()->findByPk(array('fk_ethernet'=>$fk_ethernet, 'lc_id'=>$lc_id, 'dvc_id'=>$dvc_id))){
            return;
        }
        return self::mapStatus($row);
    }

    /**
     * Maps the Status on a human readable format
     * @param $row
     * @return string
     */
    protected static function mapStatus($row){
        $info = array();
        foreach(self::$relevantAttributes as $attr){
            if ($row->$attr !== null) {
                $info[] = self::$myLabels[$attr] . ': ' . $row->$attr;
            }
        }
        return implode(', ', $info);
    }

    /**
     * Maps the status of the current instance (Used for the Grid)
     * @return string
     */
    public function getReadableStatus(){
        return self::mapStatus($this);
    }

    /**
     * This will return all existing lamp types in the Status table, so we know what different lamps exist
     * @return array
     */
    public static function getExistingLampTypesInStatus(){
        $sql = '
            SELECT DISTINCT (type)
            FROM tbl_status
            WHERE type IN ('.implode(',', array_keys(Dictionary::getLampTypes())).')
        ';

        $LampTypes = Yii::app()->db->createCommand($sql)->queryColumn();

        // Use this method to return all type=>description
        return array_intersect_key(Dictionary::getLampTypes(), array_flip($LampTypes));
    }

    protected function getLampStatus($statusBlocks) {
        $lampStatusBits = str_split($this->hex2binReverted($statusBlocks[0]), 1);

        $lampStatus = ($lampStatusBits[1] == 1 ? 'failure' : ($lampStatusBits[2] == 1 ? 'on' : 'off'));
        return array(
            'lamp_status' => $lampStatus,
            'lux_level' => ($lampStatus == 'on' ? hexdec($statusBlocks[1]) : 0)
        );
    }

    protected function getSpecialLampStatus($statusBlocks) {
        // (Simon) Due to a bug on the firmware some devices return FF where the status bits should be
        if (strtoupper($statusBlocks[0]) == 'FF') {
            $luxLevel = hexdec($statusBlocks[1]);
            return array(
                'lamp_status' => ($luxLevel ? 'on' : 'off'),
                'lux_level' => $luxLevel
            );
        } else {
            return $this->getLampStatus($statusBlocks);
        }
    }

    protected function getEmergencyLampStatus($statusBlocks) {
        $emergencyModes = array(
            0 => 'reset mode',
            1 => 'normal operation',
            2 => 'emergency mode',
            3 => 'extended emergency mode',
            4 => 'function test in progress',
            5 => 'duration test in progress',
            6 => null,
            7 => null,
        );

        $lampStatusBits = str_split($this->hex2binReverted($statusBlocks[0]), 1);
        $emergencyBits = str_split($this->hex2binReverted($statusBlocks[2]), 1);
        $failureBits = str_split($this->hex2binReverted($statusBlocks[3]), 1);

        $emergencyModeValue = ((int)$emergencyBits[0]) + (((int)$emergencyBits[1]) * 2) + (((int)$emergencyBits[2]) * 4);

        // Lamp Status
        if ($lampStatusBits[1] == 1 || // Lamp Failure
            $failureBits[0] == 1 || // Circuit failure
            $failureBits[3] == 1 ) // Emergency Lamp Failure
        {
            $lampStatus = 'failure';
        } elseif ($lampStatusBits[2] == 1) {
            $lampStatus = 'on';
        } else {
            $lampStatus = 'off';
        }

        // Battery status
        if ($failureBits[2] == 1) {
            $batteryStatus = 'failure';
        } elseif ($emergencyBits[7] == 1 ) {
            $batteryStatus = 'charged';
        } else {
            $batteryStatus = 'charging';
        }

        return array(
            'lamp_status' => $lampStatus,
            'lux_level' => ($lampStatus == 'on' ? hexdec($statusBlocks[1]) : 0),
            'emergency_mode' => $emergencyModes[$emergencyModeValue],
            'battery_status' => $batteryStatus
        );
    }

    protected function getScenePlateStatus($statusBlocks) {
        $currentSceneBits = str_split($this->hex2binReverted($statusBlocks[1]), 1);

        foreach ($currentSceneBits as $key => $value) {
            if ($key < 7 && $value == '1') {
                $currentScene = (string) $key + 1;
                continue;
            }
        }

        return array(
            'current_scene' => $currentScene,
            'alternate_scene' => ($currentSceneBits[7] == 1)
        );
    }

    protected function getClearContactInputStatus($statusBlocks) {
        $switchBits = str_split($this->hex2binReverted($statusBlocks[1]), 1);
        $switchesStatus = array();
        foreach ($switchBits as $key => $value) {
            $switchesStatus['switch_input_' . ($key + 1)] = ($value == '1');
        }
        return $switchesStatus;
    }

    protected function getPIRStatus($statusBlocks) {
        $statusMap = array(
            0 => 'not detecting',
            1 => 'detecting'
        );

        return array(
            'input_status' => $statusMap[hexdec($statusBlocks[1])]
        );
    }

    protected function getDaylightDetectorStatus($statusBlocks) {
        $statusMap = range(0, 255);

        return array(
            'lux_level' => $statusMap[hexdec($statusBlocks[1])]
        );
    }

    protected function hex2bin($hexValue){
        return substr('00000000'.decbin(hexdec($hexValue)), -8);
    }

    protected function hex2binReverted($hexValue){
        return strrev(substr('00000000'.decbin(hexdec($hexValue)), -8));
    }
}

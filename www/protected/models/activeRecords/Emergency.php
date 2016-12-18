<?php

/**
 * This is the model class for table "tbl_emergency".
 *
 * The followings are the available columns in table 'tbl_emergency':
 * @property string $fk_ethernet
 * @property integer $lc_id
 * @property integer $dvc_id
 * @property string $fk_description
 * @property string $test_type
 * @property boolean $circuit_failure
 * @property boolean $battery_duration_failed
 * @property boolean $battery_failed
 * @property boolean $emergency_lamp_failed
 * @property boolean $function_test_overdue
 * @property boolean $duration_test_overdue
 * @property boolean $function_test_failed
 * @property boolean $duration_test_failed
 * @property string $created_at
 */
class Emergency extends EActiveRecord
{
    const TEST_TYPE_FUNCTION = 'func';
    const TEST_TYPE_DURATION = 'durat';

    public $deviceType;

    protected $emergencyTypes = array('1');

    protected $emergencyCommandBits = array(
        5=>self::TEST_TYPE_FUNCTION,
        6=>self::TEST_TYPE_DURATION
    );

    protected $emergencyTestsDescription = array(
        self::TEST_TYPE_FUNCTION => 'function',
        self::TEST_TYPE_DURATION => 'duration',
    );
    protected $emergencyFailureStatusBits = array(
        0=>'circuit_failure',
        1=>'battery_duration_failed',
        2=>'battery_failed',
        3=>'emergency_lamp_failed',
        4=>'function_test_overdue',
        5=>'duration_test_overdue',
        6=>'function_test_failed',
        7=>'duration_test_failed'
    );

    const FAIL_ICON = '<img src="../themes/intsys/images/errorRedDot.png" />';
    const OK_ICON = '<img src="../themes/intsys/images/successGreenDot.png" />';

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_emergency';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('lc_id, dvc_id, fk_ethernet, test_type', 'required'),
			array('lc_id, dvc_id, fk_ethernet, fk_description', 'numerical', 'integerOnly'=>true),
            array('circuit_failure, battery_duration_failed, battery_failed, emergency_lamp_failed, function_test_overdue, duration_test_overdue, function_test_failed, duration_test_failed', 'boolean'),
			array('fk_ethernet', 'length', 'max'=>20),
			// The following rule is used by search().
			array('dvc_id, test_type, circuit_failure, battery_duration_failed, battery_failed, emergency_lamp_failed, function_test_overdue, duration_test_overdue, function_test_failed, duration_test_failed, created_at', 'safe', 'on'=>'search'),
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
		return array(
            'fk_ethernet' => 'Floor',
            'ethernet_name' => 'Floor',
            'lc_id' => 'Light Ctrl',
			'dvc_id' => 'Device',
            'fk_description' => 'Dev Type',
            'type_description' => 'Dev Type',
            'test_type' => 'Test Type',
			'circuit_failure' => 'Circuit',
			'battery_duration_failed' => 'Battery Duration',
			'battery_failed' => 'Battery',
            'emergency_lamp_failed' => 'Emergency Lamp',
            'function_test_overdue' => 'Function Test Overdue',
            'duration_test_overdue' => 'Duration Test Overdue',
            'function_test_failed' => 'Function Test',
            'duration_test_failed' => 'Duration Test',
            'translated_circuit_failure' => 'Circuit',
            'translated_battery_duration_failed' => 'Battery Duration',
            'translated_battery_failed' => 'Battery',
            'translated_emergency_lamp_failed' => 'Emergency Lamp',
            'translated_function_test_overdue' => 'Function Test Overdue',
            'translated_duration_test_overdue' => 'Duration Test Overdue',
            'translated_function_test_failed' => 'Function Test',
            'translated_duration_test_failed' => 'Duration Test',
			'created_at' => 'Reported At',
		);
	}

    public function getReportFields(){
        return array(
            'ethernet_name',
            'lc_id',
            'dvc_id',
            'test_type',
            'translated_circuit_failure',
            'translated_battery_duration_failed',
            'translated_battery_failed',
            'translated_emergency_lamp_failed',
            'translated_function_test_overdue',
            'translated_duration_test_overdue',
            'translated_function_test_failed',
            'translated_duration_test_failed',
            'created_at',
        );
    }

    public function ethernet_name()
    {
        return $this->ethernet->name;
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
        $config = array(
            'criteria'=>$this->getSearchCriteria(),
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

    protected function getSearchCriteria() {
        $criteria=new CDbCriteria;
        $criteria->with = array( 'ethernet' );

        $criteria->compare('fk_ethernet', $this->fk_ethernet, true );
        $criteria->compare('lc_id',$this->lc_id);
        $criteria->compare('dvc_id',$this->dvc_id);
        $criteria->compare('fk_description',$this->fk_description, true);
        $criteria->compare('test_type',$this->test_type);
        $criteria->compare('circuit_failure',$this->circuit_failure);
        $criteria->compare('battery_duration_failed',$this->battery_duration_failed);
        $criteria->compare('battery_failed',$this->battery_failed);
        $criteria->compare('emergency_lamp_failed',$this->emergency_lamp_failed);
        $criteria->compare('function_test_overdue',$this->function_test_overdue);
        $criteria->compare('duration_test_overdue',$this->duration_test_overdue);
        $criteria->compare('function_test_failed',$this->function_test_failed);
        $criteria->compare('duration_test_failed',$this->duration_test_failed);
        $criteria->compare('created_at',$this->created_at,true);

        return $criteria;
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
        }elseif ($name == 'type_description'){
            return $this->description->description;
        }elseif ($name == 'translated_circuit_failure'){
            return $this->circuit_failure ? 'FAIL' : 'OK';
        }elseif ($name == 'translated_battery_duration_failed'){
            return $this->battery_duration_failed ? 'FAIL' : 'OK';
        }elseif ($name == 'translated_battery_failed'){
            return $this->battery_failed ? 'FAIL' : 'OK';
        }elseif ($name == 'translated_emergency_lamp_failed'){
            return $this->emergency_lamp_failed ? 'FAIL' : 'OK';
        }elseif ($name == 'translated_function_test_overdue'){
            return $this->function_test_overdue ? 'FAIL' : 'OK';
        }elseif ($name == 'translated_duration_test_overdue'){
            return $this->duration_test_overdue ? 'FAIL' : 'OK';
        }elseif ($name == 'translated_function_test_failed'){
            return $this->function_test_failed ? 'FAIL' : 'OK';
        }elseif ($name == 'translated_duration_test_failed'){
            return $this->duration_test_failed ? 'FAIL' : 'OK';
        }else{
            return parent::__get($name);
        }
    }

    public function getValueToGrid($name){
        if (in_array($name, $this->emergencyFailureStatusBits)) {
            return $this->$name ? Emergency::FAIL_ICON : Emergency::OK_ICON;
        }
    }

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Emergency the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    public function save($runValidation=true,$attributes=null)
    {
        throw new Exception('Method save() is deprecated in this model. Use saveEmergencyStatus() instead');
    }

    /**
     * This method will map the Emergency status received to it's appropriate fields and save a new instance of the object if applicable
     * @param $status
     * @return bool
     */
    public function saveEmergencyStatus($status){
        if (!in_array($this->deviceType, $this->emergencyTypes)){
            return;
        }

        $octets = str_split($status, 2);

        if (!$this->checkIfMappingIsNeeded($octets[2])){
            return;
        }
        $this->mapBits($octets[3]);

        // Composite Key. We must assure that if exists, we update instead to handle PK integrity
        if ($this->isNewRecord) {
            if (Emergency::model()->findByPk(array(
                'fk_ethernet' => $this->fk_ethernet,
                'lc_id' => $this->lc_id,
                'dvc_id' => $this->dvc_id
            ))) {
                $this->isNewRecord = false;
                $this->created_at = date('Y-m-d H:i:s');
            }
        }

        if ($this->isWorthSaving()){
            /** @var CDbTransaction $transaction */
            $transaction = Yii::app()->db->beginTransaction();
            try {
                if (parent::save(true)) {
                    $this->saveFailureFlags(); // Must run after save command, because we need the trigger to save the info on EmergencyHist
                    $transaction->commit();
                    $this->clearFlags();
                } else {
                    $transaction->rollback();
                }
            } catch (Exception $e) {
                $transaction->rollback();
                throw $e;
            }
        }
    }

    /**
     * Every time a failure occurs, we have to set the new_failure flag. That will allow us to get in reports, when did that failure occurred
     * We only set the flag if is a new failure (no other flag found). If no failure happened, then we clear all flags.
     * Flags are set on the StatusHist
     */
    protected function saveFailureFlags() {
        foreach ($this->emergencyFailureStatusBits as $field) {
            $this->saveFailureFlag($field);
        }
    }

    /**
     * Does the saving of one flag per field
     * These Flags are used to make note of new failures. If we have a failure ($this->field = 1) we look for a flag. If it's already set, then it recurrent, and we ignore.
     * If the flag is not set, then ait's a new failure, and we create a new flag for it.
     * If the value is ok, then we clear all the flags that may exist, as it is not failing anymore.
     *
     * @param $field
     */
    protected function saveFailureFlag($field) {
        $flagField = 'new_'.$field;

        if ($this->$field) {
            $flagSet = Yii::app()->db->createCommand(
                "SELECT COUNT($flagField)
                FROM tbl_emergency_hist
                WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id AND $flagField=1"
            )->queryScalar();
            if (!$flagSet) {
                Yii::app()->db->createCommand(
                    "UPDATE tbl_emergency_hist
                    SET $flagField=1
                    WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id
                    AND id_emergency_hist=(
                      SELECT latest_id FROM (
                        SELECT MAX(id_emergency_hist) AS latest_id
                        FROM tbl_emergency_hist
                        WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id
                      ) AS T
                    )"
                )->execute();
            }
        } else {
            Yii::app()->db->createCommand(
                "UPDATE tbl_emergency_hist
                SET $flagField=0
                WHERE fk_ethernet=$this->fk_ethernet AND lc_id=$this->lc_id AND dvc_id=$this->dvc_id"
            )->execute();
        }
    }

    protected function checkIfMappingIsNeeded($commandOctet) {
        $mappingNeeded = false;
        $bits = str_split($this->hex2binReverted($commandOctet), 1);
        foreach (array_keys($this->emergencyCommandBits) as $key){
            if ($bits[$key]){
                $testType = $this->emergencyCommandBits[$key];
                $this->test_type = $this->emergencyTestsDescription[$testType];
                $mappingNeeded = true;
                break;
            }
        }
        return $mappingNeeded;
    }

    protected function mapBits($statusOctet) {
        $bits = str_split($this->hex2binReverted($statusOctet), 1);
        foreach ($bits as $key => $value) {
            $attr = $this->emergencyFailureStatusBits[$key];
            $this->$attr = ($value == 1);
        }
    }

    protected function hex2bin($hexValue){
        return substr('00000000'.decbin(hexdec($hexValue)), -8);
    }

    protected function hex2binReverted($hexValue){
        return strrev(substr('00000000'.decbin(hexdec($hexValue)), -8));
    }

    protected function clearFlags() {
        $emergencyCommand = new EmergencyTriggerCommand();
        $testType = array_search($this->test_type, $this->emergencyTestsDescription);
        $emergencyCommand->sendClearFlagCommand($this->fk_ethernet, $this->lc_id, $this->dvc_id, $testType);
    }

    /**
     * Some Hardware problems may not clear the emergency responses, even if the clearFlags command has been sent.
     * That is due to the lag that the devices have to respond...
     * Therefore in order to avoid duplicated information we only save the same information if it is 5 minutes apart
     */
    protected function isWorthSaving() {
        $criteria = new CDbCriteria();
        $criteria->compare('lc_id', $this->lc_id);
        $criteria->compare('dvc_id', $this->dvc_id);
        $criteria->compare('fk_ethernet', $this->fk_ethernet);
        $criteria->compare('test_type', $this->test_type);
        $criteria->compare('circuit_failure', $this->circuit_failure);
        $criteria->compare('battery_duration_failed', $this->battery_duration_failed);
        $criteria->compare('battery_failed', $this->battery_failed);
        $criteria->compare('emergency_lamp_failed', $this->emergency_lamp_failed);
        $criteria->compare('function_test_overdue', $this->function_test_overdue);
        $criteria->compare('duration_test_overdue', $this->duration_test_overdue);
        $criteria->compare('function_test_failed', $this->function_test_failed);
        $criteria->compare('function_test_overdue', $this->function_test_overdue);
        $criteria->addCondition('TIMEDIFF(now(), created_at) / 60 < ' . Yii::app()->params['minimum_delay_save_emergency_response_in_minutes']);
        $result = $this->query($criteria);
        if ($result) {
            return false;
        }
        return true;
    }

    /**
     * Clears all entries of devices that belong to a given Ethernet
     * @param $ethernet_id
     */
    public static function clearData($ethernet_id){
        self::model()->deleteAll('fk_ethernet=:fk_ethernet', array(':fk_ethernet'=>$ethernet_id));
    }

}

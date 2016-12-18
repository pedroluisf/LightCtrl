<?php

/**
 * This is the model class for table "vw_failure".
 *
 * The followings are the available columns in table 'vw_failure':
 * @property string $fk_ethernet
 * @property string $ethernet_name
 * @property integer $lc_id
 * @property integer $dvc_id
 * @property string $type
 * @property string $type_description
 * @property boolean $lamp_failed
 * @property boolean $circuit_failure
 * @property boolean $battery_duration_failed
 * @property boolean $battery_failed
 * @property boolean $emergency_lamp_failed
 * @property boolean $function_test_overdue
 * @property boolean $duration_test_overdue
 * @property boolean $function_test_failed
 * @property boolean $duration_test_failed
 * @property string $created_at
 *
 */
class Failure extends CActiveRecord
{
    public $failure_display;

    protected static $myLabels = array(
            'fk_ethernet' => 'Floor',
            'ethernet_name' => 'Floor',
            'lc_id' => 'Light Ctrl',
            'dvc_id' => 'Device',
            'type' => 'Dev Type',
            'type_description' => 'Dev Type',
            'failure_display' => 'Failures',
            'failure_string' => 'Failures',
            'lamp_failed' => 'Lamp Failed',
            'circuit_failure' => 'Circuit Failure',
            'battery_duration_failed' => 'Battery Duration Failed',
            'battery_failed' => 'Battery Failed',
            'emergency_lamp_failed' => 'Emergency Lamp Failed',
            'function_test_overdue' => 'Function Test Overdue',
            'duration_test_overdue' => 'Duration Test Overdue',
            'function_test_failed' => 'Function Test Failed',
            'duration_test_failed' => 'Duration Test Failed',
			'created_at' => 'Reported At',
		);

    protected static $relevantAttributes = array(
        'lamp_failed',
        'circuit_failure',
        'battery_duration_failed',
        'battery_failed',
        'emergency_lamp_failed',
        'function_test_overdue',
        'duration_test_overdue',
        'function_test_failed',
        'duration_test_failed'
    );

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'vw_failure';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			// The following rule is used by search().
			array('dvc_id, fk_ethernet, type_description, created_at, failure_display', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
        return self::$myLabels;
	}

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabelsForReport()
    {
        $reportLabels = self::$myLabels;
        unset($reportLabels['fk_ethernet']);
        unset($reportLabels['failure_display']);
        unset($reportLabels['type']);
        unset($reportLabels['lamp_failed']);
        unset($reportLabels['circuit_failure']);
        unset($reportLabels['battery_duration_failed']);
        unset($reportLabels['battery_failed']);
        unset($reportLabels['emergency_lamp_failed']);
        unset($reportLabels['function_test_overdue']);
        unset($reportLabels['duration_test_overdue']);
        unset($reportLabels['function_test_failed']);
        unset($reportLabels['duration_test_failed']);
        return $reportLabels;
    }

    public function getReportFields(){
        return array(
            'ethernet_name',
            'lc_id',
            'dvc_id',
            'type_description',
            'failure_string',
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

        $criteria->compare('fk_ethernet', $this->fk_ethernet);
        $criteria->compare('ethernet_name', $this->ethernet_name);
        $criteria->compare('lc_id',$this->lc_id, true);
		$criteria->compare('dvc_id',$this->dvc_id, true);
        $criteria->compare('type_description',$this->type_description, true);
		$criteria->compare('created_at',$this->created_at,true);
        $criteria->compare(
            "CONCAT(
                CASE WHEN lamp_failed=1
                    THEN 'lamp_failed'
                    ELSE ''
                END,'__',
                CASE WHEN circuit_failure=1
                    THEN 'circuit_failure'
                    ELSE ''
                END,'__',
                CASE WHEN battery_duration_failed=1
                    THEN 'battery_duration_failed'
                    ELSE ''
                END,'__',
                CASE WHEN battery_failed=1
                    THEN 'battery_failed'
                    ELSE ''
                END,'__',
                CASE WHEN emergency_lamp_failed=1
                    THEN 'emergency_lamp_failed'
                    ELSE ''
                END,'__',
                CASE WHEN function_test_overdue=1
                    THEN 'function_test_overdue'
                    ELSE ''
                END,'__',
                CASE WHEN duration_test_overdue=1
                    THEN 'duration_test_overdue'
                    ELSE ''
                END,'__',
                CASE WHEN function_test_failed=1
                    THEN 'function_test_failed'
                    ELSE ''
                END,'__',
                CASE WHEN duration_test_failed=1
                    THEN 'duration_test_failed'
                    ELSE ''
                END,'__'
            )",
            $this->failure_display, true);

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
        if ($name == 'failure_string'){
            return $this->getReadableFailure();
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
     * This model is over a view, therefore we do not allow save
     *
     * @param bool $runValidation
     * @param null $attributes
     * @return bool|void
     * @throws Exception
     */
    public function save($runValidation=true,$attributes=null) {
        throw new Exception('Method save() is deprecated in this model');
    }

    /**
     * Maps the Status on a human readable format
     * @param $row
     * @return string
     */
    public static function mapFailures($row){
        $info = array();
        foreach(self::$relevantAttributes as $attr){
            if ($row->$attr) {
                $info[] = self::$myLabels[$attr];
            }
        }
        return implode(' | ', $info);
    }

    /**
     * Maps the status of the current instance (Used for the Grid)
     * @return string
     */
    public function getReadableFailure(){
        return self::mapFailures($this);
    }

}

<?php

/**
 * This is the model class for table "tbl_command_schedule".
 *
 * The followings are the available columns in table 'tbl_command_schedule':
 * @property integer $id_exec_schedule
 * @property string $description
 * @property string $periodicity
 * @property int $fk_area
 * @property string $fk_ethernet
 * @property integer $lc_id
 * @property integer $dvc_id
 * @property integer $group
 * @property string $type
 * @property string $cci_sw_num
 * @property string $cci_data
 * @property string $start_date
 * @property string $end_date
 * @property int $fk_user
 * @property int $manual_trigger
 * @property int $fk_command
 * @property string $created_at
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Ethernet $ethernet
 * @property Area $area
 */

class ExecutedSchedule extends CActiveRecord
{
    const UNIQUE_COMMAND = 'unique';
    const WEEKLY_COMMAND = 'weekly';
    const MONTHLY_COMMAND = 'monthly';

    const OK_ICON = '<img src="../themes/intsys/images/success.png" />';
    const FAIL_ICON = '<img src="../themes/intsys/images/error.png" />';
    const PENDING_ICON = '<img src="../themes/intsys/images/pending.png" />';

    public $command_status;

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_executed_schedule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array('description, periodicity, fk_area, fk_ethernet, type, fk_user, start_date, end_date', 'required'),
            array('group, cci_sw_num, cci_data', 'safe'),
            array('description', 'length', 'max'=>256),
            array('group', 'numerical', 'integerOnly'=>true),
            array('fk_area, fk_ethernet, fk_user', 'length', 'max'=>11),
            array('type', 'length', 'max'=>64),
            // The following rule is used by search().
            array('id_exec_schedule, description, periodicity, fk_area, fk_ethernet, lc_id, dvc_id, group, type, manual_trigger, end_date, fk_user, command_status', 'safe', 'on' => 'search'),
            array('description, periodicity, fk_area, fk_ethernet, lc_id, dvc_id, group, type, start_date, end_date, fk_user', 'safe', 'on' => 'create'),
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
			'user' => array(self::BELONGS_TO, 'User', 'fk_user'),
            'area' => array(self::BELONGS_TO, 'Area', 'fk_area'),
            'ethernet' => array(self::BELONGS_TO, 'Ethernet', 'fk_ethernet'),
            'commandQueue' => array(self::BELONGS_TO, 'CommandQueue', 'fk_command'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_exec_schedule' => 'Id Schedule',
			'description' => 'Description',
			'periodicity' => 'Periodicity',
			'fk_area' => 'Area',
			'fk_ethernet' => 'Floor',
			'lc_id' => 'Light Ctr',
            'dvc_id' => 'Device',
            'group' => 'Group',
			'type' => 'Type',
            'cci_sw_num' => 'Switch Number',
			'cci_data' => 'Action',
			'start_date' => 'Executed At',
            'end_date' => 'End Date',
			'fk_user' => 'User',
			'created_at' => 'Created At',
            'command_status' => 'Status',
		);
	}

    public function getPeriodicityForDisplay() {
        $periodicity = CommandSchedule::model()->getPeriodicityOptionsList();
        return $periodicity[$this->periodicity];
    }

    public function getTypeForDisplay() {
        $types = CommandSchedule::model()->getCommandTypesList();
        return $types[$this->type];
    }

    public function getWeekDaysForDisplay() {
        $weekdays = CommandSchedule::model()->getWeekdaysList();
        $days2Show = array();
        foreach ($weekdays as $day) {
            if ($this->$day) {
                $days2Show[] = $this->getAttributeLabel($day);
            }
        }
        return implode(', ', $days2Show);
    }

    public function getMonthRepeatForDisplay() {
        $repeat = CommandSchedule::model()->getMonthRepeatOptionsList();
        return $repeat[$this->month_repeat];
    }

    public function getGroupForDisplay() {
        if ($this->group !== null) {
            return $this->group + 1;
        }
        return null;
    }

    public function getCommandStatusToGrid(){
        if ($this->commandQueue->status == 'finished'){
            return self::OK_ICON;
        } elseif ($this->commandQueue->status == 'failed' || $this->commandQueue->status == 'error'){
            return self::FAIL_ICON;
        } elseif ($this->commandQueue->status == 'pending' || $this->commandQueue->status == 'processing'){
            return self::PENDING_ICON;
        } else {
            return '';
        }
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
        $criteria->with = array( 'area', 'ethernet' );
        $criteria->with = array( 'area', 'commandQueue' );

		$criteria->compare('id_exec_schedule',$this->id_exec_schedule);
		$criteria->compare('lc_id',$this->lc_id,true);
        $criteria->compare('dvc_id',$this->dvc_id,true);
        $criteria->compare('cci_sw_num',$this->cci_sw_num,true);
        $criteria->compare('t.group',$this->group);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('periodicity',$this->periodicity,true);
		$criteria->compare('t.fk_area',$this->fk_area,true);
        $criteria->compare('fk_ethernet',$this->fk_ethernet,true);
		$criteria->compare('type',$this->type,true);
        $criteria->compare('manual_trigger',$this->manual_trigger);
		$criteria->compare('start_date',$this->start_date,true);
		$criteria->compare('end_date',$this->end_date,true);
        $criteria->compare('commandQueue.status',$this->command_status,true);

        $config = array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'id_exec_schedule DESC',
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
    public function searchEmergencyScheduled()
    {
        $criteria=new CDbCriteria;
        $criteria->with = array('area', 'ethernet', 'commandQueue');

        $criteria->compare('id_exec_schedule',$this->id_exec_schedule);
        $criteria->compare('lc_id',$this->lc_id,true);
        $criteria->compare('dvc_id',$this->dvc_id,true);
        $criteria->compare('cci_sw_num',$this->cci_sw_num,true);
        $criteria->compare('t.group',$this->group);
        $criteria->compare('description',$this->description,true);
        $criteria->compare('periodicity',$this->periodicity,true);
        $criteria->compare('t.fk_area',$this->fk_area,true);
        $criteria->compare('fk_ethernet',$this->fk_ethernet,true);
        $criteria->addInCondition('type',array('function','duration'));
        $criteria->compare('manual_trigger',$this->manual_trigger);
        $criteria->compare('start_date',$this->start_date,true);
        $criteria->compare('end_date',$this->end_date,true);
        $criteria->compare('commandQueue.status',$this->command_status,true);

        $config = array(
            'criteria'=>$criteria,
            'pagination' => array(
                'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']),
            ),
            'sort'=>array(
                'defaultOrder'=>'id_exec_schedule DESC',
                'multiSort'=>true,
            )
        );
        return new CActiveDataProvider($this, $config);
    }

    /**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ExecutedSchedule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * This function will return the devices ( [ lc_id => [ dvc_id ] ] ) that were triggered on this scheduled command or empty if all devices should be considered
     * @return array|null
     */
    public function getDevicesExecuted() {
        $components = array();
        $parser = new JsonComponentsParser($this->fk_ethernet);

        if ($this->group) {
            $components = $parser->getDevicesByGroup($this->group);
        } else {
            if ($this->lc_id) {
                if ($this->dvc_id) {
                    $component = $parser->getDeviceByLcIdAndDvcId($this->lc_id, $this->dvc_id);
                    $components[] = $component;
                } else {
                    $components = $parser->getDevicesByLcID($this->lc_id);
                }
            } else {
                $components = $parser->getAllDevices();
            }
        }

        $response = array();
        /** @var DeviceTransfer $component */
        foreach ($components as $component) {
            $response[$component->lc_id][] = $component->dvc_id;
        }
        return $response;
    }

    /**
     * Clears all entries of devices that belong to a given Ethernet
     * @param $ethernet_id
     */
    public static function clearData($ethernet_id){
        self::model()->deleteAll('fk_ethernet=:fk_ethernet', array(':fk_ethernet'=>$ethernet_id));
    }

}

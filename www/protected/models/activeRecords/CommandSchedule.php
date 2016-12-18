<?php

/**
 * This is the model class for table "tbl_command_schedule".
 *
 * The followings are the available columns in table 'tbl_command_schedule':
 * @property integer $id_schedule
 * @property string $description
 * @property string $periodicity
 * @property string $fk_area
 * @property string $fk_ethernet
 * @property integer $lc_id
 * @property integer $dvc_id
 * @property integer $group
 * @property integer $priority
 * @property string $type
 * @property string $cci_sw_num
 * @property string $cci_data
 * @property string $cmd
 * @property string $cmd_name
 * @property string $start_date
 * @property string $event_time
 * @property string $month_repeat
 * @property integer $monday
 * @property integer $tuesday
 * @property integer $wednesday
 * @property integer $thursday
 * @property integer $friday
 * @property integer $saturday
 * @property integer $sunday
 * @property string $fk_user
 * @property string $created_at
 * @property string $date_display;
 * @property string $time_display;
 *
 * The followings are the available model relations:
 * @property User $user
 * @property Ethernet $ethernet
 * @property Area $area
 */

class CommandSchedule extends CActiveRecord
{
    const UNIQUE_NORMAL_SCENARIO = 'unique_normal';
    const UNIQUE_FUNCTION_SCENARIO = 'unique_function';
    const UNIQUE_DURATION_SCENARIO = 'unique_duration';
    const WEEKLY_NORMAL_SCENARIO = 'weekly_normal';
    const WEEKLY_FUNCTION_SCENARIO = 'weekly_function';
    const WEEKLY_DURATION_SCENARIO = 'weekly_duration';
    const MONTHLY_NORMAL_SCENARIO = 'monthly_normal';
    const MONTHLY_FUNCTION_SCENARIO = 'monthly_function';
    const MONTHLY_DURATION_SCENARIO = 'monthly_duration';

    const UNIQUE_COMMAND = 'unique';
    const WEEKLY_COMMAND = 'weekly';
    const MONTHLY_COMMAND = 'monthly';

    public $draw_id;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_command_schedule';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
        return array(
            array('description, periodicity, fk_area, fk_ethernet, draw_id, type, fk_user, start_date, event_time', 'required'),
            array('monday, tuesday, wednesday, thursday, friday, saturday, sunday, month_repeat, cci_sw_num, cci_data, group, priority', 'safe'),
            array('cci_sw_num, cci_data', 'required', 'on' => array(self::UNIQUE_NORMAL_SCENARIO, self::WEEKLY_NORMAL_SCENARIO, self::MONTHLY_NORMAL_SCENARIO)),
            array('month_repeat', 'required', 'on' => array(self::MONTHLY_NORMAL_SCENARIO, self::MONTHLY_FUNCTION_SCENARIO, self::MONTHLY_DURATION_SCENARIO)),
            array('periodicity', 'checkboxListValidator', 'on' => array(self::WEEKLY_NORMAL_SCENARIO, self::WEEKLY_FUNCTION_SCENARIO, self::WEEKLY_DURATION_SCENARIO)),
            array('monday, tuesday, wednesday, thursday, friday, saturday, sunday', 'numerical', 'integerOnly'=>true),
            array('description, cmd', 'length', 'max'=>256),
            array('priority', 'numerical', 'integerOnly'=>true, 'max'=>10),
            array('priority', 'length', 'max'=>1),
            array('fk_area, fk_ethernet, fk_user', 'length', 'max'=>11),
            array('type', 'length', 'max'=>64),
            array('event_time', 'match', 'pattern' => '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/'),
            array('cci_data','in','range'=>array('on', 'off', 'momentary'), 'allowEmpty'=>false, 'on' => array(self::UNIQUE_NORMAL_SCENARIO, self::WEEKLY_NORMAL_SCENARIO, self::MONTHLY_NORMAL_SCENARIO)),
            array('cci_sw_num','in','range'=>range(1, 8), 'allowEmpty'=>false, 'on' => array(self::UNIQUE_NORMAL_SCENARIO, self::WEEKLY_NORMAL_SCENARIO, self::MONTHLY_NORMAL_SCENARIO)),
            // The following rule is used by search().
            array('id_schedule, description, periodicity, fk_area, fk_ethernet, lc_id, dvc_id, group, priority, type, cmd, start_date, date_display, event_time, time_display, month_repeat, monday, tuesday, wednesday, thursday, friday, saturday, sunday, fk_user, created_at', 'safe', 'on' => 'search'),
        );
    }

    public function checkboxListValidator($attribute)
    {
        if (!$this->monday && !$this->tuesday && !$this->wednesday && !$this->thursday && !$this->friday && !$this->saturday && !$this->sunday) {
            $this->addError($attribute, 'At least one week day must be selected');
        }
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
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id_schedule' => 'Id Schedule',
			'description' => 'Description',
			'periodicity' => 'Periodicity',
            'periodicity_display' => 'Periodicity',
			'fk_area' => 'Area',
			'fk_ethernet' => 'Floor',
            'ethernet_name' => 'Floor',
			'lc_id' => 'Light Ctr',
            'dvc_id' => 'Device',
            'group' => 'Group',
            'priority' => 'Priority',
			'type' => 'Type',
            'type_display' => 'Type',
			'cci_sw_num' => 'Switch Number',
			'cci_data' => 'Action',
			'cmd' => 'Cmd',
			'start_date' => 'Date',
            'date_display' => 'Date',
			'event_time' => 'Time',
			'time_display' => 'Time',
			'month_repeat' => 'Repeat',
            'month_repeat_display' => 'Repeat',
            'weekdays_display' => 'Weekdays',
			'monday' => 'Monday',
			'tuesday' => 'Tuesday',
			'wednesday' => 'Wednesday',
			'thursday' => 'Thursday',
			'friday' => 'Friday',
			'saturday' => 'Saturday',
			'sunday' => 'Sunday',
			'fk_user' => 'User',
			'created_at' => 'Created At',
		);
	}

    public function getReportFields(){
        return array(
            'fk_area',
            'ethernet_name',
            'lc_id',
            'dvc_id',
            'group',
            'priority',
            'type_display',
            'cci_sw_num',
            'cci_data',
            'date_display',
            'time_display',
            'periodicity_display',
            'month_repeat_display',
            'weekdays_display',
            'created_at',
        );
    }

    public function __get($name) {
        switch ($name) {
            case 'ethernet_name':
                return $this->ethernet->name;
                break;
            case 'date_display':
                return $this->getDateForDisplay();
                break;
            case 'time_display':
                return $this->getTimeForDisplay();
                break;
            case 'type_display':
                return $this->getTypeForDisplay();
                break;
            case 'periodicity_display':
                return $this->getPeriodicityForDisplay();
                break;
            case 'month_repeat_display':
                return $this->getMonthRepeatForDisplay();
                break;
            case 'weekdays_display':
                return $this->getWeekDaysForDisplay();
                break;
        }
        return parent::__get($name);
    }

    public function getDateForDisplay() {
        return date('Y-m-d', strtotime($this->start_date));
    }

    public function getNextDateForScheduler() {
        switch ($this->periodicity){
            case $this::WEEKLY_COMMAND:
                $dateForDisplay = $this->getNextWeeklyDateForScheduler();
                break;
            case $this::MONTHLY_COMMAND:
                $dateForDisplay = $this->getNextMonthlyDateForScheduler();
                break;
            default:
                $dateForDisplay = date('Y-m-d', strtotime($this->start_date));
        }
        return $dateForDisplay;
    }

    protected function getNextWeeklyDateForScheduler() {
        $nextDate = null;
        for ($i=0; $i<8; $i++) {
            if ($i==0) {
                $currentTime = (((date('h') * 60) + date('i')) * 60) + date('s');
                $dayOfWeek = lcfirst(date('l'));
                if ($this->$dayOfWeek == 1 && (strtotime($this->event_time) >= $currentTime)) {
                    $nextDate = time();
                    break;
                }
            } else {
                $dayOfWeek = lcfirst(date('l', time() + ($i * 86400)));
                if ($this->$dayOfWeek == 1) {
                    $nextDate = time() + ($i * 86400);
                    break;
                }
            }
        }
        return $nextDate > 0 ? date('Y-m-d', $nextDate) : null;
    }

    protected function getNextMonthlyDateForScheduler() {
        $currentTime = (((date('h') * 60) + date('i')) * 60) + date('s');
        $today = time() - $currentTime;
        $startDate = strtotime($this->start_date);
        if ($startDate == $today && (strtotime($this->event_time) >= $currentTime)) {
            $nextDate = time();
        } else {
            $month = date('m', $startDate);
            $year = $startDateYear = date('Y', $startDate);
            $nextDate = null;
            $monthRepeatValues = array(
                'monthly' => 1,
                'bimonthly' => 2,
                'quarterly' => 3,
                'biannualy' => 6,
            );

            do {
                if ($startDate <= $today) {
                    do {
                        $month += $monthRepeatValues[$this->month_repeat];
                    } while ($month < date('m') && $year <= $startDateYear);

                    if (date('d', $startDate) <= date('d')) {
                        $month += $monthRepeatValues[$this->month_repeat];
                    }
                }
                if ($month > 12) {
                    $month-=12;
                    $year++;
                }
                if (checkdate($month, (int) date('d', $startDate), $year)) {
                    $nextDate = strtotime($year . '-' . $month . '-' . date('d', $startDate));
                }
            } while (!$nextDate);
        }
        return $nextDate > 0 ? date('Y-m-d', $nextDate) : null;
    }

    public function getTimeForDisplay() {
        return date('H:i', strtotime($this->event_time));
    }

    public function getPeriodicityForDisplay() {
        $periodicity = $this->getPeriodicityOptionsList();
        return $periodicity[$this->periodicity];
    }

    public function getTypeForDisplay() {
        $types = $this->getCommandTypesList();
        return $types[$this->type];
    }

    public function getWeekDaysForDisplay() {
        $weekdays = $this->getWeekdaysList();
        $days2Show = array();
        foreach ($weekdays as $day) {
            if ($this->$day) {
                $days2Show[] = $this->getAttributeLabel($day);
            }
        }
        return implode(', ', $days2Show);
    }

    public function getMonthRepeatForDisplay() {
        $repeat = $this->getMonthRepeatOptionsList();
        return $repeat[$this->month_repeat];
    }

    public function getGroupForDisplay() {
        if ($this->group !== null) {
            return $this->group + 1;
        }
        return null;
    }

    /**
     * @return array admissible WeekDays
     */
    public static function getWeekdaysList()
    {
        return array(
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday'
        );
    }

    /**
     * @return array admissible command names
     */
    public static function getCommandTypesList()
    {
        return array(
            'normal' => 'Switch Input',
            'function' => 'Emergency Function Test',
            'duration' => 'Emergency Duration Test'
        );
    }

    public static function getPeriodicityOptionsList() {
        return array(
            'unique'=>'Unique',
            'weekly'=>'Weekly',
            'monthly'=>'Monthly'
        );
    }
    /**
     * @return array admissible repeat options
     */
    public static function getMonthRepeatOptionsList() {
        return array(
            'monthly' => 'Every Month',
            'bimonthly' => 'Every 2 Months',
            'quarterly' => 'Every 3 Months',
            'biannualy' => 'Every 6 Months',
        );
    }

    /**
     * @return array admissible switch number options
     */
    public static function getClearContactDataOptionsList() {
        return array(
            'on' => 'ON',
            'off' => 'OFF',
            'momentary' => 'MOMENTARY',
        );
    }

    /**
     * @return array admissible group options
     */
    public static function getGroupsList() {
        return range(1,15);
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

		$criteria->compare('id_schedule',$this->id_schedule);
		$criteria->compare('lc_id',$this->lc_id,true);
        $criteria->compare('dvc_id',$this->dvc_id,true);
        $criteria->compare('cci_sw_num',$this->cci_sw_num,true);
        $criteria->compare('t.group',$this->group,true);
        $criteria->compare('priority',$this->priority,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('periodicity',$this->periodicity,true);
		$criteria->compare('t.fk_area',$this->fk_area,true);
        $criteria->compare('fk_ethernet',$this->fk_ethernet,true);
		$criteria->compare('type',$this->type,true);
		$criteria->compare('event_time',$this->event_time,true);

        // Do not display schedules from inactive Caneths
        $criteria->addCondition('fk_ethernet IN (SELECT id_ethernet FROM tbl_ethernet WHERE inactive=0)');

        $config = array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'id_schedule DESC',
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
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CommandSchedule the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

    /**
     * Map the appropriate DrawId
     */
    public function afterFind() {
        $parser = new JsonComponentsParser($this->fk_ethernet);
        try {
            if ($this->lc_id !== null) {
                if ($this->dvc_id !== null) {
                    $component = $parser->getDeviceByLcIdAndDvcId($this->lc_id, $this->dvc_id);
                } else {
                    $component = $parser->getDeviceByLcIdAndDvcId($this->lc_id);
                }
            } else {
                $component = $parser->getEthernetComponent();
            }
            $this->draw_id = $component['draw_id'];
        } catch (Exception $e) {
            $this->draw_id = null;
        }
        parent::afterFind();
    }

    /**
     * Check for missing fields specific for the selected periodicity
     * * @return bool
     */
    public function beforeSave() {

        if (isset($this->fk_area) && isset($this->fk_ethernet) && isset($this->draw_id) && isset($this->type)) {

            $parser = new JsonComponentsParser($this->fk_ethernet);
            $component = $parser->getDeviceByDrawId($this->draw_id);

            $params = array(
                'ethernet_id' => $this->fk_ethernet
            );

            if ($this->type == 'normal' && !($component instanceof DeviceTransfer)) {
                $this->addError('dvc_id', 'For "Normal" command type, an input must be selected as target.');
                return false;
            } else if (($this->type == 'function' || $this->type == 'duration') && !($component instanceof DeviceTransfer)) {
                if (isset($this->group) && $this->group) {
                    $params['dvc_id'] = 'g' . $this->group;
                } else {
                    $params['dvc_id'] = EmergencyTriggerCommand::ALL;
                }
            }

            $this->event_time = '1970-01-01 ' . $this->event_time .':00';

            if (!($component instanceof EthernetTransfer)) {
                $this->lc_id = $component['lc_id'];
                $params['lc_id'] = $component['lc_id'];
                if ($component instanceof deviceTransfer) {
                    $this->dvc_id = $component['dvc_id'];
                    $params['dvc_id'] = $component['dvc_id'];
                }
            }

            if ($this->periodicity != self::MONTHLY_COMMAND && isset($this->month_repeat)) {
                unset($this->month_repeat);
            }

            if ($this->type == 'normal') {
                $command = new ClearContactInputCommand();
                $params['cci_sw_num'] = $this->cci_sw_num;
                $params['cci_data'] = $this->cci_data;
                $preparedCmd = $command->prepareCommandForScheduling($params);
                $this->cmd = $preparedCmd['cmd'];
            } else {
                if (isset($this->cci_sw_num)) {
                    unset($this->cci_sw_num);
                }
                if (isset($this->cci_data)) {
                    unset($this->cci_data);
                }
                $command = new EmergencyTriggerCommand();
                $params['type'] = ($this->type == 'function') ? 'func' : 'durat';
                $params['proc'] = 'start';
                $preparedCmd = $command->prepareCommandForScheduling($params);
                $this->cmd = $preparedCmd['cmd'];
            }
            $this->cmd_name = $preparedCmd['cmd_name'];
        }
        return true;
    }

    /**
     * This will create a executedSchedule Record linked to a new Command on the Queue
     *
     * @throws Exception
     */
    public function execute($manualTrigger=false){

        // When manually triggered the current user is the logged in one. When automatic is the one who created the Scheduled Task
        $userId = ($manualTrigger ? Yii::app()->user->getId() : $this->fk_user);

        // Create the command queue
        $commandQueueModel = new CommandQueue('create');
        $commandQueueModel->cmd = $this->cmd;
        $commandQueueModel->cmd_name = $this->cmd_name;
        $commandQueueModel->ethernet_id = $this->fk_ethernet;
        $commandQueueModel->fk_user = $userId;
        // Generate a random hash. Scheduling must always perform, so it's not subject to hash validations
        $commandQueueModel->hash = md5(time().getrandmax());
        if (!$commandQueueModel->save()){
            $msg = 'Error occurred while saving new Command Queue Record' .
                PHP_EOL . print_r($commandQueueModel->getErrors(), true);
            throw new Exception($msg);
        }

        // Create the linked Executed Schedule
        $executed = new ExecutedSchedule('create');
        $executed->setAttributes($this->getAttributes());
        // Override Attributes
        if ($manualTrigger) {
            $startDate = new DateTime(date('Y-m-d H:i:00'));
            $startDate->modify('+1 minute');
        } else {
            $startDate = new DateTime(date('Y-m-d', time()) . ' ' . date('H:i:00', strtotime($this->event_time)));
        }
        $endDate = clone($startDate);
        switch (strtolower($this->type)) {
            case 'duration':
                $endDate->modify('+4 hours');
                break;
            case 'function':
                $endDate->modify('+15 minutes');
                break;
        }
        $executed->start_date = $startDate->format('Y-m-d H:i:00');
        $executed->end_date = $endDate->format('Y-m-d H:i:00');
        $executed->fk_user = $userId;
        $executed->manual_trigger = $manualTrigger;
        $executed->fk_command = $commandQueueModel->getPrimaryKey();
        if (!$executed->save()){
            $msg = 'Error occurred while saving new Executed Schedule Record' .
                PHP_EOL . print_r($executed->getErrors(), true);
            throw new Exception($msg);
        }
    }

    /**
     * This method will look for the commands that should be created for a given minute.
     * It will Isolate the schedules with higher priority
     * @param DateTime
     * @return array
     */
    public function getRecordsForDateTime(DateTime $date) {
        // First we get the signature that identify the highest priority for each component
        $prioritySignatures = $this->getPrioritySignature($date);
        if (empty($prioritySignatures)) {
            return array();
        }

        // Now we get the highest corresponding Ids of those signatures
        $ids = $this->getIdsFromPrioritySignature($date, $prioritySignatures);

        // Return data
        $criteria = new CDbCriteria();
        $criteria->addInCondition('id_schedule', $ids);
        $criteria->addCondition('fk_ethernet IN (SELECT id_ethernet FROM tbl_ethernet WHERE inactive=0)');
        return $this->findAll($criteria);
    }

    private function getPrioritySignature(DateTime $date) {
        // Get where clause
        $where = $this->getWhereClauseForPrioritySignature($date);
        return Yii::app()->db->createCommand(
            "SELECT max(priority) as priority, fk_area, fk_ethernet, lc_id, dvc_id, `group`
             FROM tbl_command_schedule WHERE $where
            GROUP BY fk_area, fk_ethernet, lc_id, dvc_id, `group`, cci_sw_num "
        )->queryAll();
    }

    private function getIdsFromPrioritySignature(DateTime $date, array $prioritySignatures) {
        $prioritySignatureWhere = $this->getWhereClauseForPrioritySignature($date);
        // Build Where Clause
        $signature = array();
        foreach ($prioritySignatures as $record) {
            $signature[] = '( fk_area='.$record['fk_area'].
                ' AND fk_ethernet='.$record['fk_ethernet'].
                (is_null($record['lc_id']) ? ' AND lc_id IS NULL ' : ' AND lc_id='.$record['lc_id']).
                (is_null($record['dvc_id']) ? ' AND dvc_id IS NULL ' : ' AND dvc_id='.$record['dvc_id']).
                (is_null($record['group']) ? ' AND `group` IS NULL ' : ' AND `group`='.$record['group']).
                ' AND priority='.$record['priority'].
                ' AND (' . $prioritySignatureWhere . '))'.PHP_EOL;
        }

        // Get Ids
        $ids = Yii::app()->db->createCommand(
            'SELECT max(id_schedule) as id
             FROM tbl_command_schedule WHERE ' . implode(' OR ', $signature) .
            'GROUP BY fk_area, fk_ethernet, lc_id, dvc_id, `group`, cci_sw_num '
        )->queryAll();

        // Clean the received records
        $finalIds = array();
        foreach ($ids as $id) {
            $finalIds[] = $id['id'];
        }

        return $finalIds;
    }

    private function getWhereClauseForPrioritySignature(DateTime $date) {
        // Get where clauses
        $uniqueCriteria = $this->getUniqueSchedules($date);
        $weeklyCriteria = $this->getWeeklySchedules($date);
        $monthlyCriteria = $this->getMonthlySchedules($date);

        return "($uniqueCriteria) OR ($weeklyCriteria) OR ($monthlyCriteria)";
    }

    private function getUniqueSchedules(DateTime $date) {
        $startDate = $date->format("Y-m-d 00:00:00");
        $eventTime = $date->format("1970-01-01 H:i:00");
        $where = "periodicity = 'unique'";
        $where .= " AND start_date = '$startDate'";
        $where .= " AND event_time = '$eventTime'";
        return $where;
    }

    private function getWeeklySchedules(DateTime $date) {
        $startDate = $date->format("Y-m-d 00:00:00");
        $eventTime = $date->format("1970-01-01 H:i:00");
        $dayOfWeek = lcfirst($date->format("l"));
        $where = "periodicity = 'weekly'";
        $where .= " AND start_date <= '$startDate'";
        $where .= " AND $dayOfWeek = 1";
        $where .= " AND event_time = '$eventTime'";
        return $where;
    }

    private function getMonthlySchedules(DateTime $date) {
        $startDate = $date->format("Y-m-d 00:00:00");
        $eventTime = $date->format("1970-01-01 H:i:00");
        $dayMonth = $date->format("d");
        $month = $date->format("m");
        $monthOdd = $month % 2;
        $where = "periodicity = 'monthly'";
        $where .= " AND start_date <= '$startDate'";
        $where .= " AND DAY(start_date) = $dayMonth";
        $where .= " AND event_time = '$eventTime'";
        $where .= " AND ( ";
        $where .= " (month_repeat = 'monthly') ";
        $where .= " OR (month_repeat = 'bimonthly' AND MONTH(start_date)%2 = $monthOdd)";
        $where .= " OR (month_repeat = 'quarterly' AND MONTH(start_date) IN (".$this->getMultiples($month, 3)."))";
        $where .= " OR (month_repeat = 'biannualy' AND MONTH(start_date) IN (".$this->getMultiples($month, 6)."))";
        $where .= ")";
        return $where;
    }

    private function getMultiples($value, $base) {
        $multiples = array();
        for ($i=1; $i<=12; $i++) {
            if (abs($i-$value)%$base == 0) {
                $multiples[] = $i;
            }
        }
        return implode(',', $multiples);
    }

    /**
     * Clears all entries of devices that belong to a given Ethernet
     * @param $ethernet_id
     */
    public static function clearData($ethernet_id){
        self::model()->deleteAll('fk_ethernet=:fk_ethernet', array(':fk_ethernet'=>$ethernet_id));
    }

}

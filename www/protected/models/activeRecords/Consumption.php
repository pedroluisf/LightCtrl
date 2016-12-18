<?php

/**
 * This is the model class for table "tbl_consumption".
 *
 * The followings are the available columns in table 'tbl_consumption':
 * @property integer $id_consumption
 * @property string $date
 * @property integer $fk_ethernet
 * @property integer $lc_id
 * @property integer $dvc_id
 * @property string $type
 * @property integer $fk_description
 * @property integer $consumption_watts
 * @property integer $consumption_minutes
 *
 * The followings are the available model relations:
 * @property Ethernet $ethernet
 * @property Description $description
 */
class Consumption extends CActiveRecord
{
    public $ethernet_name;
    public $type_description;

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_consumption';
	}

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('fk_ethernet, lc_id, dvc_id, type', 'required'),
            array('fk_ethernet, lc_id, dvc_id, fk_description, consumption_watts, consumption_minutes', 'numerical', 'integerOnly'=>true),
            array('fk_ethernet', 'length', 'max'=>11),
            array('fk_description, consumption_watts, consumption_minutes', 'length', 'max'=>5),
            // The following rule is used by search().
            array('date, fk_ethernet, lc_id, dvc_id, fk_description, consumption_watts, consumption_minutes', 'safe', 'on'=>'search'),
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
            'date' => 'Date',
            'fk_ethernet' => 'Floor',
            'ethernet_name' => 'Floor',
            'lc_id' => 'Light Ctrl',
            'dvc_id' => 'Device',
            'type' => 'Dev Type',
            'fk_description' => 'Dev Type',
            'type_description' => 'Dev Type',
            'consumption_watts' => 'Consumption in Watts',
            'consumption_minutes' => 'Consumption in Minutes',
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

		$criteria->compare('id_consumption',$this->id_consumption);
        $criteria->compare('date',$this->date);
        $criteria->compare('fk_ethernet',$this->fk_ethernet);
        $criteria->compare('lc_id',$this->lc_id);
        $criteria->compare('dvc_id',$this->dvc_id);
        $criteria->compare('fk_description',$this->fk_description);
        $criteria->compare('consumption_watts', $this->consumption_watts, true );
        $criteria->compare('consumption_minutes', $this->consumption_minutes, true );

        $config = array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'id_consumption DESC',
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

    public function getDailyConsumption(DateTime $dateFrom, DateTime $dateTo, FiltersForm $filters) {
        $sql = '
            SELECT
             E.name as ethernet_name,
             C.lc_id as lc_id,
             C.dvc_id as dvc_id,
             C.type as type,
             D.description as type_description,
        ';

        // We iterate through all days and translate them to columns
        $days = $this->getDateDaysRange($dateFrom, $dateTo);
        foreach ($days as $day) {
            $label = str_replace('-', '_',$day);
            $sql .= "
                 SUM(CASE WHEN date = '$day' THEN C.consumption_minutes / 60 ELSE 0 END) as consumption_hours_$label,
                 SUM(CASE WHEN date = '$day' THEN C.consumption_minutes ELSE 0 END) as consumption_minutes_$label,
                 SUM(CASE WHEN date = '$day' THEN C.consumption_watts ELSE 0 END) as consumption_watts_$label,
            ";
        }

        $sql .= '
             E.fk_area as fk_area,
             C.fk_ethernet as fk_ethernet,
             C.fk_description as fk_description
            FROM tbl_consumption C
            INNER JOIN tbl_ethernet E ON C.fk_ethernet=E.id_ethernet
            INNER JOIN tbl_area A ON E.fk_area=A.id_area
            LEFT JOIN tbl_description D ON C.fk_description=D.id_description
            WHERE date >= :dateFrom
            AND  date <= :dateTo
            GROUP BY fk_ethernet, lc_id, dvc_id
        ';
        $rawData= Yii::app()->db->createCommand($sql)
            ->bindValue('dateFrom',$dateFrom->format('Y-m-d'))
            ->bindValue('dateTo',$dateTo->format('Y-m-d'))
            ->queryAll();

        return $filters->filter($rawData);
    }

    /**
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @param FiltersForm $filters
     * @param string $valueField
     * @return array - ['date'=>$date, 'value'=>$consumption]
     */
    public function getDailyConsumptionByDate(DateTime $dateFrom, DateTime $dateTo, FiltersForm $filters, $valueField) {

        $whereClause = array();
        $whereClause[] = 'date >= \''.$dateFrom->format('Y-m-d').'\'';
        $whereClause[] = 'date <= \''.$dateTo->format('Y-m-d').'\'';
        $whereClause = array_merge($whereClause, $filters->getFiltersAsArray());

        if (lcfirst($valueField)=='hours') {
            $column = 'SUM(consumption_minutes) / 60 as consumption';
        } else {
            $column = 'SUM(consumption_'.lcfirst($valueField).') as consumption';
        }

        $sql = '
            SELECT
                date as date,
                '.$column.'
            FROM tbl_consumption
            INNER JOIN tbl_ethernet ON tbl_consumption.fk_ethernet=tbl_ethernet.id_ethernet
            WHERE '.implode(' AND ', $whereClause).'
            GROUP BY date
        ';

        $data =  Yii::app()->db->createCommand($sql)->queryAll();
        $treatedData = array();

        foreach ($data as &$row) {
            // Prepare final data
            if (!isset($treatedData[$row['date']])) {
                $treatedData[$row['date']] = array();
                $treatedData[$row['date']]['date'] = $row['date'];
            }
            $treatedData[$row['date']]['value'] = $row['consumption'];
        }

        // Remove the date Index
        return array_values($treatedData);
    }

    public function getWattsConsumptionByFloor(DateTime $dateFrom, DateTime $dateTo, FiltersForm $filters) {
        $whereClause = array();
        $whereClause[] = 'C.date >= \''.$dateFrom->format('Y-m-d').'\'';
        $whereClause[] = 'C.date <= \''.$dateTo->format('Y-m-d').'\'';
        $whereClause = array_merge($whereClause, $filters->getFiltersAsArray());

        $sql = '
            SELECT
                E.name,
                SUM(C.consumption_watts) as consumption
            FROM tbl_consumption C
            INNER JOIN tbl_ethernet E ON C.fk_ethernet = E.id_ethernet
            WHERE '.implode(' AND ', $whereClause).'
            GROUP BY fk_ethernet
        ';

        return Yii::app()->db->createCommand($sql)->queryAll();
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

    /**
     * This function will calculate consumptions (watts and minutes) of a device for a given date
     *
     * @param DateTime $start
     * @param DateTime $finish
     */
    public function consolidate(DateTime $start, DateTime $finish) {
        $devicesDone = array();
        $histFound = StatusHist::model()->findAllLampStatusForConsolidation($start, $finish);

        /** @var StatusHist $status */
        foreach ($histFound as $status) {
            $componentsParser = new JsonComponentsParser($status->fk_ethernet);
            /** @var DeviceTransfer $device */
            $device = $componentsParser->getDeviceByLcIdAndDvcId($status->lc_id, $status->dvc_id);
            // Ignored previously done devices
            if (in_array($device->getId(), $devicesDone)) {
                continue;
            }
            $devicesDone[] = $device->getId();
            $this->calculateConsumption($device, $start, $finish);
        }
    }

    /**
     * Were we try to figure out for how long have the lights been on
     * We need to read all the status changes for this device since the last update up until the final date
     * After that we will need to understand the consumptions for each day, and save it separately
     *
     * @param DeviceTransfer $device
     * @param DateTime $start
     * @param DateTime $finish
     */
    private function calculateConsumption(DeviceTransfer $device, DateTime $start, DateTime $finish) {
        $consumptionMinutes = array();
        $lastTimeOn = null;
        $switchedOff = null;

        $occurrences = StatusHist::model()->findAllForDeviceBetweenDates($device, $start, $finish);

        // For each found occurrence we look for the lamp status
        foreach ($occurrences as $occurrence) {
            if ($occurrence->lamp_status == 'on') {
                // If we haven't saved the last time it was on, then the lamp was off and was switched on for the 1st time
                // But if already exists, we ignore, as could be done to any status change other than the lamp status
                if ($lastTimeOn == null) {
                    $lastTimeOn = new DateTime($occurrence->created_at);
                }
            } else {
                // We caught a repeated "off" after already treated the first one. just continue
                if ($lastTimeOn == null && $switchedOff){
                    continue;
                }

                // If a lamp is switched off and no "on" recorded we assume it was already "on" from the start
                if ($lastTimeOn == null) {
                    $lastTimeOn = $start;
                }
                // When is switched off, its the time to calculate how long has been on
                $occurrenceTime = new DateTime($occurrence->created_at);

                // Get the consumption values on a daily based array
                $this->calculateConsumptionOnDailyBasis($lastTimeOn, $occurrenceTime, $consumptionMinutes);

                // Set to null to indicate it is not on anymore
                $lastTimeOn = null;
                // We have caught the switched off
                $switchedOff = true;
            }
        }

        // Save the difference of the last "on" status (if triggered)
        if ($lastTimeOn != null) {
            $this->calculateConsumptionOnDailyBasis($lastTimeOn, $finish, $consumptionMinutes);
        }

        // Save the info day by day
        foreach ($consumptionMinutes as $dateConsumption => $minutesConsumed) {
            $this->saveConsolidatedInfo($device, new DateTime($dateConsumption), $minutesConsumed);
        }

    }

    /**
     * Calculates the consumption on a daily basis, between a start Datetime and an end Datetime
     * This will be saved on the destination array provided
     *
     * @param Datetime $start
     * @param Datetime $finish
     * @param array $destinationArray
     */
    private function calculateConsumptionOnDailyBasis(Datetime $start, Datetime $finish, array &$destinationArray) {
        $days = $this->getDateDaysRange($start, $finish);
        foreach ($days as $day) {
            $currentDay = new DateTime($day);

            // Start of calculation for this day
            if ($start > $currentDay) {
                $startCalcOn = $start;
            } else {
                $startCalcOn = new DateTime($currentDay->format('Y-m-d\T00:00:00')); // This day at midnight
            }

            // End of calculation for this day
            $finishCalcOn = clone $currentDay;
            $finishCalcOn->add(new DateInterval('P1D')); // Next day at midnight
            if ($finishCalcOn > $finish) {
                $finishCalcOn = $finish;
            }

            // We now perform the calculation
            $timeDiff = $startCalcOn->diff($finishCalcOn);
            $minutes = $timeDiff->days * 24 * 60;
            $minutes += $timeDiff->h * 60;
            $minutes += $timeDiff->i;
            $minutes += ($timeDiff->s > 30 ? 1 : 0);
           if (isset($destinationArray[$day])) {
                $destinationArray[$day] += $minutes;
            } else {
                $destinationArray[$day] = $minutes;
            }
        }
    }

    /**
     * Gets an array of dates separated by days
     *
     * @param DateTime $start
     * @param DateTime $last
     * @return array
     */
    public function getDateDaysRange(DateTime $start, DateTime $last) {
        $dates = array();
        $step = '+1 day';
        $output_format = 'Y-m-d';
        $current = strtotime($start->format('Y-m-d'));
        $last = strtotime($last->format('Y-m-d'));

        while( $current <= $last ) {
            $dates[] = date($output_format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    /**
     * We save the consolidated data in here. If it exists, we just add, otherwise we create from scratch
     *
     * @param DeviceTransfer $device
     * @param DateTime $date
     * @param $consumptionMinutes
     */
    private function saveConsolidatedInfo(DeviceTransfer $device, DateTime $date, $consumptionMinutes)
    {
        // Look for existing records
        $criteria = new CDbCriteria();
        $criteria->addCondition("fk_ethernet = :fk_ethernet");
        $criteria->addCondition("lc_id = :lc_id");
        $criteria->addCondition("dvc_id = :dvc_id");
        $criteria->addCondition("date = :date");
        $criteria->params = array(
            ':date' => $date->format('Y-m-d'),
            ':fk_ethernet' => $device->ethernet_id,
            ':lc_id' => $device->lc_id,
            ':dvc_id' => $device->dvc_id
        );
        $existingRecords = Consumption::model()->findAll($criteria);
        if (empty($existingRecords)){
            $consumptionModel = new Consumption('create');
        } else {
            $consumptionModel = $existingRecords[0]; // There can be only one :)
        }

        // Prepare model for save
        $consumptionModel->date = $date->format('Y-m-d');
        $consumptionModel->fk_ethernet = $device->ethernet_id;
        $consumptionModel->lc_id = $device->lc_id;
        $consumptionModel->dvc_id = $device->dvc_id;
        $consumptionModel->type = $device->dev_type;
        $consumptionModel->fk_description = Description::getComponentDescriptionId($device);

        // Calculate Consumption
        $wattage = EnergeticClass::getWattageByClass($device->energetic_class);
        $consumptionModel->consumption_minutes += $consumptionMinutes;
        $consumptionModel->consumption_watts += round(($consumptionMinutes / 60) * $wattage);

        $consumptionModel->save();
    }

}

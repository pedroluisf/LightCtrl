<?php

/**
 * This is the model class for table "tbl_status_hist".
 *
 * The followings are the available columns in table 'tbl_status_hist' not present in 'tbl_status':
 * @property integer $id_status_hist
 */
class StatusHist extends Status
{
    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return StatusHist the static model class
     */
    public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }

    /**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'tbl_status_hist';
	}

    /**
     * Clears all entries of devices that belong to a given Ethernet
     * @param $ethernet_id
     */
    public static function clearData($ethernet_id){
        self::model()->deleteAll('fk_ethernet=:fk_ethernet', array(':fk_ethernet'=>$ethernet_id));
    }

    /**
     * Clears all entries of devices older than x days
     * @param $days
     * @return int the number of rows deleted
     */
    public static function clearDataOlderThan($days){
        return self::model()->deleteAll('DATEDIFF(NOW(), created_at) > :days', array(':days'=>$days));
    }

    /**
     * Finds all Lamps status that are 'on' at a given date, or that were turned 'off'/'failure' after a given date
     * This is used to calculate the consolidated consumption of lamps at a given point in time
     * @param DateTime $start
     * @param DateTime $finish
     * @return array|CActiveRecord|mixed|null
     */
    public function findAllLampStatusForConsolidation(DateTime $start, DateTime $finish)
    {
        $criteria = new CDbCriteria();

        $criteria->addCondition("lamp_status = 'on' AND created_at <= :date_until");
        $criteria->addCondition("lamp_status IN ('off', 'failure') AND created_at >= :date_from AND created_at <= :date_until", 'OR');
        $criteria->params = array(
            ':date_from' => $start->format('Y-m-d H:i:s'),
            ':date_until' => $finish->format('Y-m-d H:i:s')
        );

        return $this->findAll($criteria);
    }

    /**
     * Finds all occurrences for a given device within a certain time.
     *
     * @param DeviceTransfer $device
     * @param DateTime $dateTimeFrom
     * @param DateTime $dateTimeUntil
     * @return StatusHist[]|null
     */
    public function findAllForDeviceBetweenDates(DeviceTransfer $device, DateTime $dateTimeFrom, DateTime $dateTimeUntil)
    {
        $criteria = new CDbCriteria();

        $criteria->addCondition('fk_ethernet = :fk_ethernet');
        $criteria->addCondition('lc_id = :lc_id');
        $criteria->addCondition('dvc_id = :dvc_id');
        $criteria->addCondition('created_at >= :date_from');
        $criteria->addCondition('created_at <= :date_until');
        $criteria->params = array(
            ':fk_ethernet' => $device->ethernet_id,
            ':lc_id' => $device->lc_id,
            ':dvc_id' => $device->dvc_id,
            ':date_from' => $dateTimeFrom->format('Y-m-d H:i:s'),
            ':date_until' => $dateTimeUntil->format('Y-m-d H:i:s')
        );
        $criteria->order = 'created_at ASC';

        return $this->findAll($criteria);
    }

    /**
     * Finds the Aggregated consumption of the devices on an hourly based array
     * @param Datetime $date
     * @param $timeFrom
     * @param $timeTo
     * @param FiltersForm $filters
     * @return mixed
     */
    public function getHourlyConsumption(Datetime $date, $timeFrom, $timeTo, FiltersForm $filters)
    {
        $aggregatedData = array();
        $hourLimit = $this->getHourLimit($date, $timeTo);

        $consumptionData = $this->getConsumptionByDeviceHours($date, $timeFrom, $timeTo, $filters);
        foreach ($consumptionData as $info) {
            for ($i=0; $i<=$hourLimit; $i++) {
                $aggregatedData[$i]['hour'] = $i;
                $aggregatedData[$i]['value'] += $info[$i];
            }
        }

        return array_values($aggregatedData);
    }

    /**
     * Finds the consumption of the devices on an hourly based array
     * @param Datetime $date
     * @param $timeFrom
     * @param $timeTo
     * @param FiltersForm $filters
     * @return mixed
     */
    public function getHourlyConsumptionByDevice(Datetime $date, $timeFrom, $timeTo, FiltersForm $filters)
    {
        $consumptionData = $this->getConsumptionByDeviceHours($date, $timeFrom, $timeTo, $filters);
        return array_values($consumptionData);
    }

    /**
     * Finds the Aggregated consumption of the devices on an hourly based array
     * @param Datetime $date
     * @param $timeFrom
     * @param $timeTo
     * @param FiltersForm $filters
     * @return mixed
     */
    public function getConsumptionByDeviceHours(Datetime $date, $timeFrom, $timeTo, FiltersForm $filters)
    {
        $aTimeTo = explode(':', $timeTo);
        $hourLimit = $this->getHourLimit($date, $timeTo);

        $consumptionData = array();

        $devices = $this->getAllLampDeviceIdsWithOnStatus($filters);
        foreach ($devices as $device) {
            $deviceParser = new JsonComponentsParser($device['ethernet_id']);
            /** @var DeviceTransfer $deviceTransfer */
            try {
                $deviceTransfer = $deviceParser->getDeviceByLcIdAndDvcId($device['lc_id'], $device['dvc_id']);
            } catch (Exception $e) {
                // Ignore missing - invalid components
                continue;
            }

            $wattage = EnergeticClass::getWattageByClass($deviceTransfer->energetic_class);

            // Add the info of the device
            $consumptionData[$deviceTransfer->getId()]['fk_ethernet'] = $deviceTransfer->ethernet_id;
            $consumptionData[$deviceTransfer->getId()]['ethernet_name'] = $device['ethernet_name'];
            $consumptionData[$deviceTransfer->getId()]['lc_id'] = $deviceTransfer->lc_id;
            $consumptionData[$deviceTransfer->getId()]['dvc_id'] = $deviceTransfer->dvc_id;
            $consumptionData[$deviceTransfer->getId()]['description'] = $deviceTransfer->description;
            // Prepare 24 Entries (one per hour) or until hour limit
            $consumptionData[$deviceTransfer->getId()] = array_merge($consumptionData[$deviceTransfer->getId()], array_fill(0, $hourLimit+1, 0));

            // Get the previous entry for this device so we know if lamp was on or off
            $lampStatus = $this->getLampStatusBeforeDate(
                $device['ethernet_id'],
                $device['lc_id'],
                $device['dvc_id'],
                $date->format("Y-m-d $timeFrom:00")
            );

            // Get all status changes for this device
            /** @var StatusHist[] $statusData */
            $statusData =  $this->findAllForDeviceBetweenDates(
                $deviceTransfer,
                date_create($date->format("Y-m-d $timeFrom:00")),
                date_create($date->format("Y-m-d $timeTo:59"))
            );

            // If no status data was found, and lamp was on, we need to account for all the hours between start and finish
            if (empty($statusData) && $lampStatus == 'on') {
                for ($i=0; $i<=$hourLimit; $i++) {
                    $consumptionData[$deviceTransfer->getId()][$i] += $wattage;
                }
                continue;
            }

            // We use this to know what we have calculated so far
            $lastCalculatedDate = new DateTime($date->format("Y-m-d $timeFrom:00"));

            // Go through all status data and add as we go
            foreach ($statusData as $data) {
                // We only add if lamp is "on", so ignore everything else
                if ($lampStatus == 'on') {
                    // Break it down in hourly entries
                    for ($i=(int)$lastCalculatedDate->format('H'); $i<=$hourLimit; $i++) {
                        $dataDate = new DateTime($data->created_at);
                        if ($i > $dataDate->format('H')) {
                            break;
                        }
                        $hour = sprintf('%02d', $i);
                        if ($data->created_at > $dataDate->format("Y-m-d $hour:59:59")) {
                            $dataDate->setTime($hour, 59, 59);
                        }
                        $dateDiff = $lastCalculatedDate->diff($dataDate);
                        $consumptionData[$deviceTransfer->getId()][$i] += ($dateDiff->i / 60 * $wattage);
                    }
                }
                $lampStatus = $data->lamp_status;
                $lastCalculatedDate = new DateTime($data->created_at);
            }

            // Add the rest of the consumption for the day until the time limit
            if ($lampStatus == 'on') {
                $hourStart = ((int)$lastCalculatedDate->format('H'));
                for ($i=$hourStart; $i<=$hourLimit; $i++) {
                    $hour = sprintf('%02d', $i);
                    if ($i > $aTimeTo[0]) {
                        break;
                    }
                    $date = clone $lastCalculatedDate;
                    if ($timeTo < "$hour:59") {
                        $date->setTime($aTimeTo[0], $hourLimit, 59);
                    } else {
                        $date->setTime($hour, 59, 59);
                    }
                    $dateDiff = $lastCalculatedDate->diff($date);
                    $consumptionData[$deviceTransfer->getId()][$i] += (($dateDiff->h + ($dateDiff->i / 60)) * $wattage);
                    $lastCalculatedDate = $date;
                }
            }
        }

        return $consumptionData;
    }

    private function getAllLampDeviceIdsWithOnStatus(FiltersForm $filters)
    {
        $whereClause = array();
        $whereClause[] = 'type IN ('.implode(',', array_keys(Dictionary::getLampTypes())).')';
        $whereClause[] = 'lamp_status = \'on\'';
        $whereClause = array_merge($whereClause, $filters->getFiltersAsArray());

        $sql = '
            SELECT
              S.fk_ethernet as ethernet_id,
              E.name as ethernet_name,
              lc_id,
              dvc_id
            FROM tbl_status_hist S
            INNER JOIN tbl_ethernet E ON S.fk_ethernet=E.id_ethernet
            INNER JOIN tbl_area A ON E.fk_area=A.id_area
            WHERE '.implode(' AND ', $whereClause).'
            GROUP BY fk_ethernet, lc_id, dvc_id
        ';

        return Yii::app()->db->createCommand($sql)->queryAll();
    }

    /**
     * Gets the last know status of a lamp before a given date
     * @param int $fk_ethernet
     * @param int $lc_id
     * @param int $dvc_id
     * @param string $date
     * @return CActiveRecord[]
     */
    private function getLampStatusBeforeDate($fk_ethernet, $lc_id, $dvc_id, $date) {
        $whereClause = array();
        $whereClause[] = 'fk_ethernet = '.$fk_ethernet;
        $whereClause[] = 'lc_id = '.$lc_id;
        $whereClause[] = 'dvc_id = '.$dvc_id;
        $whereClause[] = "created_at < '".$date."'";

        $status = Yii::app()->db
            ->createCommand(
                'SELECT lamp_status
                  FROM '.$this->tableName().'
                  WHERE '.implode(' AND ', $whereClause).'
                  ORDER BY created_at DESC
                  LIMIT 1'
            )
            ->queryScalar();
        if ($status == null) {
            $status = 'off';
        }

        return $status;
    }

    private function getHourLimit(Datetime $date, $timeTo) {
        $aTimeTo = explode(':', $timeTo);
        $currentDate = new DateTime();

        if ($date->format('Y-m-d') == $currentDate->format('Y-m-d')
        && $aTimeTo[0] > $currentDate->format('H')) {
            return ((int)$currentDate->format('H')) + 1;
        } else {
            if ($aTimeTo[1] == 0) {
                return $aTimeTo[0];
            } else {
                return ((int)$aTimeTo[0]) + 1;
            }
        }
    }
}

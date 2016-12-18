<?php

/**
 * This is the model class for requesting a executed emergency report
 * Used to retrieve a diferent search method
 */
class ExecutedEmergency extends EmergencyHist
{
    /** @var ExecutedSchedule $executedSchedule */
    protected $executedSchedule;

	public function setExecutedSchedule(ExecutedSchedule $executedSchedule)
	{
		$this->executedSchedule = $executedSchedule;
	}

    public function getReportFields(){
        $fields = parent::getReportFields();
        unset($fields[array_search('test_type', $fields)]);
        return $fields;
    }

    public function search($pagination=true)
    {
        $criteria=new CDbCriteria;
        $criteria->with = array( 'ethernet' );

        // Filter by the devices of the executed Schedule
        $criteria->compare('fk_ethernet', $this->executedSchedule->fk_ethernet);
        $response = $this->executedSchedule->getDevicesExecuted();
        $devicesCriteria = new CDbCriteria();
        if (!empty($response)) {
            foreach ($response as $lc_id => $dvc_ids) {
                $tempCriteria = new CDbCriteria();
                $tempCriteria->addCondition("lc_id=$lc_id");
                $tempCriteria->addInCondition('dvc_id', $dvc_ids);
                $devicesCriteria->mergeWith($tempCriteria, 'OR');
            }
            $criteria->mergeWith($devicesCriteria);
        }

        $criteria->compare('test_type', $this->executedSchedule->type);
        $criteria->addCondition('created_at >= \''.$this->executedSchedule->start_date.'\'');
        $criteria->addCondition('created_at <= \''.$this->executedSchedule->end_date.'\'');

        // Merge normal filters
        $criteria->mergeWith($this->getSearchCriteria());

        // Return data
        $config = array(
            'criteria'=>$criteria,
            'sort'=>array(
                'defaultOrder'=>'fk_ethernet, lc_id, dvc_id ASC',
                'multiSort'=>true,
            )
        );
        if ($pagination){
            $config['pagination'] = array(
                'pageSize'=> Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']),
            );
        } else {
            $config['pagination']=false;
        }

        return new CActiveDataProvider($this, $config);
    }

}

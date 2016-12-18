<?php
/* @var $this scheduleController */
/* @var $model CommandSchedule */

$this->breadcrumbs=array(
    'Scheduling'=>array('index'),
    'View',
);

$this->menu=$this->buildCommandScheduleMenu($model->id_schedule);

?>

<h1>View Schedule #<?php echo $model->id_schedule; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
    'data'=>$model,
    'attributes'=>array_filter(array(
        'id_schedule',
        'description',
        array(
            'name'=>'fk_area',
            'value'=>CHtml::encode($model->area->name),
        ),
        array(
            'name'=>'fk_ethernet',
            'value'=>CHtml::encode($model->ethernet->name),
        ),
        'lc_id',
        'dvc_id',
        'cci_sw_num',
        array(
            'name'=>'group',
            'filter'=>array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12,13=>13,14=>14,15=>15),
        ),
        array(
            'name'=>'type',
            'value'=>CHtml::encode($model->getTypeForDisplay()),
        ),
        ($model->type == 'normal' ? 'cci_data' : null),
        array(
            'name'=>'start_date',
            'value'=>CHtml::encode($model->getNextDateForScheduler()),
        ),
        array(
            'name'=>'event_time',
            'value'=>CHtml::encode($model->getTimeForDisplay()),
        ),
        array(
            'name'=>'periodicity',
            'value'=>CHtml::encode($model->getPeriodicityForDisplay()),
        ),
        ($model->periodicity == 'monthly' ? array(
            'name'=>'month_repeat',
            'value'=>CHtml::encode($model->getMonthRepeatForDisplay()),
        ) : null),
        ($model->periodicity == 'weekly' ? array(
            'label'=>'Weekdays',
            'value'=>CHtml::encode($model->getWeekDaysForDisplay()),
        ) : null),
        array(
            'name'=>'fk_user',
            'value'=>CHtml::encode($model->user->username),
        ),
        'created_at'
    )),
)); ?>

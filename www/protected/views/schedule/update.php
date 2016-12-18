<?php
/* @var $this scheduleController */
/* @var $model CommandSchedule */

$this->breadcrumbs=array(
    'Scheduling'=>array('index'),
    'Update',
);

?>

<div id="action_bar">
    <?php
    $list = CHtml::listData(Area::model()->findAll(),'id_area', 'name');
    echo CHtml::dropDownList('area_select', Area::model()->id_area, $list,
        array(
            'empty' => '(Select an Area)',
        )
    );
    ?>
    <div id="properties"></div>
    <div id= "treeView_container" class="mtm">
        <?php
            // Dummy Tree for including libraries
            $this->widget('CTreeView');
        ?>
    </div>
</div>

<div id="schedule_container">

<?php $this->renderPartial(
        '_formSchedule',
        array(
            'title'=>'Update Scheduled Command',
            'model'=>$model,
            'types' => CommandSchedule::getCommandTypesList(),
            'repeatOptions' => CommandSchedule::getMonthRepeatOptionsList(),
            'periodicityOptions' => CommandSchedule::getPeriodicityOptionsList(),
            'cciData' => CommandSchedule::getClearContactDataOptionsList(),
            'groups' => CommandSchedule::getGroupsList(),
        )
    );
?>
</div>

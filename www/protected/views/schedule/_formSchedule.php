<?php
/* @var $this ScheduleController */
/* @var $model CommandSchedule */
/* @var $form ScheduleForm */

$weeklyFieldsState = ($model->periodicity == CommandSchedule::WEEKLY_COMMAND) ? '' : 'disabled';
$monthlyFieldsState = ($model->periodicity == CommandSchedule::MONTHLY_COMMAND) ? '' : 'disabled';

$mondayState = ($model->monday === '0') ? '' : 'checked';
$tuesdayState = ($model->tuesday === '0') ? '' : 'checked';
$wednesdayState = ($model->wednesday === '0') ? '' : 'checked';
$thursdayState = ($model->thursday === '0') ? '' : 'checked';
$fridayState = ($model->friday === '0') ? '' : 'checked';
$saturdayState = ($model->saturday === '0') ? '' : 'checked';
$sundayState = ($model->sunday === '0') ? '' : 'checked';
?>

<div id="statusMsg"></div>
<h1><?php echo $title; ?></h1>

<div class="form form-flex">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'schedule-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

<?php // HIDDEN FIELDS
    echo $form->hiddenField($model, 'fk_user', array('value' => Yii::app()->user->id));
    echo $form->hiddenField($model, 'fk_area');
    echo $form->hiddenField($model, 'fk_ethernet');
    echo $form->hiddenField($model, 'draw_id');
    echo $form->hiddenField($model, 'dvc_id');
    echo $form->hiddenField($model, 'cci_sw_num');
?>
    <div class="span-19">
        <div class="row date_time_row">
            <div>
                <?php echo $form->labelEx($model,'start_date',array('class'=>"form_date_label")); ?>
                <?php echo $form->textField($model,'start_date',array('size'=>20,'maxlength'=>19)); ?>
                <?php
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model' => $model,
                            'attribute' => 'start_date',
                            'options'=>array(
                                'dateFormat'=>'yy-mm-dd',
                                'changeYear'=>'true',
                                'changeMonth'=>'true',
                                'showAnim' =>'slide',
                                'yearRange'=>'1900:'.(date('Y')+1)
                            ),
                            'htmlOptions' => array('style' => 'width : 80px; text-align:center;')
                        ),
                        true); ?>
                <?php echo $form->error($model,'start_date'); ?>
            </div>
            <div>
                <?php echo $form->labelEx($model,'event_time',array('class'=>"form_date_label")); ?>
                <?php
                    $this->widget('CMaskedTextField', array(
                        'model' => $model,
                        'attribute' => 'event_time',
                        'mask' => '99:99',
                        'htmlOptions' => array('style' => 'width : 40px;')
                    ));
                ?>
                <?php echo $form->error($model,'event_time'); ?>
            </div>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'description'); ?>
            <?php echo $form->textField($model,'description',array('size'=>60,'maxlength'=>256)); ?>
            <?php echo $form->error($model,'description'); ?>
        </div>

        <div class="row">
            <?php echo $form->labelEx($model,'periodicity'); ?>
            <?php echo $form->radioButtonList($model,'periodicity',
                    $periodicityOptions,
                    array('onchange' => 'periodicityChange(this.value);')
                );
            ?>
            <?php echo $form->error($model,'menuType'); ?>

        </div>

        <div class="row form_week_days">
            <?php echo $form->checkBox($model,'monday',  array('checked'=>$mondayState, 'disabled' => $weeklyFieldsState)); ?>
            <?php echo $form->labelEx($model,'monday'); ?>
            <?php echo $form->error($model,'monday'); ?>

            <?php echo $form->checkBox($model,'tuesday',  array('checked'=>$tuesdayState, 'disabled' => $weeklyFieldsState)); ?>
            <?php echo $form->labelEx($model,'tuesday'); ?>
            <?php echo $form->error($model,'tuesday'); ?>

            <?php echo $form->checkBox($model,'wednesday',  array('checked'=>$wednesdayState, 'disabled' => $weeklyFieldsState)); ?>
            <?php echo $form->labelEx($model,'wednesday'); ?>
            <?php echo $form->error($model,'wednesday'); ?>

            <?php echo $form->checkBox($model,'thursday',  array('checked'=>$thursdayState, 'disabled' => $weeklyFieldsState)); ?>
            <?php echo $form->labelEx($model,'thursday'); ?>
            <?php echo $form->error($model,'thursday'); ?>

            <?php echo $form->checkBox($model,'friday',  array('checked'=>$fridayState, 'disabled' => $weeklyFieldsState)); ?>
            <?php echo $form->labelEx($model,'friday'); ?>
            <?php echo $form->error($model,'friday'); ?>

            <?php echo $form->checkBox($model,'saturday',  array('checked'=>$saturdayState, 'disabled' => $weeklyFieldsState)); ?>
            <?php echo $form->labelEx($model,'saturday'); ?>
            <?php echo $form->error($model,'saturday'); ?>

            <?php echo $form->checkBox($model,'sunday',  array('checked'=>$sundayState, 'disabled' => $weeklyFieldsState)); ?>
            <?php echo $form->labelEx($model,'sunday'); ?>
            <?php echo $form->error($model,'sunday'); ?>
        </div>

        <div class="row form_month_repeat">
            <?php echo $form->labelEx($model,'month_repeat'); ?>
            <?php echo $form->dropDownList(
                $model,
                'month_repeat',
                $repeatOptions,
                array(
                    'options' => array(
                        $model->month_repeat => array('selected'=>true)
                    ),
                    'prompt'=>'select repetition pattern',
                    'disabled' => $monthlyFieldsState
                )
            ); ?>
            <?php echo $form->error($model,'month_repeat'); ?>
        </div>

    </div>
    <div class="span-6 last">
        <div class="row">
            <?php echo $form->labelEx($model,'type'); ?>
            <?php echo $form->dropDownList($model,'type', $types, array('prompt'=>'select type', 'id' => 'CommandSchedule_type')); ?>
            <?php echo $form->error($model,'type'); ?>
        </div>
        <div class="row CommandSchedule_cci_data <?php echo ($model->type != 'normal') ? 'form-hidden': ''; ?>">
            <?php echo $form->labelEx($model,'cci_data'); ?>
            <?php echo $form->dropDownList($model,'cci_data', $cciData, array('prompt'=>'select action')); ?>
            <?php echo $form->error($model,'cci_data'); ?>
        </div>
        <div class="row CommandSchedule_group <?php echo ($model->group != 'function' && $model->group != 'duration') ? 'form-hidden': ''; ?>">
            <?php echo $form->labelEx($model,'group'); ?>
            <?php echo $form->dropDownList($model,'group', $groups, array('prompt'=>'All')); ?>
            <?php echo $form->error($model,'group'); ?>
        </div>
        <div class="row CommandSchedule_priority">
            <?php echo $form->labelEx($model,'priority'); ?>
            <?php echo $form->dropDownList($model,'priority', array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10)); ?>
            <?php echo $form->error($model,'priority'); ?>
            <br/><span class="hint">Priority is used when more than one schedule is set at the same time for the same component/group</span>
            <br/><span class="hint">(Highest priority will prevail)</span>
        </div>
        <br/>
        <div class="row">
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('id' => 'CommandSchedule_submit')); ?>
        </div>
    </div>

<?php $this->endWidget(); ?>

</div><!-- form -->
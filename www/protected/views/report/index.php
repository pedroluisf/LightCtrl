<?php
/* @var $this ReportController */

$this->breadcrumbs=array(
	'Report',
);

?>

<h1>System Reports</h1>

<h3>Choose bellow the report type</h3>

<div class="report_select">
    <div class="button_column">
        <a href="<?php echo Yii::app()->getBaseUrl()?>/report/currentStatus"><div class="ui_button report_select_button report_button_status">Current Status</div></a>
        <a href="<?php echo Yii::app()->getBaseUrl()?>/report/emergencyCurrent"><div class="ui_button report_select_button report_button_emergency_current">Emergency</div></a>
    </div>
    <div class="button_column">
        <a href="<?php echo Yii::app()->getBaseUrl()?>/report/status"><div class="ui_button report_select_button report_button_status_hist">Status Hist</div></a>
        <a href="<?php echo Yii::app()->getBaseUrl()?>/report/emergency"><div class="ui_button report_select_button report_button_emergency">Emergency Hist</div></a>
    </div>
    <div class="button_column">
        <a href="<?php echo Yii::app()->getBaseUrl()?>/report/failure"><div class="ui_button report_select_button report_button_failure">Failures</div></a>
        <a href="<?php echo Yii::app()->getBaseUrl()?>/report/emergencyScheduledList"><div class="ui_button report_select_button report_button_emergency_scheduled">Scheduled Emerg.</div></a>
    </div>
</div>
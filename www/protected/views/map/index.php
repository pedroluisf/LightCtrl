<?php
/* @var $this MapController */

$this->breadcrumbs=array(
	'Map',
);

?>

<h1>Consumption Maps</h1>

<h3>Choose bellow the Consumption Map to display</h3>

<div class="report_select" style="max-width:340px;">
    <div class="button_column">
        <a href="<?php echo Yii::app()->getBaseUrl()?>/map/dailyHours"><div class="ui_button report_select_button map_button_daily_hours">Hours / Daily</div></a>
        <a href="<?php echo Yii::app()->getBaseUrl()?>/map/dailyWatts"><div class="ui_button report_select_button map_button_daily_watts">Watts / Daily</div></a>
    </div>
    <div class="button_column">
        <a href="<?php echo Yii::app()->getBaseUrl()?>/map/hourlyWatts"><div class="ui_button report_select_button map_button_hourly_watts">Watts / Hour</div></a>
        <a href="<?php echo Yii::app()->getBaseUrl()?>/map/wattsFloor"><div class="ui_button report_select_button map_button_watts_floor">Watts / Floor</div></a>
    </div>
</div>
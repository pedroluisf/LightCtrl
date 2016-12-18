<?php
/* @var $this ConfigurationController */
/* @var $model Area */

$this->breadcrumbs=array(
	'Areas'=>array('index'),
	'Create',
);

$this->menu=$this->buildConfigurationMenu();
?>

<h1>Create Building Area</h1>

<?php $this->renderPartial('area/_form', array('model'=>$model)); ?>
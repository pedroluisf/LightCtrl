<?php
/* @var $this ConfigurationController */
/* @var $model Ethernet */

$this->breadcrumbs=array(
	'Ethernet Interfaces'=>array('ethernet'),
	'Create',
);

$this->menu=$this->buildConfigurationMenu();
?>

<h1>Create Ethernet Interface</h1>

<?php $this->renderPartial('ethernet/_form', array('model'=>$model)); ?>
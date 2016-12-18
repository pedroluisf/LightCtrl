<?php
/* @var $this ConfigurationController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Areas',
);

$this->menu=$this->buildConfigurationMenu();
?>

<h1>Building Areas</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'area/_view',
)); ?>

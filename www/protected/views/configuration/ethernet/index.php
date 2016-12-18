<?php
/* @var $this ConfigurationController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Ethernet Interfaces',
);

$this->menu=$this->buildConfigurationMenu();
?>

<h1>Ethernet Interfaces</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'ethernet/_view',
)); ?>

<?php
/* @var $this EnergeticClassController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
	'Energetic Classes',
);

$this->menu=$this->buildConfigurationMenu();
?>

<h1>Energetic Classes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>

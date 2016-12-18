<?php
/* @var $this ConfigurationController */
/* @var $model Ethernet */

$this->breadcrumbs=array(
	'Ethernet Interfaces'=>array('ethernet'),
	$model->name,
);

$this->menu=$this->buildConfigurationMenu(array('id_ethernet' => $model->id_ethernet));
?>



<h1>View Ethernet #<?php echo $model->id_ethernet; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id_ethernet',
		'name',
		'desc',
		'host',
        array(
            'name'=>'fk_area',
            'value'=>CHtml::encode($model->area->name),
        ),
        array(
            'name'=>'status',
            'value'=>CHtml::encode($model->translated_inactive),
        )
	),
)); ?>

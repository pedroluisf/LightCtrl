<?php
/* @var $this EnergeticClassController */
/* @var $model EnergeticClass */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
	'Energetic Classes'=>array('admin'),
	$model->id_energetic_class,
);

$this->menu=$this->buildConfigurationMenu($model);
?>

<h1>View Energetic Class #<?php echo $model->id_energetic_class; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id_energetic_class',
        'class_key',
		'description',
		'consumption_watts',
		'created_at',
	),
)); ?>

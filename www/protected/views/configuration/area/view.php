<?php
/* @var $this ConfigurationController */
/* @var $model Area */

$this->breadcrumbs=array(
	'Areas'=>array('area'),
	$model->name,
);

$this->menu=$this->buildConfigurationMenu(array('id_area' => $model->id_area));
?>

<h1>View Building Area #<?php echo $model->id_area; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id_area',
		'name',
		'desc',
		'plan',
	),
)); ?>

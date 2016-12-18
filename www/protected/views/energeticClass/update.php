<?php
/* @var $this EnergeticClassController */
/* @var $model EnergeticClass */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
	'Energetic Classes'=>array('admin'),
	$model->id_energetic_class=>array('view','id'=>$model->id_energetic_class),
	'Update',
);

$this->menu=$this->buildConfigurationMenu($model);
?>

<h1>Update Energetic Class <?php echo $model->id_energetic_class; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>
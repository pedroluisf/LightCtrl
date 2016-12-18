<?php
/* @var $this EnergeticClassController */
/* @var $model EnergeticClass */

$this->breadcrumbs=array(
    'Configuration'=>array('configuration/index'),
	'Energetic Classes'=>array('admin'),
	'Create',
);

$this->menu=$this->buildConfigurationMenu($model);
?>

<h1>Create Energetic Class</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>
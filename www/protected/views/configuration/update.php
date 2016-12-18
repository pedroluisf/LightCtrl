<?php
/* @var $this ConfigurationController */
/* @var $model Configuration */

$this->breadcrumbs=array(
	'Configuration'=>array('index'),
	'Update',
);

$this->menu=$this->buildConfigurationMenu(array('id_configuration' => $model->id_configuration));
?>

<h1>Update <?php echo $model->label; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>
<?php
/* @var $this ConfigurationController */
/* @var $model Area */

$this->breadcrumbs=array(
	'Areas'=>array('area'),
	$model->name=>array('areaview','id'=>$model->id_area),
	'Update',
);

$this->menu=$this->buildConfigurationMenu(array('id_area' => $model->id_area));
?>

<h1>Update Building Area <?php echo $model->id_area; ?></h1>

<?php $this->renderPartial('area/_form', array('model'=>$model)); ?>
<?php
/* @var $this ConfigurationController */
/* @var $model Ethernet */

$this->breadcrumbs=array(
	'Ethernet Interfaces'=>array('ethernet'),
	$model->name=>array('ethernetview','id'=>$model->id_ethernet),
	'Update',
);

$this->menu=$this->buildConfigurationMenu(array('id_ethernet' => $model->id_ethernet));
?>

<h1>Update Ethernet <?php echo $model->id_ethernet; ?></h1>

<?php $this->renderPartial('ethernet/_form', array('model'=>$model)); ?>
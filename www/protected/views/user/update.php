<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	$model->id_user=>array('view','id'=>$model->id_user),
	'Update',
);

$this->menu=$this->buildUserMenu($model->id_user);
?>

<h1>Update User <?php echo $model->id_user; ?></h1>

<?php $this->renderPartial('_formUpdate', array('model'=>$model)); ?>
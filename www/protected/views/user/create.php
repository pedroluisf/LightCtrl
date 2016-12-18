<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Create',
);

?>

<h1>Create User</h1>

<?php $this->renderPartial('_formCreate', array('model'=>$model)); ?>
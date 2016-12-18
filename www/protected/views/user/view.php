<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('view'),
	$model->id_user,
);

$this->menu=$this->buildUserMenu($model->id_user);
?>

<h1>View User #<?php echo $model->id_user .' "'.$model->username.'"'; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id_user',
		'username',
        array(
            'label'=>'Role',
            'type'=>'raw',
            'value'=>CHtml::encode($model->role->name),
        ),
        'email',
		'first_name',
		'last_name',
	),
)); ?>

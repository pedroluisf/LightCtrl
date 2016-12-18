<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Change Role',
);

?>

<h1>Change Role</h1>
<h2>User "<?php echo $model->user->username?>"</h2>

<?php $this->renderPartial('_formRole', array('model'=>$model)); ?>
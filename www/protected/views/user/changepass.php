<?php
/* @var $this UserController */
/* @var $model User */

$this->breadcrumbs=array(
	'Users'=>array('index'),
	'Change Pass',
);

?>

<h1>Change Password</h1>
<h2>User "<?php echo $model->user->username?>"</h2>

<?php $this->renderPartial('_formPass', array('model'=>$model)); ?>
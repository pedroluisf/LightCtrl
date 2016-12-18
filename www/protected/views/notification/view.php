<?php
/* @var $this NotificationController */
/* @var $model Notification */

$this->breadcrumbs=array(
	'Notifications'=>array('index'),
	$model->id_notification,
);
$this->menu=$this->buildNotificationsMenu($model->id_notification);

?>

<h1>View Notification #<?php echo $model->id_notification; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id_notification',
        array(
            'name'=>'fk_user',
            'value'=>$model->user->username
        ),
		'level',
        array(
            'name'=>'message',
            'type'=>'raw',
            'value'=>str_replace(PHP_EOL, "<br/>", $model->message)
        )
	),
)); ?>

<?php
/* @var $this NotificationController */
/* @var $model Notification */

$this->breadcrumbs=array(
	'Notifications',
);

$this->menu=$this->buildNotificationsMenu();

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#notification-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Notifications</h1>

<p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
    <?php $this->renderPartial('_search',array(
        'model'=>$model,
    )); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'notification-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'columns'=>array_filter(array(
        array(
            'name' => 'check',
            'id' => 'selectedIds',
            'value' => '$data->id_notification',
            'class' => 'CCheckBoxColumn',
            'selectableRows' => '100',
        ),
        array(
            'type'=>'raw',
            'htmlOptions' => array('style' => 'width: 50px; text-align: center;'),
            'name'=>'new',
            'value'=>'($data->new ? \'<img src="\' . Yii::app()->request->baseUrl . \'/themes/intsys/images/notification_unread.png"/>\' : \'<img src="\' . Yii::app()->request->baseUrl . \'/themes/intsys/images/notification_read.png"/>\' )',
            'filter'=>array(1=>'Yes',0=>'No')
        ),
        array(
            'htmlOptions' => array('style' => 'width: 80px; text-align: center;'),
            'name'=>'id_notification',
        ),
        array(
            'htmlOptions' => array('style' => 'width: 100px; text-align: center;'),
            'name'=>'level',
            'filter'=>array('Info'=>'Info','Warning'=>'Warning','Error'=>'Error')
        ),
        ($showUser ?
            array(
                'name'=>'fk_user',
                'htmlOptions' => array('style' => 'width: 200px;'),
                'value'=>'$data->user->username',
                'filter'=>CHtml::listData(User::model()->findAll(), 'id_user', 'username'),
            ) :
            null
        ),
        'message',
        array(
            'class'=>'CButtonColumn',
            'template'=>'{view}{delete}{mark_as_read}',
            'buttons' => array(
                'view' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "notification/view",
                        array("id" => "$data->id_notification"))'
                ),
                'delete' => array(
                    'url' => '$this->grid->controller->createUrl(
                            "notification/delete",
                            array("id" => "$data->id_notification"))'
                ),
                'mark_as_read' => array(
                    'label' => 'Mark as Read',
                    'url' => '$this->grid->controller->createUrl(
                        "notification/view",
                        array("id" => "$data->id_notification"))',
                    'imageUrl' => Yii::app()->request->baseUrl . '/themes/intsys/images/notification_mark_as_read.png',
                    'options' => array( 'onclick' => '
                            event.preventDefault();
                            if (confirm("Are you sure you want to mark this notification as read?")) {
                                $.ajax({
                                    type:"get",
                                    url:$(this).attr("href"),
                                    success:function(data) {
                                        $.fn.yiiGridView.update("notification-grid");
                                    }
                                });
                            }'
                    )
                )
            )
        ),
    )),
)); ?>

<div >

    <p>With Selected:

    <?php
    echo CHtml::ajaxButton("Mark as Read", $this->createUrl('notification/viewMany'),
        array(
            'type' => 'post',
            'data' => 'js:{
                YII_CSRF_TOKEN : "' . Yii::app()->request->csrfToken . '",
                ids : $.fn.yiiGridView.getChecked("notification-grid","selectedIds").toString()
            }',
            'success' => 'js:function(data){ $.fn.yiiGridView.update("notification-grid")  }'
        ),
        array(
            'confirm'=>'Are you sure you want to mark as read all selected notifications?'
        )
    );

    echo CHtml::ajaxButton("Delete", $this->createUrl('notification/deleteMany'),
        array(
            'type' => 'post',
            'data' => 'js:{
                YII_CSRF_TOKEN : "' . Yii::app()->request->csrfToken . '",
                ids : $.fn.yiiGridView.getChecked("notification-grid","selectedIds").toString()
            }',
            'success' => 'js:function(data){ $.fn.yiiGridView.update("notification-grid")  }'
        ),
        array(
            'class' => 'mlm',
            'confirm'=>'Are you sure you want to delete all selected notifications?'
        )
    );
    ?>
    </p>
</div>


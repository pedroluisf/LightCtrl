<?php
/* @var $this ReportController */
/* @var $model ExecutedSchedule */

$this->breadcrumbs=array(
	'Report'=>array('/report'),
    'Emergency Scheduled'
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']);
?>

<h1>List of Emergency Scheduled Tests</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php
    $this->renderPartial('/partial/_clearSort',
        array(
            'gridId'=>'report-grid',
            'url'=>'emergency',
            'filters'=>'Emergency'
        )
    )
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'report-grid',
    'dataProvider'=>$model->searchEmergencyScheduled(),
    'filter'=>$model,
    'ajaxUpdate'=>'grid-clear-sorting',
    'afterAjaxUpdate' => 'reinstallDatePicker', //call function to reinstall date picker
    'columns'=>array(
        'description',
        array(
            'name'=>'fk_area',
            'value'=> '$data->area->name',
            'filter'=>CHtml::listData(Area::model()->findAll(), 'id_area', 'name'),
        ),
        array(
            'name'=>'fk_ethernet',
            'value'=> '$data->ethernet->name',
            'filter'=>CHtml::listData(Ethernet::model()->findAll(), 'id_ethernet', 'name'),
        ),
        'lc_id',
        'dvc_id',
        array(
            'name'=>'group',
            'value'=>'$data->getGroupForDisplay()',
            'filter'=>array(0=>1,1=>2,2=>3,3=>4,4=>5,5=>6,6=>7,7=>8,8=>9,9=>10,10=>11,11=>12,12=>13,13=>14,14=>15),
        ),
        array(
            'name'=>'start_date',
            'filter'=>
                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'start_date',
                        'options'=>array(
                            'dateFormat'=>'yy-mm-dd',
                            'changeYear'=>'true',
                            'changeMonth'=>'true',
                            'showAnim' =>'slide',
                            'yearRange'=>'1900:'.(date('Y')+1)
                        ),
                        'htmlOptions'=>array(
                            'id'=>'date',
                        ),
                    ),
                    true),
        ),
        array(
            'name'=>'fk_user',
            'header' => 'Executed By',
            'value'=>'$data->user->username',
            'filter'=>CHtml::listData(User::model()->findAll(), 'id_user', 'username'),
        ),
        array(
            'name' => 'manual_trigger',
            'header' => 'Executed Manually?',
            'value'=>'($data->manual_trigger ? "Yes" : "No")',
            'filter'=>array(0=>"No",1=>"Yes")
        ),
        array(
            'type'=>'raw',
            'name'=>'command_status',
            'value'=>'$data->getCommandStatusToGrid()',
            'htmlOptions'=>array(
                'width'=>'40px',
                'style' => 'text-align: center;'),
            'filter'=>array(
                'pending' => 'Pending',
                'failed' => 'Failed',
                'error' => 'Error',
                'finished' => 'Success',
            )
        ),
        array(
            'class'=>'CButtonColumn',
            'template'=>'{Report}',
            'buttons' => array(
                'Report' => array(
                    'url' => '$this->grid->controller->createUrl(
                        "report/emergencyScheduled",
                        array("id_exec_schedule" => "$data->id_exec_schedule"))',
                    'imageUrl' => Yii::app()->request->baseUrl . '/themes/intsys/images/preview.png'
                )
            )
        ),
    ),
)); ?>
<div class="pager pager_sizer">
    Number of items per page:
    <?php
        echo CHtml::dropDownList('pageSize',$pageSize,array(10=>10,20=>20,50=>50,100=>100),array(
            'onchange'=>"$.fn.yiiGridView.update('report-grid',{ data:{pageSize: $(this).val() }})",
        ));
    ?>
</div>

<?php
    $this->renderPartial('_reinstalCalendarFilter');
?>
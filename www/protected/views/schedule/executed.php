<?php
/* @var $this scheduleController */
/* @var $model ExecutedSchedule */

$this->breadcrumbs=array(
    'Scheduling'=>array('index'),
    'Executed',
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']);

$this->menu=$this->buildCommandScheduleMenu();
?>

<div id="statusMsg"></div>
<h1>Completed Tasks</h1>

<?php
$this->renderPartial('/partial/_clearSort',
    array(
        'gridId'=>'schedule-grid',
        'url'=> $url = Yii::app()->createUrl('schedule/executed'),
        'filters'=>'ExecutedSchedule'
    )
);
?>
<?php
$this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'schedule-grid',
        'dataProvider'=>$model->search(),
        'filter'=>$model,
        'ajaxUpdate'=>'grid-clear-sorting',
        'afterAjaxUpdate' => 'reinstallDatePicker', //call function to reinstall date picker
        'columns'=>array(
            array(
                'name'=>'fk_area',
                'value'=>'$data->area->name',
                'filter'=>CHtml::listData(Area::model()->findAll(), 'id_area', 'name'),
            ),
            array(
                'name'=>'fk_ethernet',
                'value'=>'$data->ethernet->name',
                'filter'=>CHtml::listData(Ethernet::model()->findAll(), 'id_ethernet', 'name'),
            ),
            'lc_id',
            'dvc_id',
            'cci_sw_num',
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
                                'yearRange'=>'1900:'.(date('Y')+1),
                            ),
                            'htmlOptions'=>array(
                                'id'=>'start_date',
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
            'description',
            array(
                'name'=>'type',
                'value'=>'$data->getTypeForDisplay()',
                'filter'=>CommandSchedule::model()->getCommandTypesList()
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
                        'visible'=>'$data->commandQueue->status != "error"
                                    && $data->commandQueue->status != "failed"
                                    && $data->commandQueue->status != "pending"
                                    && $data->commandQueue->status != "processing"
                                    && $data->type != "normal"
                                    && $data->end_date < date("Y-m-d H:i:00")',
                        'url' => '$this->grid->controller->createUrl(
                        "report/emergencyScheduled",
                        array("id_exec_schedule" => "$data->id_exec_schedule"))',
                        'imageUrl' => Yii::app()->request->baseUrl . '/themes/intsys/images/preview.png'
                    )
                )
            ),
            array(
                'name'=>'fk_ethernet',
                'filter'=>false,
                'headerHtmlOptions'=>array('style' => 'display: none;'),
                'htmlOptions'=>array('style' => 'display: none;'),
            ),
        ),
    )
);

?>
<div class="pager pager_sizer">
    Number of items per page:
    <?php
        echo CHtml::dropDownList('pageSize',$pageSize,array(10=>10,20=>20,50=>50,100=>100),array(
            'onchange'=>"$.fn.yiiGridView.update('schedule-grid',{ data:{pageSize: $(this).val() }})",
        ));
    ?>
</div>

<script type="text/javascript">
    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
</script>

<?php $this->renderPartial('_reinstalCalendarFilter'); ?>
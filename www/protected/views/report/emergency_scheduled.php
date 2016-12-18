<?php
/* @var $this ReportController */
/* @var $model ExecutedEmergency */
/* @var $execScheduled ExecutedSchedule */
$this->breadcrumbs=array(
	'Report'=>array('/report'),
    'EmergencyScheduled'
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']);
?>

<h1>Scheduled Emergency Report</h1>

<h4><?php echo $execScheduled->description; ?></h4>
<p>
    <b>Periodicity:</b> <?php echo $execScheduled->getPeriodicityForDisplay(); ?><br/>
    <b>Start Date:</b> <?php echo $execScheduled->start_date; ?><br/>
    <b>Type:</b> <?php echo $execScheduled->getTypeForDisplay(); ?><br/>
    <b>Floor:</b> <?php echo $execScheduled->fk_ethernet; ?><br/>
    <b>Light Ctrl:</b> <?php echo $execScheduled->lc_id; ?><br/>
    <b>Device:</b> <?php echo $execScheduled->dvc_id; ?><br/>
    <b>Group:</b> <?php echo $execScheduled->group; ?><br/>
</p>

<?php
    $this->renderPartial('/partial/_clearSort',
        array(
            'gridId'=>'report-grid',
            'url'=>'emergencyScheduled?id_exec_schedule='.$execScheduled->id_exec_schedule
        )
    )
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'report-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'ajaxUpdate'=>'grid-clear-sorting',
    'columns'=>array(
        array(
            'name'=>'fk_ethernet',
            'value'=>'$data->ethernet->name',
            'filter'=>false,
        ),
        'lc_id',
        'dvc_id',
        array(
            'type'=>'raw',
            'name'=>'circuit_failure',
            'value'=>'$data->getValueToGrid("circuit_failure")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'PASS', true=>'FAIL')
        ),
        array(
            'type'=>'raw',
            'name'=>'battery_duration_failed',
            'value'=>'$data->getValueToGrid("battery_duration_failed")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'PASS', true=>'FAIL')
        ),
        array(
            'type'=>'raw',
            'name'=>'battery_failed',
            'value'=>'$data->getValueToGrid("battery_failed")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'PASS', true=>'FAIL')
        ),
        array(
            'type'=>'raw',
            'name'=>'emergency_lamp_failed',
            'value'=>'$data->getValueToGrid("emergency_lamp_failed")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'PASS', true=>'FAIL')
        ),
        array(
            'type'=>'raw',
            'name'=>'function_test_overdue',
            'value'=>'$data->getValueToGrid("function_test_overdue")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'NO', true=>'YES')
        ),
        array(
            'type'=>'raw',
            'name'=>'duration_test_overdue',
            'value'=>'$data->getValueToGrid("duration_test_overdue")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'NO', true=>'YES')
        ),
        array(
            'type'=>'raw',
            'name'=>'function_test_failed',
            'value'=>'$data->getValueToGrid("function_test_failed")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'PASS', true=>'FAIL')
        ),
        array(
            'type'=>'raw',
            'name'=>'duration_test_failed',
            'value'=>'$data->getValueToGrid("duration_test_failed")',
            'htmlOptions'=>array('style' => 'text-align:center'),
            'filter'=>array(false=>'PASS', true=>'FAIL')
        ),
        array(
            'name'=>'created_at',
            'filter'=>false,
        ),
        array(
            'name'=>'fk_ethernet',
            'filter'=>false,
            'headerHtmlOptions'=>array('style' => 'display: none;'),
            'htmlOptions'=>array('style' => 'display: none;'),
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
<div>Double click a line to go to the corresponding device representation in the Plan Viewer</div>

<div class="report_download">
    <a title="Click for Pdf Download" href="emergencyScheduledPdf?id_exec_schedule=<?php echo $execScheduled->id_exec_schedule; ?>" onclick="requestDownload(this.href);return false;"><div class="ui_button pdf">Pdf</div></a>
    <a title="Click for Csv Download" href="emergencyScheduledCsv?id_exec_schedule=<?php echo $execScheduled->id_exec_schedule; ?>" onclick="requestDownload(this.href);return false;"><div class="ui_button csv">Csv</div></a>
</div>

<script type="text/javascript">
    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
    function requestDownload(target) {
        var params = new Array();
        $("[name^='ExecutedEmergency']").each(function(){
            if (this.value !== '') {
                params.push(this.name+'='+this.value);
            }
        });
        window.location.href=target+'&'+params.join('&');
    }
</script>

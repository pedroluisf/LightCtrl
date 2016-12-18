<?php
/* @var $this ReportController */
/* @var $model Failure */

$this->breadcrumbs=array(
    'Report'=>array('/report'),
    'Failure'
);
$custom_filter = array(
            'lamp_failed'=>'Lamp Failed',
            'circuit_failure'=>'Circuit Failure',
            'battery_duration_failed'=>'Battery Duration Failed',
            'battery_failed'=>'Battery Failed',
            'emergency_lamp_failed'=>'Emergency Lamp Failed',
            'function_test_overdue'=>'Function Test Overdue',
            'duration_test_overdue'=>'Duration Test Overdue',
            'function_test_failed'=>'Function Test Failed',
            'duration_test_failed'=>'Duration Test Failed'
        );
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']);
?>

<h1><?php echo $title;?> Report</h1>

<p>
    You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php
    $this->renderPartial('/partial/_clearSort',
        array(
            'gridId'=>'report-grid',
            'url'=>'failure',
            'filters'=>'Failure'
        )
    )
?>
<?php
    $this->widget('zii.widgets.grid.CGridView', array(
        'id'=>'report-grid',
        'dataProvider'=>$model->search(),
        'filter'=>$model,
        'ajaxUpdate'=>'grid-clear-sorting',
        'afterAjaxUpdate' => 'reinstallDatePicker', //call function to reinstall date picker
        'columns'=>array(
            array(
                'name'=>'fk_ethernet',
                'value'=>'$data->ethernet_name',
                'filter'=>CHtml::listData(Ethernet::model()->findAll(), 'id_ethernet', 'name'),
            ),
            'lc_id',
            'dvc_id',
            array(
                'name'=>'type_description',
                'filter' => CHtml::listData(Description::model()->findAll(), 'description','description'),
            ),
            array(
                'name'=>'failure_display',
                'value'=>'($data->ReadableFailure) ? $data->ReadableFailure : ""',
                'filter'=>$custom_filter
            ),
            array(
                'name'=>'created_at',
                'filter'=>
                    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                            'model' => $model,
                            'attribute' => 'created_at',
                            'options'=>array(
                                'dateFormat'=>'yy-mm-dd',
                                'changeYear'=>'true',
                                'changeMonth'=>'true',
                                'yearRange'=>'1900:'.(date('Y')+1),
                            ),
                            'htmlOptions'=>array(
                                'id'=>'date',
                            ),
                        ),
                        true),
            ),
            array(
                'name'=>'fk_ethernet',
                'filter'=>false,
                'headerHtmlOptions'=>array('style' => 'display: none;'),
                'htmlOptions'=>array('style' => 'display: none;'),
            ),
        ),
    ));
?>
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
    <a title="Click for Pdf Download" href="failurePdf" onclick="requestDownload(this.href);return false;"><div class="ui_button pdf">Pdf</div></a>
    <a title="Click for Csv Download" href="failureCsv" onclick="requestDownload(this.href);return false;"><div class="ui_button csv">Csv</div></a>
</div>
<script type="text/javascript">
    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
    function requestDownload(target) {
        var params = new Array();
        $("[name^='Failure']").each(function(){
            if (this.value !== '') {
                params.push(this.name+'='+this.value);
            }
        });
        window.location.href=target+'?'+params.join('&');
    }
</script>

<?php
    $this->renderPartial('_reinstalCalendarFilter');
?>
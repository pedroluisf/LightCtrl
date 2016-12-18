<?php
/* @var $this ReportController */
/* @var $model Emergency */

$this->breadcrumbs=array(
	'Report'=>array('/report'),
    'Emergency'
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']);
?>

<h1><?php echo $title;?> Report</h1>

<?php
if (strtolower(Yii::app()->controller->action->id) != 'emergencycurrent') :
?>
    <p>Please select a date interval:
        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'name'=>'dateFrom',
                'value'=>($dateFrom ? $dateFrom->format('Y-m-d') : ''),
                'options'=>array(
                    'dateFormat'=>'yy-mm-dd',
                    'changeYear'=>'true',
                    'changeMonth'=>'true',
                    'yearRange'=>'1900:'.(date('Y')+1)
                ),
                'htmlOptions' => array('style' => 'width : 80px; text-align:center;')
            )
        );
        ?>

        <?php
        $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'name'=>'dateTo',
                'value'=>($dateTo ? $dateTo->format('Y-m-d') : ''),
                'options'=>array(
                    'dateFormat'=>'yy-mm-dd',
                    'changeYear'=>'true',
                    'changeMonth'=>'true',
                    'yearRange'=>'1900:'.(date('Y')+1)
                ),
                'htmlOptions' => array('style' => 'width : 80px; text-align:center;')
            )
        );
        ?>
        <input type="button" id="filter_dates" title="Filter Dates" value="Filter Dates" onclick="js:requestNewDates()"/>
    </p>
<?php
endif;
?>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php
    if ($dateFrom) {
        $url = Yii::app()->controller->action->id . "?dateFrom=" . $dateFrom->format('Y-m-d') . "&dateTo=" . $dateTo->format('Y-m-d');
    } else {
        $url = Yii::app()->controller->action->id;
    }

    $this->renderPartial('/partial/_clearSort',
        array(
            'gridId'=>'report-grid',
            'url'=>$url,
            'filters'=>'Emergency'
        )
    )
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
    'id'=>'report-grid',
    'dataProvider'=>$model->search(),
    'filter'=>$model,
    'ajaxUpdate'=>'grid-clear-sorting',
    'afterAjaxUpdate' => 'reinstallDatePicker', //call function to reinstall date picker
    'columns'=>array(
        array(
            'name'=>'fk_ethernet',
            'value'=> (Yii::app()->controller->action->id == 'emergency' ? '$data->ethernet->name' : '$data->ethernet_name'),
            'filter'=>CHtml::listData(Ethernet::model()->findAll(), 'id_ethernet', 'name'),
        ),
        'lc_id',
        'dvc_id',
        array(
            'name'=>'test_type',
            'filter'=>array('function'=>'function','duration'=>'duration')
        ),
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
            'filter'=>
                $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                        'model' => $model,
                        'attribute' => 'created_at',
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
    <a title="Click for Pdf Download" href="<?php echo Yii::app()->controller->action->id.'Pdf';?>" onclick="requestDownload(this.href);return false;"><div class="ui_button pdf">Pdf</div></a>
    <a title="Click for Csv Download" href="<?php echo Yii::app()->controller->action->id.'Csv';?>" onclick="requestDownload(this.href);return false;"><div class="ui_button csv">Csv</div></a>
</div>
<script type="text/javascript">
    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';

    function requestNewDates() {
        var dateFrom = $('#dateFrom').val(),
            dateTo = $('#dateTo').val();
        window.location.href = "?dateFrom="+dateFrom+"&dateTo="+dateTo;
    }

    function requestDownload(target) {
        var params = new Array('1=1');
        $("[name^='<?php echo get_class($model); ?>']").each(function(){
            if (this.value !== '') {
                params.push(this.name+'='+this.value);
            }
        });
        <?php if ($dateFrom) { ?>
            window.location.href=target+'?dateFrom=<?php echo $dateFrom->format('Y-m-d');?>&dateTo=<?php echo $dateTo->format('Y-m-d');?>&'+params.join('&');
        <?php } else { ?>
            window.location.href=target+'?'+params.join('&');
        <?php } ?>
    }
</script>

<?php
    $this->renderPartial('_reinstalCalendarFilter');
?>
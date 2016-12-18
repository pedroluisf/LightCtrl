<?php
/* @var $this scheduleController */
/* @var $model CommandSchedule */

$this->breadcrumbs=array(
    'Scheduling',
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']);

$this->menu=$this->buildCommandScheduleMenu();
?>

<div id="statusMsg"></div>
<h1>Waiting Tasks</h1>

<?php
    $this->renderPartial('/partial/_clearSort',
        array(
            'gridId'=>'schedule-grid',
            'url'=> $url = Yii::app()->createUrl('schedule'),
            'filters'=>'CommandSchedule'
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
                'name'=>'priority',
                'filter'=>array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10),
            ),
            array(
                'name'=>'next_date',
                'header'=>'Next Date',
                'value'=>'$data->getNextDateForScheduler()',
                'filter'=>false,
                'htmlOptions'=>array('style' => 'width: 80px;')
            ),
            array(
                'name'=>'event_time',
                'value'=>'$data->getTimeForDisplay()'
            ),
            'description',
            array(
                'name'=>'periodicity',
                'value'=>'$data->getPeriodicityForDisplay()',
                'filter'=>$model->getPeriodicityOptionsList()
            ),
            array(
                'name'=>'type',
                'value'=>'$data->getTypeForDisplay()',
                'filter'=>$model->getCommandTypesList()
            ),
            array(
                'name'=>'fk_user',
                'header'=>'Created By',
                'value'=>'$data->user->username',
                'filter'=>CHtml::listData(User::model()->findAll(), 'id_user', 'username'),
            ),
            array(
                'class'=>'CButtonColumn',
                'template'=>'{view}{update}{delete}{run_now}',
                'htmlOptions' => array('style' => 'width: 70px;'),
                'buttons' => array(
                    'view' => array(
                        'url' => '$this->grid->controller->createUrl(
                        "schedule/view",
                        array("id" => "$data->id_schedule"))'
                    ),
                    'update' => array(
                        'url' => '$this->grid->controller->createUrl(
                        "schedule/update",
                        array("id" => "$data->id_schedule"))'
                    ),
                    'delete' => array(
                        'url' => '$this->grid->controller->createUrl(
                            "schedule/delete",
                            array("id" => "$data->id_schedule"))'
                    ),
                    'run_now' => array(
                        'label' => 'Run Now',
                        'url' => '$this->grid->controller->createUrl(
                        "schedule/run",
                        array("ajax"=>1, "id" => "$data->id_schedule"))',
                        'imageUrl' => Yii::app()->request->baseUrl . '/themes/intsys/images/play.png',
                        'options' => array( 'onclick' => '
                            event.preventDefault();
                            if (confirm("Are you sure you want to run this schedule now?")) {
                                $.ajax({
                                    type:"get",
                                    url:$(this).attr("href"),
                                    success:function(data) {
                                        $("#statusMsg").html(data);
                                    },
                                    error: function (xhr, ajaxOptions, thrownError) {
                                        console.log(xhr.status + " " + thrownError);
                                    }
                                });
                            }'
                        )
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

<div class="report_download">
    <a title="Click to Print" href="<?php echo $url = Yii::app()->createUrl('schedule/listPrint'); ?>" onclick="requestDownload(this.href, true);return false;"><div class="ui_button printer">Print</div></a>
    <a title="Click for Pdf Download" href="<?php echo $url = Yii::app()->createUrl('schedule/listPdf'); ?>" onclick="requestDownload(this.href, false);return false;"><div class="ui_button pdf">Pdf</div></a>
</div>

<script type="text/javascript">
    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
    function requestDownload(target, print) {
        var params = new Array();
        $("[name^='CommandSchedule']").each(function(){
            if (this.value !== '') {
                params.push(this.name+'='+this.value);
            }
        });
        var location = target+'?'+params.join('&');
        if (print){
            printWindow = window.open(location, "Print", "status=0,toolbar=0,width=800,height=600");
            printWindow.focus();
            printWindow.print();
        }else {
            window.location.href=location;
        }
    }
</script>

<?php $this->renderPartial('_reinstalCalendarFilter'); ?>
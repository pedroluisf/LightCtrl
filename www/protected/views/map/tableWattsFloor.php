<?php
/* @var $this MapController */
/* @var $model Consumption */

$this->breadcrumbs=array(
    'Map'=>array('/map'),
    'Consumption (Watts) / Floor'
);
$pageSize=Yii::app()->user->getState('pageSize',Yii::app()->params['default_page_size']);
?>

<h1><?php echo $title; ?></h1>
<?php
$this->renderPartial(
    'partial/_filterModal',
    array(
        'showTreeview' => false,
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'filters' => $filters
    )
);
?>
<div id="actions">
    <input type="button" id="show_pie_chart" title="Chart Format" class="chart_button" onclick="changeFormat('chart')"/>
    <input type="button" id="show_table" title="Table format" class="chart_button" onclick="changeFormat('html')"/>
    <input type="button" id="filter_open" title="Set Filters" class="chart_button"/>
</div>

<div class="overflow_x-auto clear_both">
    <?php

        $columns = array();

        $columns[] = array(
            'name'=>'fk_ethernet',
            'header'=>'Floor',
            'value'=>'$data["name"]'
        );
        $columns[] = array(
            'name'=>'consumption',
            'header'=>'Consumption'
        );

        // This last one is needed for double click to index page
        $columns[] = array(
            'name'=>'fk_ethernet',
            'headerHtmlOptions'=>array('style' => 'display: none;'),
            'htmlOptions'=>array('style' => 'display: none;'),
        );

        // Render Grid / Table
        $this->widget('zii.widgets.grid.CGridView',
            array(
                'id'=>'report-grid',
                'dataProvider'=>$dataProvider,
                'ajaxUpdate'=>'grid-clear-sorting',
                'columns'=>$columns,
            )
        );
    ?>
</div>
<div class="pager pager_sizer">
    Number of items per page:
    <?php
        echo CHtml::dropDownList('pageSize',$pageSize,array(10=>10,20=>20,50=>50,100=>100),array(
            'onchange'=>"$.fn.yiiGridView.update('report-grid',{ data:{pageSize: $(this).val() }})",
        ));
    ?>
</div>

<div class="report_download">
    <a title="Click for Pdf Download" href="<?php echo Yii::app()->controller->action->id.'?format=pdf';?>" onclick="requestDownload(this.href);return false;"><div class="ui_button pdf">Pdf</div></a>
    <a title="Click for Csv Download" href="<?php echo Yii::app()->controller->action->id.'?format=csv';?>" onclick="requestDownload(this.href);return false;"><div class="ui_button csv">Csv</div></a>
</div>

<script type="text/javascript">

    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>';
    var filterUrl = '<?php echo Yii::app()->controller->action->id; ?>';
    filterCallback = function(response) {
        $('#report-grid').replaceWith($('#report-grid', response));
    }

    function requestDownload(target) {
        window.location.href=target +
            '&dateFrom=' + filterValue.dateFrom +
            '&dateTo=' + filterValue.dateTo +
            '&FiltersForm[fk_area]=' + filterValue.area_id +
            '&FiltersForm[fk_ethernet]=' + filterValue.ethernet_id +
            '&FiltersForm[lc_id]=' + filterValue.lc_id +
            '&FiltersForm[dvc_id]=' + filterValue.dvc_id;
    }
</script>

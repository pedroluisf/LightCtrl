<div id="filter_container" style="height: <?php echo (!empty($showTreeview) || (isset($dateFrom) && isset($dateTo)) ? '400' : '200') ;?>px;">
    <?php if (!empty($showTreeview)) : ?>
        <div class="filter_area percent_60 float_left">
            <?php
            $list = CHtml::listData(Area::model()->findAll(),'id_area', 'name');
            echo CHtml::dropDownList('area_select', Area::model()->id_area, $list,
                array(
                    'empty' => '(Filter Area / Floor)',
                )
            );
            ?>
            <div id= "filter_treeView" class="mtm">
                <?php
                    // Dummy Tree for including libraries
                    $this->widget('CTreeView');
                ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="filter_area  <?php echo (!empty($showTreeview) ? 'percent_40' : 'percent_100 center_align'); ?> float_left">
        <?php
        if (isset ($dateFrom)) {
            echo '<h4>' . (isset($dateTo) ? 'Start Date' : 'Date') . '</h4>';
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name' => 'dateFrom',
                    'value' => ($dateFrom ? $dateFrom->format('Y-m-d') : ''),
                    'options' => array(
                        'dateFormat' => 'yy-mm-dd',
                        'changeYear' => 'true',
                        'changeMonth' => 'true',
                        'yearRange' => '1900:' . (date('Y') + 1),
                        'onSelect' => 'js:filter.validateDates'
                    ),
                    'htmlOptions' => array('style' => 'width : 100px; text-align:center;')
                )
            );
        }
        ?>
        <?php
        if (isset ($dateTo)) {
            echo '<h4 class="mtl">End Date</h4>';
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                    'name'=>'dateTo',
                    'value'=>($dateTo ? $dateTo->format('Y-m-d') : ''),
                    'options'=>array(
                        'dateFormat'=>'yy-mm-dd',
                        'changeYear'=>'true',
                        'changeMonth'=>'true',
                        'yearRange'=>'1900:'.(date('Y')+1),
                        'onSelect' => 'js:filter.validateDates'
                    ),
                    'htmlOptions' => array('style' => 'width : 100px; text-align:center; font-size:100%;')
                )
            );
        }
        ?>
        <?php
        if (isset($timeFrom) && isset($timeTo)) {
            echo '<h4 class="mtl">Time</h4>';
            $this->widget('CMaskedTextField', array(
                'name' => 'timeFrom',
                'value' => $timeFrom,
                'mask' => '99:99',
                'completed' => 'filter.validateTime',
                'htmlOptions' => array('style' => 'width : 50px; text-align:center;')
            ));
            echo '<span class="mlm">to</span>';
            $this->widget('CMaskedTextField', array(
                'name'=>'timeTo',
                'value'=>$timeTo,
                'mask' => '99:99',
                'completed' => 'filter.validateTime',
                'htmlOptions' => array('style' => 'margin-left: 10px; width: 50px; text-align:center;'),
            ));
        }
        ?>
    </div>
</div>
<script type="text/javascript">
    var filterValue = {
        area_id: <?php echo ($filters->getParam('fk_area') != '' ? $filters->getParam('fk_area') : 'null');?>,
        ethernet_id: <?php echo ($filters->getParam('fk_ethernet') != '' ? $filters->getParam('fk_ethernet') : 'null');?>,
        lc_id: <?php echo ($filters->getParam('lc_id') != '' ? $filters->getParam('lc_id') : 'null');?>,
        dvc_id: <?php echo ($filters->getParam('dvc_id') != '' ? $filters->getParam('dvc_id') : 'null');?>,
        dateFrom: "<?php echo $dateFrom->format('Y-m-d');?>",
        dateTo: <?php echo isset($dateTo) ? '"'.$dateTo->format('Y-m-d').'"' : 'null';?>,
        timeFrom: <?php echo isset($timeFrom) ? '"'.$timeFrom.'"' : 'null';?>,
        timeTo: <?php echo isset($timeTo) ? '"'.$timeTo.'"' : 'null';?>
    };
</script>
<?php
/* @var $this IndexController */

$this->pageTitle=Yii::app()->name;
?>
<div id="toolbar">
    <div id="layers">
        <div id="layers_link">Layers</div>
        <div id="layers_list"></div>
    </div>
    <div id="buttons">
        <input type="button" class="buttons button_select" onClick="VWR.viewer.setSelectTool()" title="Select">
        <input type="button" class="buttons button_move" onClick="VWR.viewer.setHandTool()" title="Move">
        <div class="spacer"></div>
        <input type="button" class="buttons button_zoom" onClick="VWR.viewer.setZoomTool()" title="Zoom">
        <input type="button" class="buttons button_zoom_rect" onClick="VWR.viewer.setZoomRectTool()" title="Zoom Rectangle">
        <input type="button" class="buttons button_fit_window" onClick="VWR.viewer.setFitToWindowTool()" title="Fit To Window">
        <div class="spacer"></div>
        <input type="button" class="buttons button_more_info" onClick="toolbar.properties.showWindow()" title="Show Properties/Status">
        <div class="spacer"></div>
        <input type="button" class="buttons button_status" onClick="toolbar.statusCmd.getParam()" title="Get Status" disabled="disabled">
        <input type="button" class="buttons button_emergency" onClick="toolbar.emergencyCmd.getParam()" title="Emergency Test" disabled="disabled">
        <div class="spacer"></div>
        <?php
            foreach (Dictionary::getDaliMessageCodesList() as $code => $daliMsg) {
                if ($code==-1) {
                    echo '<div class="spacer"></div>';
                } else {
                    $class = Dictionary::translateDaliMessageToCssClass($code);
                    $params = Dictionary::getDaliMessageParamsByCode($code);
                    if ($params) {
                        $action = 'toolbar.dali.getParameters(\''.$code.'\')';
                    } else {
                        $action = 'toolbar.dali.code=\''.$code.'\';toolbar.dali.sendMsg()';
                    }
                    echo '<input type="button" data-type="'.$code.'" class="buttons dali '.$class.'" onClick="'.$action.'" title="'.$daliMsg.'" disabled="disabled">';
                }
            }
        ?>
        <div id="feedback">
            testing
        </div>
    </div>
    <div id="status_params">
        <input type="checkbox" id="status_force"/>
        <label for="status_force">Force Refresh on Status</label>
    </div>
    <div id="emergency_params">
        <input type="radio" name="emergency_option" id="emergency_func_test" value="func" checked="checked"/>
        <label for="emergency_func_test">Function Test</label><br/>
        <input type="radio" name="emergency_option" id="emergency_time_test" value="durat"/>
        <label for="emergency_time_test">Time Duration Test</label><br/>
    </div>
    <div id="dali_params">
        <?php
            // Dummy Slider for including libraries
            $this->widget('zii.widgets.jui.CJuiSlider');
        ?>
    </div>
</div>
<div id="action_bar">
    <?php
    $list = CHtml::listData(Area::model()->findAll(),'id_area', 'name');
    echo CHtml::dropDownList('area_select', Area::model()->id_area, $list,
        array(
            'empty' => '(Select an Area)',
        )
    );
    ?>
    <div id="properties"></div>
    <div id= "treeView_container" class="home-page mtm">
        <?php
            // Dummy Tree for including libraries
            $this->widget('CTreeView');
        ?>
    </div>
</div>

<div id="viewer_container">
    <object
        classid="clsid:A662DA7E-CCB7-4743-B71A-D817F6D575DF"
        id="AdView"
        width="600px"
        height="600px"
        Border="1" VIEWASTEXT>
        <param name="windowless" value="True">
        <param name="wmode" value="transparent">
        <param name="Src" value="">
        <param name="RibbonDisplayMode" value='RibbonNotShown'>
        <param name="UserInterfaceEnabled" value="False">
        <param name="MarkupsVisible" value="False">
    </object>
</div>

<script type="text/javascript">
    var csrf_token = '<?php echo Yii::app()->request->csrfToken;?>',
        timer_status_delay = <?php echo Yii::app()->params['timer_status_delay']; ?>;

    <?php if (!empty($selectDevice)) : ?>
    var selectDevice = <?php echo json_encode($selectDevice); ?>;
    <?php endif; ?>

</script>

<?php
    $params = Dictionary::getDaliMessageParamsByCode($code);
    if (!$params) return;

    $min = $params['min'];
    $max = $params['max'];

    /* @var Status $status */
    if ($status){
        $value = $status->lux_level;
    } else {
        $value = 0;
    }

    $valueTranslated = (isset($params['messages'][$value]) ? $params['messages'][$value] : $value );
    $valueTranslatedPercent = '('.round(($valueTranslated * 100) / $max).'%)';
    echo $this->widget('zii.widgets.jui.CJuiSlider', array(
        'value'=>$value,
        'id'=>'dialog_slider',
        'options'=>array(
            'min'=>$params['min'],
            'max'=>$params['max'] - 1, // max = no change
            'slide'=>'js:params.paramsValue',
        )
    ), true);
?>

<input type="hidden" id="dialog_name" value="<?php echo $params['command']?>"/>
<input type="hidden" id="dialog_value"/>
<input type="checkbox" class="mll" id="dialog_no_change" onClick="params.setNoChange()">No Change</input>
<input type="text" id="dialog_display" readonly="readonly" value="<?php echo $valueTranslated ;?>"/>
<input type="text" id="dialog_percent" readonly="readonly" value="<?php echo $valueTranslatedPercent ;?>"/>

<script type="text/javascript">
    var dali_msg_params = <?php echo json_encode($params); ?>;
    params = {
        setNoChange : function() {
            if ($("#dialog_no_change").is(':checked')) {
                $('#dialog_slider').slider( "option", "disabled", true );
                $("#dialog_value").val(255);
                this.paramsTranslate(255)
            } else {
                $('#dialog_slider').slider( "option", "disabled", false );
                $("#dialog_value").val($('#dialog_slider').slider("option", "value"));
                params.paramsTranslate($('#dialog_slider').slider("option", "value"))
            }
        },
        paramsValue : function(event, ui) {
            $("#dialog_value").val(ui.value);
            params.paramsTranslate(ui.value)
        },
        paramsTranslate : function(value) {
            if (value == 0){
                $("#dialog_percent").val('(0%)');
            } else {
                $("#dialog_percent").val('('+Math.round((value * 100) / 254) + '%)');
            }
            if (dali_msg_params.messages != undefined
                && dali_msg_params.messages[value] != undefined){
                value = dali_msg_params.messages[value];
            }
            $("#dialog_display").val(value);
        }
    };
</script>
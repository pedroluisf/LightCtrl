<?php
    $attributeTranslated = Dictionary::translateAttributeDescription($attribute);
    $value = ($value == 'Disabled' ? 255 : $value);
    $valueTranslated = ($value == 255 ? 'Disabled' : $value );
    $valueTranslatedPercent = ($value == 255 ? '' : '('.round(($valueTranslated * 100) / 254).'%)');
    echo '<h2>'.$attributeTranslated.($subAttribute !== null ? ' - '.($subAttribute+1) : '').'</h2>';
    echo $this->widget('zii.widgets.jui.CJuiSlider', array(
        'value'=>($value == 255 ? 0 : $value ),
        'id'=>'dialog_slider',
        'options'=>array(
            'min'=>0,
            'max'=>254, // 255 = N/A
            'slide'=>'js:attribute.setValue',
            'disabled'=>($value == 255 ? true : false),
            'htmlOptions'=>array('class'=>($value == 255 ? 'ui-state-disabled' : '')),
        )
    ), true);
?>

<input type="hidden" id="dialog_value"/>
<input type="checkbox" class="mll" id="dialog_disabled" <?php echo ($value == 255 ? 'checked="checked"' : '')?>onClick="attribute.setDisabled()">Disabled</input>
<input type="text" id="dialog_display" readonly="readonly" value="<?php echo $valueTranslated ;?>"/>
<input type="text" id="dialog_percent" readonly="readonly" value="<?php echo $valueTranslatedPercent ;?>"/>

<script type="text/javascript">
    attribute = {
        setDisabled : function() {
            if ($("#dialog_disabled").is(':checked')) {
                $('#dialog_slider').slider( "option", "disabled", true );
                $("#dialog_value").val(255);
                attribute.valueTranslate(255)
            } else {
                $('#dialog_slider').slider( "option", "disabled", false );
                $("#dialog_value").val($('#dialog_slider').slider("option", "value"));
                attribute.valueTranslate($('#dialog_slider').slider("option", "value"))
            }
        },
        setValue : function(event, ui) {
            $("#dialog_value").val(ui.value);
            attribute.valueTranslate(ui.value)
        },
        valueTranslate : function(value) {
            if (value == 0){
                $("#dialog_percent").val('(0%)');
            } else if (value != 255){
                $("#dialog_percent").val('('+Math.round((value * 100) / 254) + '%)');
            }
            if (value == 255){
                value = 'Disabled';
                $("#dialog_percent").val('');
            }
            $("#dialog_display").val(value);
        }
    };
</script>
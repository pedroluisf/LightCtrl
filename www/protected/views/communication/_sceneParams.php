<?php
$params = Dictionary::getDaliMessageParamsByCode($code);
if (!$params) return;

/* @var Status $status */
if ($status){
    $value = $status->current_scene;
} else {
    $value = 0;
}
?>

<input type="hidden" id="params_name" value="<?php echo $params['command']?>"/>
<input type="hidden" id="params_value"/>

<div style="margin-left: 70px">
    <?php
        foreach ($params['values'] as $key => $val) {
            /** @var DeviceTransfer $component */
            if ($component->scenes[$key] != 255) { // 255 stands for not used
    ?>
                <div class="block">
                    <input type="radio"
                           id="param_<?php echo $key; ?>"
                           name="<?php echo $params['command']; ?>"
                           onClick="params.markSelected(this.value)"
                           value="<?php echo $key ;?>"
                           <?php if ($key == $value) { echo 'CHECKED';} ?>
                    />
                    <label for="param_<?php echo $key; ?>"><?php echo $val ;?></label>
                </div>
    <?php
            }
        }
    ?>
</div>

<script type="text/javascript">
    params = {
        markSelected : function(value) {
            $("#params_value").val(value);
        }
    };
</script>
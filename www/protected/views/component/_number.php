<?php
$attributeTranslated = Dictionary::translateAttributeDescription($attribute);

echo '<h2>'.$attributeTranslated.($subAttribute !== null ? ' - '.($subAttribute+1) : '').'</h2>';
?>

<input type="number" id="dialog_value" value="<?php echo $value ;?>">

<script type="text/javascript">
    $("#dialog_value").keydown(function(event) {
        // Allow: backspace, delete, tab, escape, enter, ctrl+A and .
        if ($.inArray(event.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            // Allow: Ctrl+A
            (event.keyCode == 65 && event.ctrlKey === true) ||
            // Allow: home, end, left, right
            (event.keyCode >= 35 && event.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }

        var charValue = String.fromCharCode(event.keyCode),
            valid = /^[0-9]+$/.test(charValue);

        if (!valid) {
            event.preventDefault();
        }
    });
<?php if ($validator): ?>
    var min = <?php echo $validator['min']; ?>;
    var max = <?php echo $validator['max']; ?>;
    var dialog_validator = function () {
        if ($("#dialog_value").val() < min || $("#dialog_value").val() > max ) {
            return "Value should be between " + min + " and " + max;
        }
        return true;
    };
<?php endif; ?>
</script>
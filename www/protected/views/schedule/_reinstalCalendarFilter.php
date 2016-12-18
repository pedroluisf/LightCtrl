<?php
/* function to re install date picker after filter the result. if you don't use it then after filter the result calendar will not shown in filter box */
Yii::app()->clientScript->registerScript('re-install-date-picker', "
function reinstallDatePicker(id, data) {
    jQuery('#start_date').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
    jQuery('#end_date').datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy-mm-dd'
    });
}
");
?>
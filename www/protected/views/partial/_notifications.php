<?php
    if (Yii::app()->controller->id == 'notification') {
        return;
    }
?>
<div id="notification_close"></div>
<div id="notification_message">
    <?php
        if (!empty($notifications)) {
            echo 'You have a total of <b>'.count($notifications).'</b> new Notification'.(count($notifications)>1 ? 's' : '' ).'</br>';
            echo 'Click here to see '.(count($notifications)>1 ? 'them' : 'it' );
        }
    ?>
</div>
<script type="text/javascript">
    <?php
        if (!empty($notifications)) {
            echo '$("#notification_popup").fadeIn(1200);';
        }
    ?>
    $("#notification_close").live('click', function(event) {
        $('#notification_popup').fadeOut(400);
    });
    $("#notification_message").live('click', function(event) {
        window.location.href = '<?php echo Yii::app()->baseUrl; ?>/notification/index';
    });
</script>

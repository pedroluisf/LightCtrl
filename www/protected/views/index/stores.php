<script type="text/javascript">
    var messages_for_types = {},
        props_store = {},
        status_store = {};

    <?php
        $messages = Dictionary::getDaliMessagesForType();
        foreach ($messages as $code => $msg) {
            if (!empty($msg)){
                echo 'messages_for_types['.$code.']='.json_encode($msg).';';
            }
        }
    ?>
</script>

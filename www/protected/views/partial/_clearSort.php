<div id="grid-clear-sorting">
<?php
    // Only display option to clear if more than one sorted column is selected
    $sortArray = null;
    foreach ($_GET as $key => $value){
        if (preg_match('/_sort$/i', $key)) {
            $sortArray = explode('-', $value);
            break;
        }
    }
    if (!empty($sortArray) && count($sortArray)>1):
?>
    <div id="clear_sort">
        <a href="<?php echo $url; ?>">Clear Sort</a>
    </div>
<?php endif; ?>
    <script type="text/javascript">

        $("#clear_sort").live('click', function(event) {
            event.preventDefault();
            var url = $(this).children('a').attr('href'),
                gridId = '#<?php echo $gridId; ?>',
                $grid = $(gridId);

            $grid.addClass("grid-view-loading");

            <?php if ($filters): ?>
            var params = new Array();
            $("[name^=<?php echo $filters;?>]").each(function(){
                if (this.value !== '') {
                    params.push(this.name+'='+this.value);
                }
            });
            url+='?'+params.join('&');
            <?php endif;?>

            $.ajax({
                url: url
            }).success(function(data) {
                    $(gridId).replaceWith($(gridId, data));
                    $('#clear_sort').html('');
                });
        });

    </script>
</div>

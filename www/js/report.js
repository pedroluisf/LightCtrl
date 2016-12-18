/**
 * Created by Luiixx on 13-07-2014.
 */

$('#report-grid table.items tr').live('dblclick', function(){
    var fk_ethernet = $(this.children).last()[0].innerHTML,
        lc_id = $(this.children).get(1).innerHTML,
        dvc_id = $(this.children).get(2).innerHTML,
        form = $(
            '<form id="selectDevice" action="' + baseUrl + '/" method="post">' +
                '<input type="hidden" name="selectDevice[fk_ethernet]" value="' + fk_ethernet + '" />' +
                '<input type="hidden" name="selectDevice[lc_id]" value="' + lc_id + '" />' +
                '<input type="hidden" name="selectDevice[dvc_id]" value="' + dvc_id + '" />' +
                '<input type="hidden" name="YII_CSRF_TOKEN" value="' + csrf_token + '" />' +
            '</form>'
        );
    if (!fk_ethernet || isNaN(fk_ethernet)) {
        return;
    }
    $('body').append(form);  // This line is necessary for Internet Explorer
    $(form).submit();
});

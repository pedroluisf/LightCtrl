/**
 * Created by Luiixx on 01-06-2014.
 */

/************************/
/*** Grid DoubleClick ***/
/************************/
$('#schedule-grid table.items tr').dblclick(function(){
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
    $('body').append(form);  // This line is necessary for Internet Explorer
    $(form).submit();
});

/************************/
/***   Area Selection ***/
/************************/
$('#area_select').change(function(){
    // Load TreeView
    var areaSelectValue = $('#area_select').val();
    if (areaSelectValue === "") {
        $('#treeView_container').html('');
        return;
    }
    $('#treeView_container').html('<div class="loading"></div>');
    $.ajax({
        type: "GET",
        url: baseUrl+'/schedule/areaTree',
        success: function(response, status) {
            $('#treeView_container').append(response);
            $('#treeView').treeview({
                url: baseUrl+'/schedule/areaTreeData?id_area='+areaSelectValue,
                urlCallback : function() {
                    $('#treeView_container .loading').remove();

                    var periodicity = $('input[name="CommandSchedule[periodicity]"]:checked', '#schedule-form').val(),
                        device,
                        deviceType = 'emg_lamp',
                        selectedDevice = $('#CommandSchedule_draw_id').val();

                    periodicityChange(periodicity);

                    if (selectedDevice == '') {
                        selectedDevice = $('#CommandSchedule_dvc_id').val();
                        var selectedCCI = $('#CommandSchedule_cci_sw_num').val();
                        deviceType = 'cci';
                    }

                    if (selectedDevice != '') {

                        if (deviceType == 'emg_lamp') {
                            device = $('#treeView li[data-drawing_id="'  + selectedDevice + '"]>span');
                        } else if (deviceType == 'cci' && selectedCCI != '') {
                            device = $('#treeView li[data-id="'  + selectedDevice + '"][data-sw_num="'  + selectedCCI + '"]>span');
                        }

                        if (device != undefined) {
                            device.trigger('click');
                            if (device.parent('li').data('type') != 'eth') {
                                if (device.parent('li').data('type') != 'ctr') {
                                    if (deviceType == 'emg_lamp') {
                                        var Eth = device.parent('li').parent('ul').parent('li').parent('ul').parent('li').children('div.hitarea.expandable-hitarea');
                                    } else {
                                        var Eth = device.parent('li').parent('ul').parent('li').parent('ul').parent('li').parent('ul').parent('li').children('div.hitarea.expandable-hitarea');
                                    }
                                    if (Eth != undefined) {
                                        Eth.trigger('click');
                                    }
                                }
                                if (deviceType == 'emg_lamp') {
                                    var LgtCtrl = device.parent('li').parent('ul').parent('li').children('div.hitarea.expandable-hitarea');
                                } else if (deviceType == 'cci') {
                                    var LgtCtrl = device.parent('li').parent('ul').parent('li').parent('ul').parent('li').children('div.hitarea.expandable-hitarea'),
                                        scc = device.parent('li').parent('ul').parent('li').children('div.hitarea.expandable-hitarea');
                                }
                                if (LgtCtrl != undefined) {
                                    LgtCtrl.trigger('click');
                                }
                                if (scc != undefined) {
                                    scc.trigger('click');
                                }
                            }
                        }
                    } else {
                        Type.initialize();
                    }
                }
            });
            $('#CommandSchedule_fk_area').val(areaSelectValue);
        }
    });
});

/*********************/
/***    TreeView   ***/
/*********************/
$('#treeView').find('span').live("click", function(){
    var $tree_leaf = $(this).parent();
    $('#treeView li').removeClass('selected');

    if ($tree_leaf.length > 0) {
        // Show it on tree view
        $tree_leaf.addClass('selected');

        $('#CommandSchedule_draw_id').val($tree_leaf.data('drawing_id'));

        // Get the corresponding CAN
        if ($tree_leaf.data('type') == 'eth') {
            $('#CommandSchedule_fk_ethernet').val($tree_leaf.data('id'));
            showGroup();
            Type.changeSelection('all');
        } else if ($tree_leaf.data('type') == 'ctr') {
            $('#CommandSchedule_fk_ethernet').val($tree_leaf.parent().parent("[data-type='eth']").data('id'));
            showGroup();
            if ($tree_leaf.data('id') == '63') {
                Type.changeSelection('normal');
            } else {
                Type.changeSelection('emergency');
            }
        } else {
            $('.CommandSchedule_group').addClass('form-hidden');
            if ($tree_leaf.data('type') == '130') {
                Type.changeSelection('normal');
                $('#CommandSchedule_fk_ethernet').val($tree_leaf.parent().parent().parent().parent().parent().parent("[data-type='eth']").data('id'));
                $('#CommandSchedule_cci_sw_num').val($tree_leaf.attr("data-sw_num"));
            } else if ($tree_leaf.data('type') == '1') {
                $('#CommandSchedule_fk_ethernet').val($tree_leaf.parent().parent().parent().parent("[data-type='eth']").data('id'));
                Type.changeSelection('emergency');
            } else {
                Type.changeSelection('');
            }
        }

    }
});

var showGroup = function() {
    if ($('#CommandSchedule_type').val() == 'function' || $('#CommandSchedule_type').val() == 'duration') {
        $('.CommandSchedule_group').removeClass('form-hidden');
    } else {
        $('.CommandSchedule_group').addClass('form-hidden');
    }
}

/**********************/
/*** Type Selection ***/
/**********************/

var Type = {
    deviceType : null,
    switchVisibility: function(value) {
        if (value == 'normal') {
            // Show CCI Data Select
            $('.CommandSchedule_cci_data').removeClass('form-hidden');
            // Hide Group Select
            $('.CommandSchedule_group').addClass('form-hidden');
        } else {
            // Hide CCI Data Select
            $('.CommandSchedule_cci_data').addClass('form-hidden');

            this.getDeviceType($('#CommandSchedule_draw_id').val());
            if (Type.deviceType == 'eth' || Type.deviceType == 'ctr') {
                $('.CommandSchedule_group').removeClass('form-hidden');
            } else {
                $('.CommandSchedule_group').addClass('form-hidden');
            }
        }
    },
    getDeviceType: function(drawing_id) {
        $('li', $('#treeView')).each(function() {
            if (this.dataset.drawing_id == drawing_id) {
                Type.deviceType = this.dataset.type;
                return;
            }
        });
    },
    initialize : function() {
        $('#CommandSchedule_type option[value="normal"]').attr('disabled',true);
        $('#CommandSchedule_type option[value="function"]').attr('disabled',true);
        $('#CommandSchedule_type option[value="duration"]').attr('disabled',true);
        $('#CommandSchedule_type option[value=""]').attr('selected','selected');
    },
    changeSelection : function (selection) {
        if (selection == 'normal') {
            $('#CommandSchedule_type option[value="normal"]').attr('disabled',false);
            $('#CommandSchedule_type option[value="function"]').attr('disabled',true);
            $('#CommandSchedule_type option[value="duration"]').attr('disabled',true);
            $('#CommandSchedule_type option[value="normal"]').attr('selected','selected');
            $('#CommandSchedule_submit').attr('disabled',false);
        } else if (selection == 'emergency') {
            $('#CommandSchedule_type option[value="normal"]').attr('disabled',true);
            $('#CommandSchedule_type option[value="function"]').attr('disabled',false);
            $('#CommandSchedule_type option[value="duration"]').attr('disabled',false);
            if ($('#CommandSchedule_type').val() == "normal") {
                $('#CommandSchedule_type option[value=""]').attr('selected','selected');
            }
            $('#CommandSchedule_submit').attr('disabled',false);
        } else if (selection == 'all') { // ALL
            $('#CommandSchedule_type option').attr('disabled',false);
            $('#CommandSchedule_submit').attr('disabled',false);
        } else {
            $('#CommandSchedule_submit').attr('disabled',true);
        }
        Type.switchVisibility($('#CommandSchedule_type').val());
    }
}

$('#CommandSchedule_type').change(function(){
    Type.switchVisibility($('#CommandSchedule_type').val());
});

/*****************************/
/*** Periodicity Selection ***/
/*****************************/

var periodicityChange = function(value) {
    if (value == 'weekly') {
        $('#CommandSchedule_monday').removeAttr('disabled');
        $('#CommandSchedule_tuesday').removeAttr('disabled');
        $('#CommandSchedule_wednesday').removeAttr('disabled');
        $('#CommandSchedule_thursday').removeAttr('disabled');
        $('#CommandSchedule_friday').removeAttr('disabled');
        $('#CommandSchedule_saturday').removeAttr('disabled');
        $('#CommandSchedule_sunday').removeAttr('disabled');
        $('#CommandSchedule_month_repeat').attr('disabled', 'disabled');
    } else if (value == 'monthly') {
        $('#CommandSchedule_month_repeat').removeAttr('disabled');
        $('#CommandSchedule_monday').attr('disabled', 'disabled');
        $('#CommandSchedule_tuesday').attr('disabled', 'disabled');
        $('#CommandSchedule_wednesday').attr('disabled', 'disabled');
        $('#CommandSchedule_thursday').attr('disabled', 'disabled');
        $('#CommandSchedule_friday').attr('disabled', 'disabled');
        $('#CommandSchedule_saturday').attr('disabled', 'disabled');
        $('#CommandSchedule_sunday').attr('disabled', 'disabled');
    } else {
        $('#CommandSchedule_monday').attr('disabled', 'disabled');
        $('#CommandSchedule_tuesday').attr('disabled', 'disabled');
        $('#CommandSchedule_wednesday').attr('disabled', 'disabled');
        $('#CommandSchedule_thursday').attr('disabled', 'disabled');
        $('#CommandSchedule_friday').attr('disabled', 'disabled');
        $('#CommandSchedule_saturday').attr('disabled', 'disabled');
        $('#CommandSchedule_sunday').attr('disabled', 'disabled');
        $('#CommandSchedule_month_repeat').attr('disabled', 'disabled');
    }
}

$( document ).ready(function() {
    var selectedArea = $('#CommandSchedule_fk_area').val();
    if (selectedArea != '') {
        $('#area_select option[value=' + selectedArea + ']').prop('selected', true).trigger('change');
    }
});
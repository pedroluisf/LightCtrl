/**
 * Created by PedroLF on 25-01-2014.
 */

/************************/
/***   Area Selection ***/
/************************/
$('#area_select').change(function(){
    // Load TreeView
    if ($('#area_select').val() === "") {
        $('#treeView_container').html('');
        return;
    }
    $('#treeView_container').html('<div class="loading"></div>');
    $.ajax({
        type: "GET",
        url: baseUrl+'/index/areaTree',
        success: function(response, status) {
            $('#treeView_container').append(response);
            $('#treeView').treeview({
                url: baseUrl+'/index/areaTreeData?id_area='+$('#area_select').val() ,
                urlCallback : function() {
                    $('#treeView_container .loading').remove();

                    if (typeof selectDevice == "undefined" || !selectDevice.fk_ethernet) return;

                    // Device Selection on TreeView
                    if (!selectDevice.lc_id) { // If no Lc_id then its a Ethernet
                        var device = $('#treeView li[data-type="eth"][data-id="'  + selectDevice.fk_ethernet + '"]>span');
                    } else {
                        var eth = $('#treeView li[data-type="eth"][data-id="'  + selectDevice.fk_ethernet + '"]>ul');
                        if (!eth.length) return; // If no Ethernet found, ignore

                        if (!selectDevice.dvc_id) { // If no Dvc_id then its a Light Controller
                            var device = eth.children('[data-type="ctr"][data-id="'  + selectDevice.lc_id + '"]').children('span');
                        } else { // Otherwise it's a Device
                            var lc = eth.children('[data-type="ctr"][data-id="'  + selectDevice.lc_id + '"]').children('ul');
                            var device = lc.children('[data-id="'  + selectDevice.dvc_id + '"]').children('span');
                        }
                    }
                    if (!device.length) return;

                    // Tree Expand until selected Device
                    device.trigger('click');
                    if (device.data('type') != 'eth') {
                        if (device.data('type') != 'ctr') {
                            var grandParent = device.parent('li').parent('ul').parent('li').parent('ul').parent('li').children('div.hitarea.expandable-hitarea');
                            if (grandParent != undefined) {
                                grandParent.trigger('click');
                            }
                        }
                        var parent = device.parent('li').parent('ul').parent('li').children('div.hitarea.expandable-hitarea');
                        if (parent != undefined) {
                            parent.trigger('click');
                        }
                    }
                }
            });
        }
    });

    // Load Props/Status store
    $.ajax({
        type: "GET",
        dataType: "json",
        url: baseUrl+'/index/areaEthernetStores?id_area='+$(this).val(),
        success: function(response, status) {
            if (response) {
                props_store = response.props;
                status_store = response.status;
            }
        }
    });

    // Load Schema
    if (AdView.DocumentHandler != undefined) {
        $.ajax({
            type: "GET",
            url: baseUrl+'/index/areaSchema?id_area='+$(this).val(),
            success: function(response, status) {
                if (response) {
                    VWR.viewer.loadDrawing(response);
                } else {
                    VWR.viewer.unloadDrawing();
                }
            }
        });
    }
});

/********************/
/***    Layers    ***/
/********************/
$('#layers_link').click(function(){
    if ($('#layers_list').is(":visible")){
        $('#layers_list').slideUp();
    } else {
        if ($('#layers_list').html() != '') {
            $('#layers_list').slideDown();
        }
    }
});

$('.layer_check').live( "click", function() {
    if (AdView.DocumentHandler != undefined) {
        VWR.viewer.setLayerVisibility(this.name, this.checked);
    }
});

/*********************/
/***    TreeView   ***/
/*********************/
$('#treeView').find('span').live("click", function(){
    if (AdView.DocumentHandler != undefined) {
        VWR.viewer.selectObj(''+$(this).parent().data('drawing_id'));
    }
});
$('#treeView_container').on('mouseenter', function(){
    $('#properties').slideUp();
});

/*********************/
/***    Toolbar   ***/
/*********************/
var toolbar = {
    selected_can_id : null,
    selected_drawing_id : null,
    setDrawingId : function(drawing_id){
        // Perform Selection
        toolbar.selected_can_id = null;
        toolbar.selected_drawing_id = drawing_id;

        // Disable / Enable Buttons
        this.buttons.disableAll();
        this.buttons.enableBySelectedType();

        if (!drawing_id){
            return;
        }
        var $tree_leaf = $('#treeView li[data-drawing_id="'+drawing_id+'"]');
        if ($tree_leaf.length > 0) {
            // Show it on tree view
            $tree_leaf.addClass('selected');
            // Get the corresponding CAN
            if ($tree_leaf.data('type') == 'eth') {
                toolbar.selected_can_id = toolbar.selected_drawing_id;
            } else if ($tree_leaf.data('type') == 'ctr') {
                toolbar.selected_can_id = $tree_leaf.parent().parent("[data-type='eth']").data('id');
            } else {
                toolbar.selected_can_id = $tree_leaf.parent().parent().parent().parent("[data-type='eth']").data('id');
            }
        }

        toolbar.properties.setValues();
    },
    treeview : {
        resize : function(){
            var height = $(window).height() - 200;
            $('#treeView_container').css('height',height + 'px');
        }
    },
    feedback : function (response, success) {
        $('#feedback').html(response);
        if (success){
            $('#feedback').css('background-color', '#00ff00');
        } else {
            $('#feedback').css('background-color', '#ff0000');
        }
        $('#feedback').fadeIn(300);
        setTimeout(function(){
            $('#feedback').fadeOut(500);
        }, 3000);
    },
    properties : {
        setValues : function(){
            if (!toolbar.selected_drawing_id){
                return;
            }

            // Set the properties window
            $('#properties').html('');
            var propsUl = document.createElement('ul');
            for (var key in props_store[toolbar.selected_drawing_id]) {
                if (props_store[toolbar.selected_drawing_id].hasOwnProperty(key)) {
                    $(propsUl).append('<li>' + key + ": " + props_store[toolbar.selected_drawing_id][key] + '</li>');
                }
            }

            if (status_store[toolbar.selected_can_id] != undefined){
                var status = status_store[toolbar.selected_can_id][toolbar.selected_drawing_id];
                if (status != undefined){
                    status = status.split(',');
                    for (var text in status) {
                        $(propsUl).append('<li>' + status[text] + '</li>');
                    }
                }
            }
            if ($(propsUl).html()) {
                $("#properties").append(propsUl);
            }
        },
        showWindow : function () {
            if ($('#properties').is(":visible")){
                $('#properties').slideUp();
            } else {
                if ($('#properties').html()) {
                    $('#properties').slideDown();
                }
            }
        }
    },
    buttons : {
        disableAll : function(){
            $('#buttons .dali').prop( "disabled", true );
            $('#buttons .button_status').prop( "disabled", true );
            $('#buttons .button_emergency').prop( "disabled", true );
        },
        enableBySelectedType : function(){
            // Status
            $('#buttons .button_status').prop( "disabled", (toolbar.selected_drawing_id ? false : true) );
            // Dali Messages + Emergency
            $('#treeView li').each(function(){
                if ($(this).data('drawing_id') == toolbar.selected_drawing_id) {
                    // Status
                    if (messages_for_types[$(this).data('type')] != undefined) {
                        var daliMsgs = messages_for_types[$(this).data('type')];
                        for (var index = 0; index < daliMsgs.length; ++index) {
                            $('#buttons .dali').each(function() {
                                if ($(this).data('type') == daliMsgs[index]){
                                    $(this).prop( "disabled", false );
                                }
                            });
                        }
                    }

                    // Emergency
                    var emergencyAvailableTypes = [1, '1', 'eth', 'ctr'];
                    if (emergencyAvailableTypes.indexOf($(this).data('type')) != -1) {
                        $('#buttons .button_emergency').prop( "disabled", false );
                    }
                    return;
                }
            });
        }
    },
    notification : {
        timer : null,
        timerFunction : function() {
            $.ajax({
                type: "POST",
                url: baseUrl+'/communication/getNotifications',
                dataType: "json",
                data: {
                    YII_CSRF_TOKEN: csrf_token
                },
                success: function(response) {
                    if (response.success){
                        $('#notification_popup').html(response.data);
                    }
                }
            });
        }
    },
    status : {
        timerStatus : null,
        timerStatusResponseFunction : function() {
            var area_id = $('#area_select').val();
            if (!area_id){
                return;
            }
            $.ajax({
                type: "POST",
                url: baseUrl+'/communication/getStatus',
                dataType: "json",
                data: {
                    YII_CSRF_TOKEN: csrf_token,
                    area_id: area_id
                },
                success: function(response) {
                    if (response.success){
                        status_store = {};
                        for (var key in response.data) {
                            status_store[key] = response.data[key];
                        }
                        toolbar.properties.setValues();
                    }
                }
            });
        }
    },
    statusCmd : {
        getParam : function(){
            $('#viewer_container').hide();
            $('#status_params').dialog({
                modal: true,
                close: function (){
                    // Show the viewer again
                    $('#viewer_container').show();
                },
                buttons: {
                    "Cancel": function () {
                        $(this).dialog("close");
                    },
                    "Confirm": function () {
                        $(this).dialog("close");
                        toolbar.statusCmd.sendCmd();
                    }
                }
            });
        },
        sendCmd : function() {
            var type = $('#status_force').attr('checked') ? 'new' : 'latest';
            $.ajax({
                type: "POST",
                url: baseUrl+'/communication/requestStatus',
                dataType: "json",
                data: {
                    YII_CSRF_TOKEN: csrf_token,
                    ethernet_id: toolbar.selected_can_id,
                    drawing_id: toolbar.selected_drawing_id,
                    type: type
                },
                success: function(response) {
                    toolbar.feedback(response.message, response.success);
                },
                error    : function( jqXHR, textStatus ) {
                    toolbar.feedback('Request failed', false);
                    console.log( "Request failed: " + jqXHR.responseText );
                }
            });
        }
    },
    emergencyCmd : {
        emergencyOption : $('input[name=emergency_option]'),
        getParam : function(){
            $('#viewer_container').hide();
            $('#emergency_params').dialog({
                modal: true,
                width: 350,
                close: function (){
                    // Show the viewer again
                    $('#viewer_container').show();
                },
                buttons: {
                    "Cancel": function () {
                        $(this).dialog("close");
                    },
                    "Start Test": function () {
                        $(this).dialog("close");
                        toolbar.emergencyCmd.sendCmd('start');
                    },
                    "Stop Test": function () {
                        $(this).dialog("close");
                        toolbar.emergencyCmd.sendCmd('stop');
                    }
                }
            });
        },
        sendCmd : function(proc) {
            var type = toolbar.emergencyCmd.emergencyOption.filter(':checked').val();
            $.ajax({
                type: "POST",
                url: baseUrl+'/communication/triggerEmergencyTest',
                dataType: "json",
                data: {
                    YII_CSRF_TOKEN: csrf_token,
                    drawing_id: toolbar.selected_drawing_id,
                    ethernet_id: toolbar.selected_can_id,
                    proc: proc,
                    type: type
                },
                success: function(response) {
                    toolbar.feedback(response.message, response.success);
                },
                error    : function( jqXHR, textStatus ) {
                    toolbar.feedback('Request Failed', false);
                    console.log( "Request failed: " + jqXHR.responseText );
                }
            });
        }
    },
    dali : {
        code : null,
        getParameters : function (code) {
            toolbar.dali.code = code;
            $.ajax({
                url     : baseUrl+'/communication/getDaliParams',
                type    : "POST",
                dataType: "json",
                data    : {
                    YII_CSRF_TOKEN: csrf_token,
                    code : toolbar.dali.code,
                    ethernet_id : toolbar.selected_can_id,
                    drawing_id : toolbar.selected_drawing_id
                },
                success : function( response ) {
                    if (response.success) {
                        toolbar.dali.prepareParamsDialog(response.data);
                        toolbar.dali.showParamsDialog();
                    } else {
                        toolbar.feedback(response.message, false);
                    }
                },
                error    : function( jqXHR, textStatus ) {
                    toolbar.feedback('Request Failed', false);
                    console.log( "Request failed: " + jqXHR.responseText );
                }
            });

        },
        prepareParamsDialog : function(response){
            // Get the Script out of Html response, since it will be "cleaned" by jquery.html
            var daliParams = document.getElementById("dali_params"),
                JSONCode=document.createElement("script");

            // Create a holder for javascript code
            JSONCode.setAttribute("type","text/javascript");
            var code = response.match(/<script.*?>([\s\S]*?)<\/script>/gmi);
            if (code.length) {
                code = code[0];
                code = code.replace(/<script.*?>/gmi, '');
                code = code.replace(/<\/script>/gmi, '');
                JSONCode.text = code;
            }

            // Inject html
            $(daliParams).html(response);

            // Inject Javascript
            daliParams.appendChild(JSONCode);
        },
        showParamsDialog  : function () {
            // Hide the Viewer, or it will stay always on top
            $('#viewer_container').hide();
            $('#dali_params').dialog({
                width: 350,
                modal: true,
                close: function (){
                    // Show the viewer again
                    $('#viewer_container').show();
                },
                buttons: {
                    "Cancel": function () {
                        $(this).dialog("close");
                    },
                    "Confirm": function () {
                        $(this).dialog("close");
                        var param;
                        if ($('#dialog_value') != undefined) {
                            param = $('#dialog_value').val();
                            toolbar.dali.sendMsg(param);
                        }
                    }
                }
            });
        },
        sendMsg     : function(param) {
            $.ajax({
                type: "POST",
                url: baseUrl+'/communication/sendDaliCommand',
                dataType: "json",
                data: {
                    YII_CSRF_TOKEN: csrf_token,
                    ethernet_id: toolbar.selected_can_id,
                    drawing_id: toolbar.selected_drawing_id,
                    cmd: toolbar.dali.code,
                    data: param
                },
                success: function(response) {
                    toolbar.feedback(response.message, response.success);
                    // Save the response in status store
                    try {
                        var can_id = Object.keys(response.data)[0];
                        var draw_id = Object.keys(response.data[can_id])[0];
                        status_store[can_id][draw_id] = response.data[can_id][draw_id];
                    } catch (e) {}
                    toolbar.properties.setValues();
                },
                error    : function( jqXHR, textStatus ) {
                    toolbar.feedback('Request failed', false);
                    console.log( "Request failed: " + jqXHR.responseText );
                }
            });
        }
    }
}
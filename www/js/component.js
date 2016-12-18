/**
 * Created by Luiixx on 01-06-2014.
 */

var component = {
    requestPending : false,
    selectedEthernetId : null,
    selectedDrawId : null,
    attributeName : null,
    prepareFormDialog : function(response) {
        // Clear old validators
        dialog_validator = undefined;

        // Get the Script out of Html response, since it will be "cleaned" by jquery.html
        var componentForm = document.getElementById("component_edit_form");

        // Inject html
        $(componentForm).html(response);

        // Create a holder for javascript code if any found
        var code = response.match(/<script.*?>([\s\S]*?)<\/script>/gmi);
        if (code !== null) {
            var JSONCode=document.createElement("script");
            JSONCode.setAttribute("type","text/javascript");
                code = code[0];
                code = code.replace(/<script.*?>/gmi, '');
                code = code.replace(/<\/script>/gmi, '');
                JSONCode.text = code;
            // Inject Javascript
            componentForm.appendChild(JSONCode);
        }

    },
    showFormDialog : function() {
        $('#component_edit_form').dialog({
            width: 350,
            modal: true,
            buttons: {
                "Cancel": function () {
                    $(this).dialog("close");
                },
                "Confirm": function () {
                    $(":button").attr("disabled","disabled").addClass( 'ui-state-disabled' );

                    // Send new Value
                    var value;
                    if ($('#dialog_value') != undefined) {
                        if (typeof dialog_validator !== 'undefined') {
                            var valid = dialog_validator();
                            if (valid !== true){
                                alert (valid);
                                $(":button").removeAttr('disabled').removeClass( 'ui-state-disabled' );
                            } else {
                                value = $('#dialog_value').val();
                                $("#component-form [name='" + component.attributeName + "']").val(value);
                                $(this).dialog("close");
                            }
                        } else {
                            value = $('#dialog_value').val();
                            $("#component-form [name='" + component.attributeName + "']").val(value);
                            $(this).dialog("close");
                        }
                    } else {
                        $(this).dialog("close");
                    }
                }
            }
        });
    },
    sendValue : function() {
        $.ajax({
            type: 'POST',
            url: 'update',
            dataType: "json",
            data: $("#component-form").serialize(),
            scope: this,
            beforeSend: function( jqXHR, settings) {
                $('#attributes_container').html('<div class="loading"></div>');
            },
            success: function(response, status) {
                if (response.success == true) {
                    $('#attributes_container').html(response.data);
                } else {
                    alert(response.message);
                }
            },
        });
    }
}

/************************/
/***   Area Selection ***/
/************************/
$('#area_select').change(function(){
    // Load TreeView
    $('#treeView_container').html('<div class="loading"></div>');
    $.ajax({
        type: "GET",
        url: 'areaTree',
        success: function(response, status) {
            var areaSelectValue = $('#area_select').val();
            if (areaSelectValue === "") {
                $('#treeView_container .loading').remove();
                $('#attributes_container').html('');
            } else {
                $('#treeView_container').append(response);
                $('#treeView').treeview({
                    url: 'areaTreeData?id_area='+areaSelectValue,
                    urlCallback : function() {
                        $('#treeView_container .loading').remove();
                    }
                });
            }
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

        component.selectedDrawId = $tree_leaf.data('drawing_id');

        // Get the corresponding CAN
        if ($tree_leaf.data('type') == 'eth') {
            component.selectedEthernetId = $tree_leaf.data('id');
        } else if ($tree_leaf.data('type') == 'ctr') {
            component.selectedEthernetId = $tree_leaf.parent().parent("[data-type='eth']").data('id');
        } else if ($tree_leaf.data('type') == '130') {
            component.selectedEthernetId = $tree_leaf.parent().parent().parent().parent().parent().parent("[data-type='eth']").data('id');
        } else {
            component.selectedEthernetId = $tree_leaf.parent().parent().parent().parent("[data-type='eth']").data('id');
        }

        // Ask for the attributes
        $('#attributes_container').html('<div class="loading"></div>');

        $.ajax({
            type: 'GET',
            url: 'getAttributes',
            dataType: "json",
            data: {ethernetId:component.selectedEthernetId, drawId:component.selectedDrawId},
            success: function(response, status) {
                if (response.success == true) {
                    $('#attributes_container').html(response.data);
                } else {
                    console.log(response.message);
                }
            }, error: function() {
                $('#attributes_container').html('Failed to retrieve component...');
            }
        });

    }
});

/**********************************/
/***    Expandable Attributes   ***/
/**********************************/
$('.allow_expand').live('click', function(){
    var me = this;
    var expandableArea = $(me).parent().children('.expandable_area');
    if (expandableArea.is(':visible')) {
       expandableArea.slideUp("slow", function() {
           $(me).removeClass('expanded');
       });
    } else {
       expandableArea.slideDown("slow", function() {
           $(me).addClass('expanded');
       });
    }
});

/*****************************************/
/***    Attributes Form and elements   ***/
/*****************************************/
$('#attributes_container input.editable').live('click', function(){
    if (component.requestPending == true) {
        return;
    }
    component.requestPending = true;
    var me = this;
    $(this).addClass('loading_modal');
    component.attributeName = $(this).attr("name");
    // Get Edit Form
    $.ajax({
        type: 'GET',
        url: 'getForm',
        dataType: "json",
        data: {
            ethernetId:component.selectedEthernetId,
            drawId:component.selectedDrawId,
            attributeName: component.attributeName,
            value:$(this).val()
        },
        scope: this,
        success: function(response, status) {
            if (response.success == true) {
                component.prepareFormDialog(response.data);
                component.showFormDialog();
            } else {
                console.log(response.message);
            }
        },
        complete : function() {
            component.requestPending = false;
            $(me).removeClass('loading_modal');
        }
    });

});

$('#component_submit').live('click', function(){
    event.preventDefault();
    component.sendValue();
});

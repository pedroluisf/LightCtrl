/**
 * Created by Pedro Ferreira on 19-05-2015.
 */
var filter = {
    pageLoad : false,
    validateFilters: function () {
        filter.validateDates();
        if (typeof filterValue.timeFrom !== "undefined"
            && filterValue.timeFrom
            && typeof filterValue.timeTo !== "undefined"
            && filterValue.timeTo
        ) {
            filter.validateTime();
        }
    },
    validateDates: function () {
        filterValue.dateFrom = $("#dateFrom").val();
        filterValue.dateTo = $("#dateTo").val();
        if (typeof filterValue.dateTo !== "undefined" && filterValue.dateFrom > filterValue.dateTo) {
            filterValue.dateFrom = filterValue.dateTo;
        }
        $("#dateFrom").val(filterValue.dateFrom);
        $("#dateTo").val(filterValue.dateTo);
    },
    validateTime: function () {
        filterValue.timeFrom = filter.getMaxTime($("#timeFrom").val());
        filterValue.timeTo = filter.getMaxTime($("#timeTo").val());
        if (filterValue.timeFrom > filterValue.timeTo) {
            filterValue.timeFrom = filterValue.timeTo;
        }
        $("#timeFrom").val(filterValue.timeFrom);
        $("#timeTo").val(filterValue.timeTo);
    },
    getMaxTime: function (time) {
        time = time.replace('_', '0');
        t = time.split(':');
        if (t[0] > 23) {
            t[0] = 23;
        }
        if (t[1] > 59) {
            t[1] = 59;
        }
        return ''.concat(t[0], ':', t[1]);
    },
    applyFilters: function() {
        filter.validateFilters();
        var filtersData = filter.prepareFiltersParams();
        $.ajax({
            url     : filterUrl + location.search,
            type    : "POST",
            data    : filtersData,
            success : function( response ) {
                filterCallback(response)
            },
            error    : function( jqXHR, textStatus ) {
                alert('An error occurred');
                console.log( "Request failed: " + jqXHR.responseText );
            }
        });
    },
    prepareFiltersParams : function() {
        // Create our FiltersForm model
        var filtersParams = {
            YII_CSRF_TOKEN: csrf_token,
            "FiltersForm[fk_area]" : filterValue.area_id,
            "FiltersForm[fk_ethernet]" : filterValue.ethernet_id,
            "FiltersForm[lc_id]" : filterValue.lc_id,
            "FiltersForm[dvc_id]" : filterValue.dvc_id
        };
        // Add Dates and times
        jQuery.extend(filtersParams, filterValue);
        return filtersParams;
    }
}

/************************/
/***    Change View   ***/
/************************/
changeFormat = function(format) {
    var url = location.protocol + '//' + location.host + location.pathname + '?format=' + format,
        form = document.createElement("form"),
        filtersData = filter.prepareFiltersParams();

    form.setAttribute("method", 'POST');
    form.setAttribute("action", url);

    for(var key in filtersData) {
        if(filtersData.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", filtersData[key]);

            form.appendChild(hiddenField);
        }
    }
    document.body.appendChild(form);
    form.submit();
}

/************************/
/***    Filter modal  ***/
/************************/
$('#filter_open').click(function() {
    $('#filter_container').dialog({
        modal: true,
        title: "Set Filters",
        width: 600,
        buttons: {
            "Cancel": function () {
                $(this).dialog("close");
            },
            "Filter": function () {
                filter.applyFilters();
                $(this).dialog("close");
            }
        }
    });
});

/************************/
/***   Area Selection ***/
/************************/
$('#area_select').change(function(){
    // Load TreeView
    filterValue.area_id = $('#area_select').val();
    if (!filter.pageLoad){
        filterValue.ethernet_id = null;
        filterValue.lc_id = null;
        filterValue.dvc_id = null;
    }
    if (filterValue.area_id === "") {
        $('#filter_treeView').html('');
        filterValue.area_id = null;
        return;
    }
    $('#filter_treeView').html('<div class="loading"></div>');
    $.ajax({
        type: "GET",
        url: baseUrl+'/index/areaTree',
        success: function(response, status) {
            $('#filter_treeView').append(response);
            $('#treeView').treeview({
                url: baseUrl+'/index/areaTreeData?id_area='+filterValue.area_id,
                urlCallback : function() {
                    $('#filter_treeView .loading').remove();
                    if (filter.pageLoad) {
                        filter.pageLoad = false;
                        var device;
                        // Device Selection on TreeView
                        if (filterValue.ethernet_id) {
                            var eth = $('#treeView li[data-type="eth"][data-id="' + filterValue.ethernet_id + '"]>ul');
                            if (!filterValue.lc_id && !filterValue.dvc_id) {
                                device = eth;
                            } else {
                                if (filterValue.lc_id) {
                                    var lc = eth.children('[data-type="ctr"][data-id="'  + filterValue.lc_id + '"]').children('span');
                                }
                                if (!filterValue.dvc_id) {
                                    device = lc;
                                } else {
                                    device = lc.children('[data-id="'  + filterValue.dvc_id + '"]').children('span');
                                }
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
                }
            });
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

        // $tree_leaf.data('drawing_id');

        if ($tree_leaf.data('type') == 'eth') {
            filterValue.ethernet_id = $tree_leaf.data('id');
            filterValue.lc_id = null;
            filterValue.dvc_id = null;
        } else if ($tree_leaf.data('type') == 'ctr') {
            filterValue.ethernet_id = $tree_leaf.parent().parent("[data-type='eth']").data('id');
            filterValue.lc_id = $tree_leaf.data('id');
            filterValue.dvc_id = null;
        } else {
            filterValue.ethernet_id = $tree_leaf.parent().parent().parent().parent("[data-type='eth']").data('id');
            filterValue.lc_id = $tree_leaf.parent().parent("[data-type='ctr']").data('id');
            filterValue.dvc_id = $tree_leaf.data('id');
        }
    }
});

/***************************************/
/***    Select filters on TreeView   ***/
/***************************************/
$(document).ready(function() {
    if (filterValue.area_id) {
        filter.pageLoad = true;
        // Select a given device if received
        $('#area_select option[value=' + filterValue.area_id + ']').prop('selected', true).trigger('change');
    }
});
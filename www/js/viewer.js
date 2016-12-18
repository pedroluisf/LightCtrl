/**
 * Created by PedroLF on 21-01-2014.
 */
var VWR = VWR || {};
VWR.viewer = {
    proportion_x: null,
    proportion_y: null,
    selected_drawing_id: null,
    cleanStr: function(str){
        return str.replace(/[a-zA-Z]+/, '');
    },
    setSelectTool: function(){
        AdView.DocumentHandler.ExecuteCommand("SELECT");
    },
    setHandTool: function(){
        AdView.DocumentHandler.ExecuteCommand("PAN");
    },
    setZoomTool: function (){
        AdView.Viewer.ExecuteCommand("ZOOM");
    },
    setZoomRectTool: function(){
        AdView.Viewer.ExecuteCommand("ZOOMRECT");
    },
    setFitToWindowTool: function(){
        AdView.Viewer.ExecuteCommand("FITTOWINDOW");
    },
    disableToolbar: function(){
        AdView.Viewer.ToolbarVisible = false;
        AdView.Viewer.UserInterfaceEnabled = false;
    },
    loadDrawing: function(drawing){
        this.resize();
        AdView.SourcePath = drawing;
        AdView.Viewer.WaitForPageLoaded();
        VWR.viewer.disableToolbar();
        this.refreshLayerInfo();
        AdView.Viewer.ExecuteCommand("NAVIGATION");
    },
    unloadDrawing: function(){
        this.resize();
        AdView.SourcePath = "";
        AdView.Viewer.WaitForPageLoaded();
        VWR.viewer.disableToolbar();
        AdView.Viewer.ExecuteCommand("NAVIGATION");
    },
    selectObj: function(drawing_id){
        var Objs = AdView.EcompositeViewer.Section.Content.Objects(0); // 0 = All Objects, 1 = Only Selected
        var MyCollection = AdView.ECompositeViewer.Section.Content.CreateUserCollection(); // Create a collection of selected objects

        for (var i=1;(i<=Objs.Count);i++) {
            var Obj = Objs(i);
            var ObjsProp = Obj.Properties;
            var found = false;

            // Try to match the Object by the drawing_id
            for (var j=1;(j<=ObjsProp.Count);j++) {
                var prop = ObjsProp(j);
                if (prop.Name.toLowerCase() == "drawing_id" && prop.Value === drawing_id) {
                    MyCollection.AddItem(Objs(i)); // Add the object to colection
                    found = true;
                    break;
                }
            }
            // Position the viewer with the object on the center
            if (found && this.proportion_x !== null && this.proportion_y !== null) {
                var x, y, xdelta, ydelta = 0.0;
                for (var j=1;(j<=ObjsProp.Count);j++) {
                    var prop = ObjsProp(j);
                    if (prop.Name.toLowerCase() == "cr") {
                        var coord = prop.Value.split(',');
                        x = parseFloat(this.cleanStr(coord[0]));
                        y = parseFloat(this.cleanStr(coord[1]));
                        break;
                    }
                }

                xdelta = AdView.Viewer.View.Right - AdView.Viewer.View.Left;
                ydelta = AdView.Viewer.View.Top - AdView.Viewer.View.Bottom;
                // AdView.Viewer.SetView (Left, Bottom, Right, Top)
                AdView.Viewer.SetView ( (x / this.proportion_x)-xdelta/2,
                    (y / this.proportion_y)-ydelta/2,
                    (x / this.proportion_x)+xdelta/2,
                    (y / this.proportion_y)+ydelta/2);
//                AdView.ECompositeViewer.centerToCoordinates(x, y, 2);
                break;
            }

        }

        // Set the new collection of selected objects
        AdView.ECompositeViewer.Section.Content.Objects(1) = MyCollection;
    },
    setLayerVisibility: function(layer_name, visible) {
        var layers = AdView.DocumentHandler.Layers;
        var nLays = layers.Count;
        for(var i=1; i <= nLays; i++) {
            var layer = layers.Item(i);
            if ( layer && layer.name == layer_name) {
                layer.visible = visible;
            }
        }
    },
    refreshLayerInfo: function(){
        //Clear previous layers
        var layers_list = $('#layers_list'),
            ul = document.createElement('ul');
        layers_list.html('');

        //Check Document Layers and add them to external container
        var layers = AdView.DocumentHandler.Layers;
        var nLays = layers.Count;
        for(var i=1; i <= nLays; i++) {
            var layer = layers.Item(i);
            if ( layer && layer.name != "0") {

                //Create Checkbox
                var checkbox = document.createElement('input'),
                    li = document.createElement('li');
                checkbox.type = "checkbox";
                checkbox.className = 'layer_check';
                checkbox.name = layer.name;
                checkbox.checked = layer.visible;
                checkbox.id = 'layer_'+i;

                //Create Checkbox label
                var label = document.createElement('label')
                label.htmlFor = 'layer_'+i;
                label.appendChild(document.createTextNode(layer.name));

                //Append 2 container
                li.appendChild(checkbox);
                li.appendChild(label);
                ul.appendChild(li);
            }
        }
        layers_list.append(ul);

    },
    resize : function() {
        var width = $(window).width() - 330;
        var height = $(window).height() - 175;

        $('object').attr('width',width);
        $('object').attr('height',height);

        $('object embed').attr('width',width);
        $('object embed').attr('height',height);
    }
}

$(window).resize(function() {
    VWR.viewer.resize();
    toolbar.treeview.resize();
});

$(document).ready(function() {
    // Viewer adapt to window
    if (AdView.DocumentHandler == undefined) {
        window.location.replace('index/noviewer');
    }

    VWR.viewer.disableToolbar();
    VWR.viewer.resize();

    toolbar.treeview.resize();

    // Auto Status timer
    toolbar.status.timerStatus = setInterval(
        function(){
            toolbar.status.timerStatusResponseFunction()
        }, timer_status_delay);

    if ($("#notification_popup").length) {
        toolbar.notification.timer = setInterval(
            function(){
                toolbar.notification.timerFunction()
            }, 60000);
    }

    if (typeof selectDevice != 'undefined' && selectDevice['fk_area'] != undefined) {
        // Select a given device if received
        $('#area_select option[value=' + selectDevice['fk_area'] + ']').prop('selected', true).trigger('change');
    } else if ($('#area_select')[0].length > 1) { // 1st entry is always the "select area"
        // If not received any, then select the first area
        $('#area_select option:eq(1)').prop('selected', true).trigger('change'); // Index 1 is second entry
    }
});

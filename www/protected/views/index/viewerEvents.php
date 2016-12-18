<script language="javascript" for="AdView" event="OnUpdateUiItem(type, state, data)">
    if (type == "OBJECTPROPERTIES")
    {
        // Clear tree view selection
        $('#treeView li').removeClass('selected');

        objProps = data;
        if ( objProps )
        {
            var drawing_id = null;
            nObjs = objProps.Count;
            for (i=1; i <= nObjs; i++ )
            {
                prop = objProps.Item(i);
                if ( prop && prop.Name.toLowerCase() == 'drawing_id') {
                    drawing_id = prop.Value;
                }
            }
            // Select it on toolbar
            toolbar.setDrawingId(drawing_id.toString())
        } else {
            // Clear Selection
            toolbar.setDrawingId(null)
        }
    }
</script>
<script language="javascript" for="AdView" event="OnEndLoadItem(type, data, result)">
    if (type == "DOCUMENT")
    {
        VWR.viewer.disableToolbar();
    }
    else if (type == "SHEET")
    {
        // Fit to Window. This shows all design and sets view.top and view.right to it's max values
        AdView.Viewer.ExecuteCommand("FITTOWINDOW");

        // Look for "COORD_FINISH" to know proportions on view/objects
        var Objs = AdView.EcompositeViewer.Section.Content.Objects(0);

        outerFor:
        for (var i=1;(i<=Objs.Count);i++) {
            var Obj = Objs(i);
            var ObjsProp = Obj.Properties;

            for (j=1;(j<=ObjsProp.Count);j++) {
                var prop = ObjsProp(j);

                if (prop.Name.toUpperCase() == "COORD_FINISH") {
                    var coord = prop.Value.split(',');
                    var max_x = parseFloat(VWR.viewer.cleanStr(coord[0]));
                    var max_y = parseFloat(VWR.viewer.cleanStr(coord[1]));

                    // Set proportions if we found the marker
                    VWR.viewer.proportion_x = max_x / AdView.Viewer.View.Right;
                    VWR.viewer.proportion_y = max_y / AdView.Viewer.View.Top;

                    break outerFor;
                }

            }

        }
    }
</script>
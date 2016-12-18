<?php
$this->layout = 'report';
?>

<div id="report_title">
    <?php
        echo '<h3><b>'.Yii::app()->params['companyName'].' - '.Yii::app()->name.'</b></h3>';
        echo '<h4><b>Task List Report</b></h4>';
        echo '<table id="table_title">';
        echo '<tr><td style="width:130px;"><b>Client:</b></td><td colspan=3>'.Yii::app()->configuration->get('client_name').'</td></tr>';
        echo '<tr><td style="width:130px;"><b>Building:</b></td><td colspan=3>'.Yii::app()->configuration->get('building_name').'</td></tr>';
        echo '<tr><td style="width:130px;"><b>Generated On:</b></td><td>'.date('Y-m-d H:i:s').'</td><td style="width:50px;"><b>by:</b></td><td>'.Yii::app()->user->getName().'</td></tr>';
        echo '<tr><td style="width:130px;"><b>Number of Items:</b></td><td colspan=3>'.count($rows).'</td></tr>';
        echo '</table>';
    ?>
</div>

<?php
echo '<table><thead><tr>';
foreach ($fields as $field){
    echo '<th>'.$labels[$field].'</th>';
}
echo '<tr></thead><tbody>';

foreach ($rows as $row){
    echo '<tr class="dotted">';
    foreach ($fields as $field){
        echo '<td>'.$row->$field.'</td>';
    }
    echo '</tr>';
}

echo '</tbody></table>';
?>

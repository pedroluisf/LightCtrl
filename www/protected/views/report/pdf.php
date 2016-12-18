<?php
$this->layout = 'report';
$filtersArray = array();
if ($filters){
    foreach($filters as $key => $filter) {
        $filtersArray[] = $labels[$key] . '=' . $filter;
    }
}

?>

<div id="report_title">
    <?php
        echo '<h3><b>'.Yii::app()->params['companyName'].' - '.Yii::app()->name.'</b></h3>';
        echo '<h4><b>'.$reportName.' Report</b></h4>';
        if ($customHeader) {
            echo '<p>'.(is_array($customHeader) ? implode('</p><p>', $customHeader) : $customHeader).'</p>';
        }
        echo '<table id="table_title">';
        echo '<tr><td style="width:130px;"><b>Client:</b></td><td colspan=3>'.Yii::app()->configuration->get('client_name').'</td></tr>';
        echo '<tr><td style="width:130px;"><b>Building:</b></td><td colspan=3>'.Yii::app()->configuration->get('building_name').'</td></tr>';
        echo '<tr><td style="width:130px;"><b>Generated On:</b></td><td>'.date('Y-m-d H:i:s').'</td><td style="width:50px;"><b>by:</b></td><td>'.Yii::app()->user->getName().'</td></tr>';
        if (!empty($filtersArray)) {
            echo '<tr><td style="width:130px;"><b>Filters used:</b></td><td colspan=3>'.implode(', ',$filtersArray).'</td></tr>';
        }
        echo '<tr><td style="width:130px;"><b>Number of Items:</b></td><td colspan=3>'.count($rows).'</td></tr>';
        echo '<tr><td style="width:130px;"><b>Digital Signature:</b></td><td colspan=3>'.$digitalSignature.'</td></tr>';
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
    echo '<tr>';
    foreach ($fields as $field){
        echo '<td>'.$row->$field.'</td>';
    }
    echo '</tr>';
}

echo '</tbody></table>';
?>

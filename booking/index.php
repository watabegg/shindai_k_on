<?php
    session_start();
    
    require('../common/php/base.php');

    $today = new DateTime();
    
    $days = $_GET['day'] ?? $today->format('Y-m-d');
    $num = $_GET['num'] ?? 0;
    $times = $time[$num];

    $timeselect = '<select name="time" required>';

    for($i=0; $i < count($time); $i++){
        if($i == $num){
            $timeselect .= '<option value="'. $i . '" selected>' . $time[$i] . '</option>';
        }
        else{
            $timeselect .= '<option value="'. $i . '">' . $time[$i] . '</option>';
        }
    }
    $timeselect .= '<option value='. count($time) .'>ユーザー入力(備考に記入してください)</option></select>';

    $dayselect = '<input type="date" name="day" value="'. $days . '" required>';

?>

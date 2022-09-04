<?php
    session_start();
    
    require('../common/php/base.php');
    
    $days = $_GET['day'] ?? $today->format('Y/m/d');
    $num = $_GET['num'] ?? 0;
    $times = $time[$num];

?>

<?php
    session_start();
    require('common/php/base.php');

    $year_month_day = $_GET['date'] ?? getSunday();

    $year = substr($year_month_day, 0, 4);
    $month = sprintf("%01d", substr($year_month_day, 4, 2));
    $day = sprintf("%01d", substr($year_month_day, 6, 2));

    $next_week = getNthDay($year, $month, $day, '+1 week');
    $pre_week = getNthDay($year, $month, $day, '-1 week');

    
    list($t, $slt, $hyt, $y, $n, $j) = getYNJ($year, $month, $day);
    $weekterm = $t.'~';

    list($t, $slt, $hyt, $y, $n, $j) = getYNJ($year, $month, $day, 6);
    $weekterm .= $t;

    $table = '<tr><th></th>'; //

    for($i = 0; $i < 7; $i++) {
        list($t, $slt, $hyt, $y, $n, $j) = getYNJ($year, $month, $day, $i);
        if (isToday($y, $n, $j)) {
            $table .= '<th class="today" class="'.$Enweek[$i].'">'.$slt.'<br>'.$week[$i].'</th>';
        }
        else {
            $table .= '<th class="'.$Enweek[$i].'">'.$slt.'<br>'.$week[$i].'</th>';
        }
    }
    $table .= '</tr>';

    for($i = 0; $i < count($time); $i++) {
        $table .= '<tr><th class="'. $row_style[$i % 2] . '">' .$time[$i].'</th>';
        for($k = 0; $k < 7; $k++) {
            list($t, $slt, $hyt, $y, $n, $j) = getYNJ($year, $month, $day, $k);
            if (isToday($y, $n, $j)) {
                $table .= '<td class="today" class="'.$Enweek[$k].'"><a class="booking_window" href="./booking/index.html?day='.$hyt.'&num='.$i.'">' .'×<br>予約を確認'.'</a></td>';
            }
            else {
                $table .= '<td class="'.$Enweek[$k].'"><a class="booking_window" href="./booking/index.html?day='.$hyt.'&num='.$i.'">'.'〇<br>予約する'.'</a></td>';
            }
        }
        $table .= '</tr>';
    }
?>

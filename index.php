<?php
    session_start();
    require('common/php/base.php');

    $year_month_day = $_GET['date'] ?? getSunday();

    $year = substr($year_month_day, 0, 4);
    $month = sprintf("%01d", substr($year_month_day, 4, 2));
    $day = sprintf("%01d", substr($year_month_day, 6, 2));

    $next_week = getNthDay($year, $month, $day, '+1 week');
    $pre_week = getNthDay($year, $month, $day, '-1 week');

    
    list($t, $slt, $hyt, $y, $m, $d) = getYMD($year, $month, $day);
    $weekterm = $t.'~';

    list($t, $slt, $hyt, $y, $m, $d) = getYMD($year, $month, $day, 6);
    $weekterm .= $t;

    $table = '<tr><th></th>'; //

    for($i = 0; $i < 7; $i++) {
        list($t, $slt, $hyt, $y, $m, $d) = getYMD($year, $month, $day, $i);
        if(isBorA($y, $m, $d) == 'today'){
            $table .= '<th class="today" class="'.$Enweek[$i].'">'.$slt.'('.$week[$i].')</th>';
        }
        elseif(isBorA($y, $m, $d) == 'Past'){
            $table .= '<th class="Past" class="'.$Enweek[$i].'">'.$slt.'('.$week[$i].')</th>';
        }
        elseif(isBorA($y, $m, $d) == 'Future'){
            $table .= '<th class="Future" class="'.$Enweek[$i].'">'.$slt.'('.$week[$i].')</th>';
        }
    }
    $table .= '</tr>';

    for($i = 0; $i < count($time); $i++) {
        $table .= '<tr><th class="'. $row_style[$i % 2] . '">' .$time[$i].'</th>';
        for($k = 0; $k < 7; $k++) {
            list($t, $slt, $hyt, $y, $m, $d) = getYMD($year, $month, $day, $k);
            if(isBorA($y, $m, $d) == 'today'){
                $table .= '<td class="today" class="'.$Enweek[$k].'"><a class="booking_window" href="./booking/index.html?day='.$hyt.'&num='.$i.'">' .'×<br>予約を確認'.'</a></td>';
            }
            elseif(isBorA($y, $m, $d) == 'Past'){
                $table .= '<td class="Past" class="'.$Enweek[$k].'"><a class="booking_window" href="./booking/index.html?day='.$hyt.'&num='.$i.'">'.'〇<br>予約する'.'</a></td>';
            }
            elseif(isBorA($y, $m, $d) == 'Future'){
                $table .= '<td class="Future" class="'.$Enweek[$k].'"><a class="booking_window" href="./booking/index.html?day='.$hyt.'&num='.$i.'">'.'△<br>予約する,予約を確認'.'</a></td>';
            }
        }
        $table .= '</tr>';
    }
?>

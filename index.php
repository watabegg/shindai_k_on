<?php
    session_start();
    require('common/php/base.php');

    $today = new DateTime();

    $Y_m_d = $_GET['date'] ?? getSunday(); //2022-09-29メモ：ここ辺りにdate勝手に編集されたときの処理を作る

    $next_week = getAnyDay($Y_m_d, 0, 'Ymd', '+1');
    $pre_week = getAnyDay($Y_m_d, 0, 'Ymd', '-1');
    if($next_week > getAnyDay(getSunday(), 0, 'Ymd', '+3')){
        $next_week_tag = '翌週<span class="material-symbols-outlined">navigate_next</span></span>';
    }
    else{
        $next_week_tag = '<a id="next" href="'.$_SERVER['SCRIPT_NAME'].'?date='.$next_week.'">翌週<span class="material-symbols-outlined">navigate_next</span></a>';
    }

    if($pre_week < getAnyDay(getSunday(), 0, 'Ymd', '-1')){
        $pre_week_tag = '<span class="material-symbols-outlined">navigate_before</span>先週';
    }
    else{
        $pre_week_tag = '<a id="prev" href="'.$_SERVER['SCRIPT_NAME'].'?date='.$pre_week.'" ><span class="material-symbols-outlined">navigate_before</span>先週</a>';
    }

    $weekterm = getAnyDay($Y_m_d, 0, 'Y年m月d日').'~' . getAnyDay($Y_m_d, 6, 'Y年m月d日');

    $table_box = []; //テーブルの中身を二次元配列で管理しちゃうぞ～！

    $toweekSun = getAnyDay($Y_m_d, 0, 'Y-m-d');
    $toweekSat = getAnyDay($Y_m_d, 6, 'Y-m-d');

    try{
        $dbh = new PDO($localdsn, $localuser, $localpass);
        $res = $dbh->query("SELECT day, time, regist_name, part, otherpart FROM booking WHERE day >= '$toweekSun' AND day <= '$toweekSat'");
        $date = $res->fetchAll(PDO::FETCH_ASSOC);

        for($i=0; $i < 7; $i++){
            $daydate = getAnyDay($toweekSun, $i, 'Y-m-d');
            for($j=0; $j < count($time); $j++){
                $table_box[$i][$j] =<<<_HTML_

                _HTML_;
            }
        }
    }
    catch(PDOException $e){
        print $e->getMessage();
    }

    $table = '<tr><th></th>'; 
    for($i = 0; $i < 7; $i++) {
        list($y, $m, $d) = getAnyDay($Y_m_d, $i, 'split');
        $slt = getAnyDay($Y_m_d, $i, 'Y/m/d');
        $table .= '<th class="'.$Enweek[$i].'">'.$slt.'('.$week[$i].')</th>';
    }
    $table .= '</tr>';
    for($i = 0; $i < count($time); $i++) {
        $table .= '<tr><th class="'. $row_style[$i % 2] . ' table_time">' .$time[$i].'</th>';
        for($k = 0; $k < 7; $k++) {
            list($y, $m, $d) = getAnyDay($Y_m_d, $k, 'split');
            $hyt = getAnyDay($Y_m_d, $k, 'Y-m-d');
            if(isBorA($y, $m, $d) == 'today'){
                $table .= '<td class="today"><a class="booking_window" href="./booking/index.html?day='.$hyt.'&num='.$i.'">' .'×<br>予約を確認'.'</a></td>';
            }
            elseif(isBorA($y, $m, $d) == 'Past'){
                $table .= '<td class="Past">'.'〇<br>予約する'.'</td>';
            }
            elseif(isBorA($y, $m, $d) == 'Future'){
                $table .= '<td class="Future"><a class="booking_window" href="./booking/index.html?day='.$hyt.'&num='.$i.'">'.'△<br>予約する,予約を確認'.'</a></td>';
            }
        }
        $table .= '</tr>';
    }
?>

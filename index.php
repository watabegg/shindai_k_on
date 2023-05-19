<?php
    session_start();
    require('common/php/base.php');

    $today = new DateTime();

    $NotY_m_d = $_GET['date'] ?? getSunday();
    if(is_numeric($NotY_m_d) && strlen($NotY_m_d) == 8){
        if(isAnyday($NotY_m_d, 0)){
            $Y_m_d = $NotY_m_d;
        }
        else{
            $Y_m_d = getAnyDay($_GET['date'], 0, 'Ymd', '-1');
            echo '<script>alert("想定外の入力のためページを自動遷移しました")</script>';
        }
    }
    else{
        $Y_m_d = getSunday();
        echo '<script>alert("想定外の入力のためページを自動遷移しました")</script>';
    }

    $next_week = getAnyDay($Y_m_d, 0, 'Ymd', '+1');
    $pre_week = getAnyDay($Y_m_d, 0, 'Ymd', '-1');
    if($next_week > getAnyDay(getSunday(), 0, 'Ymd', '+3')){
        $next_week_tag = '<button class="next_week" disabled><span class="material-symbols-outlined">navigate_next</span></button>';
    }
    else{
        $next_week_tag = '<button class="next_week" onclick="location.href=\'index.html?date='.$next_week.'\'"><span class="material-symbols-outlined">navigate_next</span></button>';
    }

    if($pre_week < getAnyDay(getSunday(), 0, 'Ymd', '-1')){
        $pre_week_tag = '<button class="prev_week" disabled><span class="material-symbols-outlined">navigate_before</span></button>';
    }
    else{
        $pre_week_tag = '<button class="prev_week" onclick="location.href=\'index.html?date='.$pre_week.'\'"><span class="material-symbols-outlined">navigate_before</span></button>';
    }

    $table_box = []; //テーブルの中身を二次元配列で管理しちゃうぞ～！
    $table_box2 = [];
    for($i=0; $i < count($time); $i++){
        $table_box[$i] = [];
        $table_box2[$i] = [];
        for($j=0; $j < 7; $j++){
            $table_day = getAnyDay($Y_m_d, $j, 'Y-m-d');
            $table_box[$i][$table_day] = '';
        }
    }

    $toweekSun = getAnyDay($Y_m_d, 0, 'Y-m-d');
    $toweekSat = getAnyDay($Y_m_d, 6, 'Y-m-d');

    try{
        $dbh = new PDO($dsn, $user, $pass);
        $res = $dbh->query("SELECT booking_id, day, time, regist_name FROM booking WHERE day BETWEEN '$toweekSun' AND '$toweekSat'");
        $date = $res->fetchAll(PDO::FETCH_ASSOC);
        $count = $res->rowCount();

        //その日その時間に入ってる予約のidをコンマで区切った文字列に変換
        for($i=0; $i < $count; $i++){
            $table_box[$date[$i]["time"]][$date[$i]["day"]] .= $date[$i]["booking_id"] . ',';
        }
        for($i=0; $i < $count; $i++){
            $table_box[$date[$i]["time"]][$date[$i]["day"]] = rtrim($table_box[$date[$i]["time"]][$date[$i]["day"]], ",");
        }
    }
    catch(PDOException $e){
        print $e->getMessage();
    }

    $symbol = '';
    $table = '<tr><th class="table_day"></th>'; 
    for($i = 0; $i < 7; $i++) {
        $slt = getAnyDay($Y_m_d, $i, 'm月d日');
        $table .= '<th class="'.$Enweek[$i].' table_day">'.$slt.'('.$week[$i].')</th>';
    }
    $table .= '</tr>';
    for($i = 0; $i < count($time); $i++) {
        $table .= '<tr><td class="table_time"><span class="time_first">' .substr($time[$i], 0, 6).'</span><span class="time_end">'.substr($time[$i], 6, 10).'</span></td>';
        for($j = 0; $j < 7; $j++) {
            $theday = getAnyDay($Y_m_d, $j, 'Y-m-d'); 
            $table_class = isBorA($theday);
            if($table_class == 'Past'){
                $bookingURL = '';            
            }
            else{
                $bookingURL = 'href="./booking/index.html?day='.$theday.'&num='.$i;   
            }
            if(mb_strlen($table_box[$i][$theday]) > 17){ //予約が複数入ってるとき
                $id_some = explode(",", $table_box[$i][$theday]); //コンマ区切りidを配列変換
                $part_some = [];
                for($k=0; $k < count($id_some); $k++){
                    $part_some[$k] = intval(substr($id_some[$k], -7, -1));
                }
                if(array_product($part_some) == 510510){ //バンド練習×
                    $symbol = 'close';
                }
                else{ //バンド練習以外△
                    $symbol = 'change_history';
                }
            }
            elseif(mb_strlen($table_box[$i][$theday]) == 0){ //予約なし〇
                $symbol = 'circle';
            }
            else{ //予約が一つのみ
                if(substr($table_box[$i][$theday], -7, -1) == "510510"){ //バンド練習×
                    $symbol = 'close';
                }
                else{ 
                    if(substr($table_box[$i][$theday], 16, 17) == "1"){ //他パートあり△
                        $symbol = 'change_history';
                    }
                    elseif(substr($table_box[$i][$theday], 16, 17) == "0"){ //他パートなし×
                        $symbol = 'close';
                    }
                }
            }
            $table .= <<<_HTML_
                <td class="$table_class table_content">
                    <div class="table_box">
                    <a class="booking_window" $bookingURL&status=$symbol">
                        <div class="table_Symbol">
                            <span class="material-symbols-outlined $symbol">$symbol</span>
                        </div>
                    </a>
                    </div>
                </td>
            _HTML_;
        }
        $table .= '</tr>';
    }
?>

<?php
    session_start();
    require('common/php/base.php');

    $today = new DateTime();

    $Y_m_d = $_GET['date'] ?? getSunday(); //2022-09-29メモ：ここ辺りにdate勝手に編集されたときの処理を作る

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

    $weekterm = getAnyDay($Y_m_d, 0, 'm月d日').'~' . getAnyDay($Y_m_d, 6, 'm月d日');

    $table_box = []; //テーブルの中身を二次元配列で管理しちゃうぞ～！
    $table_box2 = [];
    for($i=0; $i < count($time); $i++){
        $table_box[$i] = [];
        $table_box2[$i] = [];
        for($j=0; $j < 7; $j++){
            $table_day = getAnyDay($Y_m_d, $j, 'Y-m-d');
            $table_box[$i][$table_day] = null;
            $table_box2[$i][$table_day] = null;
        }
    }

    $toweekSun = getAnyDay($Y_m_d, 0, 'Y-m-d');
    $toweekSat = getAnyDay($Y_m_d, 6, 'Y-m-d');

    try{
        $dbh = new PDO($dsn, $user, $pass);
        $res = $dbh->query("SELECT booking_id, day, time, regist_name FROM booking WHERE day BETWEEN '$toweekSun' AND '$toweekSat'");
        $date = $res->fetchAll(PDO::FETCH_ASSOC);
        $count = $res->rowCount();

        for($i=0; $i < $count; $i++){
            $table_box[$date[$i]["time"]][$date[$i]["day"]] .= $date[$i]["booking_id"] . ',';
            $table_box2[$date[$i]["time"]][$date[$i]["day"]] .= $date[$i]["regist_name"] . ',';
        }
        for($i=0; $i < $count; $i++){
            $table_box[$date[$i]["time"]][$date[$i]["day"]] = rtrim($table_box[$date[$i]["time"]][$date[$i]["day"]], ",");
            $table_box2[$date[$i]["time"]][$date[$i]["day"]] = rtrim($table_box2[$date[$i]["time"]][$date[$i]["day"]], ",");
        }
    }
    catch(PDOException $e){
        print $e->getMessage();
    }

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
                $confirmURL = '';
                $bookingURL = '';            
            }
            else{
                $confirmURL = 'href="./confirm/index.html?day='.$theday.'&num='.$i.'"';
                $bookingURL = 'href="./booking/index.html?day='.$theday.'&num='.$i.'"';   
            }
            if(mb_strlen($table_box[$i][$theday]) > 17){
                $id_some = explode(",", $table_box[$i][$theday]);
                $part_some = [];
                for($k=0; $k < count($id_some); $k++){
                    $part_some[$k] = intval(substr($id_some[$k], -7, -1));
                }
                if(array_product($part_some) == 510510){
                    $band_name = mb_strimwidth($table_box2[$i][$theday], 0, 10, '…', 'utf8');
                    $table .= <<<_HTML_
                        <td class="$table_class table_content">
                            <div class="table_box">
                            <a class="booking_window" $confirmURL>
                                <div class="table_Symbol band_name">
                                    $band_name
                                </div>
                                <div class="table_some">
                                    <span class="table_detail">詳細</span>
                                </div>
                            </a>
                            </div>
                        </td>
                    _HTML_;
                }
                else{
                    $band_name = mb_strimwidth($table_box2[$i][$theday], 0, 10, '…', 'utf8');
                    $table .= <<<_HTML_
                        <td class="$table_class table_content">
                            <div class="table_box">
                            <a class="booking_window" $bookingURL>
                                <div class="table_Symbol band_name">
                                    $band_name
                                </div>
                                <div class="table_some">
                                    <span class="table_booking">予約</span>
                                    <span class="table_detail"><a $confirmURL>詳細</a></span>
                                </div>
                            </a>
                            </div>
                        </td>
                    _HTML_;
                }
            }
            elseif(mb_strlen($table_box[$i][$theday]) == 0){
                $table .= <<<_HTML_
                    <td class="$table_class table_content">
                        <div class="table_box">
                        <a class="booking_window" $bookingURL>
                            <div class="table_Symbol circle">
                                <span class="material-symbols-outlined">circle</span>
                            </div>
                            <div class="table_some">
                                <span class="table_booking">予約</span>
                            </div>
                        </a>
                        </div>
                    </td>
                _HTML_;
            }
            else{
                if(substr($table_box[$i][$theday], -7, -1) == "510510"){
                    $band_name = mb_strimwidth($table_box2[$i][$theday], 0, 10, '…', 'utf8');
                    $table .= <<<_HTML_
                        <td class="$table_class table_content">
                            <div class="table_box">
                                <a class="booking_window" $confirmURL>
                                    <div class="table_Symbol band_name">$band_name</div>
                                    <div class="table_some">
                                        <span class="table_detail">詳細</span>
                                    </div>
                                </a>
                            </div>
                        </td>
                    _HTML_;
                }
                else{
                    if(substr($table_box[$i][$theday], 16, 17) == "1"){
                        $band_name = mb_strimwidth($table_box2[$i][$theday], 0, 10, '…', 'utf8');
                        $table .= <<<_HTML_
                            <td class="$table_class table_content">
                                <div class="table_box">
                                    <a class="booking_window" $bookingURL>
                                        <div class="table_Symbol band_name">
                                            $band_name
                                        </div>
                                        <div class="table_some">
                                            <span class="table_booking">予約</span>
                                            <span class="table_detail"><a $confirmURL>詳細</a></span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        _HTML_;
                    }
                    elseif(substr($table_box[$i][$theday], 16, 17) == "0"){
                        $band_name = mb_strimwidth($table_box2[$i][$theday], 0, 10, '…', 'utf8');
                        $table .= <<<_HTML_
                            <td class="$table_class table_content">
                                <div class="table_box">
                                    <a class="booking_window" $confirmURL>
                                        <div class="table_Symbol band_name">
                                            $band_name
                                        </div>
                                        <div class="table_some">
                                            <span class="table_detail">詳細</span>
                                        </div>
                                    </a>
                                </div>
                            </td>
                        _HTML_;
                    }
                }
            }
        }
        $table .= '</tr>';
    }
?>

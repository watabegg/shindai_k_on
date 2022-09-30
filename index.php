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
        $dbh = new PDO($localdsn, $localuser, $localpass);
        $res = $dbh->query("SELECT booking_id, day, time, regist_name FROM booking WHERE day >= '$toweekSun' AND day <= '$toweekSat'");
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

    $table = '<tr><th></th>'; 
    for($i = 0; $i < 7; $i++) {
        list($y, $m, $d) = getAnyDay($Y_m_d, $i, 'split');
        $slt = getAnyDay($Y_m_d, $i, 'Y/m/d');
        $table .= '<th class="'.$Enweek[$i].'">'.$slt.'('.$week[$i].')</th>';
    }
    $table .= '</tr>';
    for($i = 0; $i < count($time); $i++) {
        $table .= '<tr><th class="'. $row_style[$i % 2] . ' table_time">' .$time[$i].'</th>';
        for($j = 0; $j < 7; $j++) {
            list($y, $m, $d) = getAnyDay($Y_m_d, $j, 'split');
            $theday = getAnyDay($Y_m_d, $j, 'Y-m-d');
            $table_class = isBorA($y, $m, $d);
            if(mb_strlen($table_box[$i][$theday]) > 17){
                $id_some = explode(",", $table_box[$i][$theday]);
                $part_some = [];
                for($k=0; $k < count($id_some); $k++){
                    $part_some[$k] = intval(substr($id_some[$k], -7, -1));
                }
                if(array_product($part_some) == 510510){
                    $table .= <<<_HTML_
                        <td class="$table_class">
                            <div class="table_box">
                            <a class="booking_window" href="./confirm/index.html?day=$theday&num=$i">
                                <div class="table_Symbol">
                                    個人練習
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
                    $table .= <<<_HTML_
                        <td class="$table_class">
                            <div class="table_box">
                            <a class="booking_window" href="./booking/index.html?day=$theday&num=$i">
                                <div class="table_Symbol">
                                    <span class="material-symbols-outlined">pentagon</span>
                                </div>
                                <div class="table_some">
                                    <span class="table_booking">予約</span>
                                    <span class="table_detail"><a href="./confirm/index.html?day=$theday&num=$i">詳細</a></span>
                                </div>
                            </a>
                            </div>
                        </td>
                    _HTML_;
                }
            }
            elseif(mb_strlen($table_box[$i][$theday]) == 0){
                $table .= <<<_HTML_
                    <td class="$table_class">
                        <div class="table_box">
                        <a class="booking_window" href="./booking/index.html?day=$theday&num=$i">
                            <div class="table_Symbol">
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

                    $table .= <<<_HTML_
                        <td class="$table_class">
                            <div class="table_box">
                            <a class="booking_window" href="./confirm/index.html?day=$theday&num=$i">
                                <div class="table_Symbol">
                                    {$table_box2[$i][$theday]}
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
                    if(substr($table_box[$i][$theday], 16, 17) == "1"){
                        $table .= <<<_HTML_
                            <td class="$table_class">
                                <div class="table_box">
                                <a class="booking_window" href="./booking/index.html?day=$theday&num=$i">
                                    <div class="table_Symbol">
                                        <span class="material-symbols-outlined">pentagon</span>
                                    </div>
                                    <div class="table_some">
                                        <span class="table_booking">予約</span>
                                        <span class="table_detail"><a href="./confirm/index.html?day=$theday&num=$i">詳細</a></span>
                                    </div>
                                </a>
                                </div>
                            </td>
                        _HTML_;
                    }
                    elseif(substr($table_box[$i][$theday], 16, 17) == "0"){
                        $table .= <<<_HTML_
                            <td class="$table_class">
                                <div class="table_box">
                                <a class="booking_window" href="./confirm/index.html?day=$theday&num=$i">
                                    <div class="table_Symbol">
                                        個人練習
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
            //$table .= '<td class="today"><a class="booking_window" href="./booking/index.html?day='.$theday.'&num='.$i.'">' .'×<br>予約を確認'.'</a></td>';
        }
        $table .= '</tr>';
    }
?>

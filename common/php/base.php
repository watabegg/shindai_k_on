<?php
    date_default_timezone_set('Asia/Tokyo'); //東京時間にする
    $week = ['日','月','火','水','木','金','土']; //曜日日本語表記のため
    $Mweek = ['月','火','水','木','金','土','日'];
    $Enweek = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    $row_style = ['even', 'odd'];
    $part_jp = ['ボーカル', 'ギター(マーシャル)', 'ギター(ジャズコーラス)', 'ベース', 'ドラム', 'キーボード', 'その他'];
    $otherpart_jp = ['なし', 'あり'];
    $prime_num = [2,3,5,7,11,13,17];
    //$time =['7:30~9:00','9:00~10:30','10:40~12:10','12:10~13:30','13:30~15:00','15:10~16:40','16:50~18:20','18:30~20:00','20:00~21:30'];
    $localtimecsv = 'http://localhost/shindai_k_on/common/csv/time.csv'; //ローカル
    $timecsv = 'http://' . $_SERVER['HTTP_HOST'] . '/common/csv/time.csv';

    $dsn = 'mysql:dbname=reservation;host=localhost;charset=utf8';
    $user = 'root';
    $pass = '';

    $timefile = fopen($localtimecsv, "r");
    $time = fgetcsv($timefile);
    fclose($timefile);

    function decode_html($word){
        return html_entity_decode($word);
    }

    function getToday($date = 'Y-m-d') {
        $today = new DateTime();
        return $today->format($date);
    }

    function isToday($year, $manth, $day) {
        $today = getToday();
        if ($today == $year. "-". $manth. "-". $day) {
            return true;
        }
        return false;
    }

    function getSunday() {
        $today = new DateTime();
        $w = $today->format('w');
        $ymd = getToday();

        $next_prev = new DateTime($ymd);
        $next_prev->modify("-{$w} day");
        return $next_prev->format('Ymd');
    }

    function getMonday() {
        $today = new DateTime();
        $w = $today->format('w');
        $ymd = getToday();

        if ($w==0) { $d = 6; }
        else { $d = $w -1; }

        $next_prev = new DateTime($ymd);
        $next_prev->modify("-{$d} day");
        return $next_prev->format('Ymd');
    }

    function isBorA($year, $month, $day){
        $today = getToday('Ymd');
        $days = $year . $month . $day;
        if($today == $days){
            return 'today';
        }
        elseif($today > $days){
            return 'Past';
        }
        elseif($today < $days){
            return 'Future';
        }
    }

    //日付と形式から±n週w曜日の日付を$formatで出力($formatの入力対応はY-m-d,Y年m月d日,Y/m/d, Ymd,split)
    //これなんで動いてんのかよくわかんなくなってきた(DatetimeってYmdの入力が動くけど…？)
    function getAnyDay($Ymd, $w, $format='Y-m-d', $n='+0'){
        global $Enweek;
        $Anyday = new DateTime($Ymd. ' '.$n. ' weeks'. ' ' .$Enweek[$w]);
        if($format == 'split'){
            return [$Anyday->format('Y'), $Anyday->format('m'), $Anyday->format('d')];
        }
        else{
            return $Anyday->format($format);
        }
    }

    function prime_fact($number, $prime_list = [2,3,5,7,11,13,17]){
        $out_list = [];
        foreach($prime_list as $prime){
            if($number % $prime == 0){
                array_push($out_list, $prime);
            }
        }
        return $out_list;
    }

    //一応置き場(多分使わないのでリリースで消す)
    /*
    function getNthDay($year, $month, $day, $n) {
        $next_prev = new DateTime($year.'-'.$month.'-'.$day);
        $next_prev->modify($n);
        return $next_prev->format('Ymd');
    }

    function getYMD($year, $month, $day, $i=0) {
        $ymd = getNthDay($year, $month, $day, '+'.$i.'day');
        $y = substr($ymd, 0, 4);
        $m = substr($ymd, 4, 2);
        $d = substr($ymd, 6, 2);
        $t = $y. '年'. $m. '月'. $d. '日';
        $slt = $y. '/'. $m. '/'. $d;
        $hyt = $y. '-'. $m. '-'. $d;

        return [$t, $slt, $hyt, $y, $m, $d];
    }
    */
?>
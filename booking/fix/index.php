<?php
    session_start();

    require('../../common/php/base.php');

    if(isset($_GET['id'])){
        if(strlen($_GET['id']) == 17){
            $id = $_GET['id'];
        }
    } //ここに例外処理を追加(2022-10-23追記)

    $Ymdday = intval(substr($id, 0, 8));
    $days = getAnyDay($Ymdday, 0);
    $times = intval(substr($id, 8, 2));

    $page_flag = 0;

    try{
        $dbh = new PDO($dsn, $user, $pass);
        $IdRs = $dbh->query("SELECT * FROM booking WHERE booking_id = '$id'");
        $Iddate = $IdRs->fetch(PDO::FETCH_ASSOC);
    }
    catch(PDOException $e){
        $e->getMessage();
    }
    $dbh = null;
    
    $timeselect = '<select name="time" required>';
    for($i=0; $i < count($time); $i++){
        if($i == $Iddate['time']){
            $timeselect .= '<option value="'. $i . '" selected>' . $time[$i] . '</option>';
        }
        else{
            $timeselect .= '<option value="'.$i.'">' . $time[$i] . '</option>';
        }
    }
    $timeselect .= '</select>';

    $MaxDate = getAnyDay($today->format('Ymd'), 6, 'Y-m-d', '+3');
    $dayselect = '<input type="date" name="day" value="'. $days . '" min="'. $today->format('Y-m-d') .'" max="'. $MaxDate .'" required>';

    $selectedpart = [];
    for($i=0; $i < count($part_jp); $i++){
        $selectedpart[$i] = null;
    }
    $prime = prime_fact($Iddate["part"]);
    for($i=0; $i < count($prime); $i++){
        $j = array_search($prime[$i], $prime_num);
        $selectedpart[$j] = 'selected';
    }
    $partselect = '<select multiple="multiple" class="multiple-select" name="part[]" required>';
    for($i=0; $i < count($part_jp); $i++){
        $partselect .= <<<_HTML_
            <option value="{$prime_num[$i]}" {$selectedpart[$i]}>{$part_jp[$i]}</option>
        _HTML_;
    }
    $partselect .= '</select>';

    function fix(){
        echo <<<_HTML_

        _HTML_;
    }
    function fix_confirm(){
        echo <<<_HTML_

        _HTML_;
    }
    function complete(){
        echo <<<_HTML_
        <div>
            <h1>完了しました！</h1>
            <button type="button" onclick="location.href='http://localhost/shindai_k_on/index.html'">ホームに戻る</button>
        </div>
        _HTML_;
    }
    function delete(){
        echo <<<_HTML_

        _HTML_;
    }
    function delete_confirm(){
        echo <<<_HTML_

        _HTML_;
    }
    
?>
<?php
    session_start();

    require('../common/php/base.php');

    $today = new DateTime();
    
    //GETの取得
    $days = $_GET['day'] ?? $today->format('Y-m-d');
    $num = $_GET['num'] ?? 0;
    $status = $_GET['status'] ?? 'close';

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

    $MaxDate = getAnyDay($today->format('Ymd'), 6, 'Y-m-d', '+3');
    $dayselect = '<input type="date" name="day" value="'. $days . '" min="'. $today->format('Y-m-d') .'" max="'. $MaxDate .'" required>';

    try{
        $dbh = new PDO($dsn, $user, $pass);
        $res = $dbh->query("SELECT booking_id, regist_name, part, otherpart, remark, name FROM booking WHERE day = '$days' AND time = '$num'");
        $date = $res->fetchAll(PDO::FETCH_ASSOC);
        $count = $res->rowCount();
    }
    catch(PDOException $e){
        $e->getMessage();
    }
    
    $usedpart = [];
    for($i=0; $i < count($part_jp); $i++){
        $usedpart[$i] = null;
    }
    for($i=0; $i < $count; $i++){
        $prime = prime_fact($date[$i]["part"]);
        for($j=0; $j < count($prime); $j++){
            $k = array_search($prime[$j], $prime_num);
            $usedpart[$k] = 'disabled';
        }
    }
    $partselect = '<select multiple="multiple" class="multiple-select" name="part[]" required>';
    for($i=0; $i < count($part_jp); $i++){
        $partselect .= <<<_HTML_
            <option value="{$prime_num[$i]}" {$usedpart[$i]}>{$part_jp[$i]}</option>
        _HTML_;
    }
    
    if($count > 0){
        if($date[0]['otherpart'] == 1){
            $otherpartselect = <<<_HTML_
                <input type="hidden" name="otherpart" value="1">
                <input type="radio" name="otherpart" checked disabled>あり
                <input type="radio" name="otherpart" value="0" disabled>なし
            _HTML_;
        }
        elseif($date[0]['otherpart'] == 0){
            $otherpartselect = <<<_HTML_
                <input type="radio" name="otherpart" disabled>あり
                <input type="hidden" name="otherpart" value="0">
                <input type="radio" name="otherpart" value="0" checked disabeld>なし
            _HTML_;
        }
    }
    else{
        $otherpartselect = <<<_HTML_
            <input type="radio" name="otherpart" value="1">あり
            <input type="radio" name="otherpart" value="0" checked>なし
        _HTML_;
    }

    $page_flag = 0;

    //確認フォームがPOSTされたときの処理
    if(isset($_POST['form_confirm'])) {
        $page_flag = 1;

        $select_day = htmlspecialchars($_POST['day'], ENT_QUOTES);
        $select_time = htmlspecialchars($_POST['time'], ENT_QUOTES);
        $regist_name = htmlspecialchars($_POST['regist_name'], ENT_QUOTES);
        $part = $_POST['part'];
        $otherpart = htmlspecialchars($_POST['otherpart'], ENT_QUOTES);
        $remark = htmlspecialchars($_POST['remark'], ENT_QUOTES);
        $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES);

        //素数の積
        $part_pro = array_product($part);

        //パスワード暗号化(文字数のみ'･'で取得)
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_len = str_repeat('･', mb_strlen($password));

        $YMD_day = str_replace('-', '', $select_day);
        //17桁のID(文字列)
        $booking_id = strval($YMD_day) . strval(sprintf('%02d', $select_time)) . strval(sprintf('%06d', $part_pro)) . strval($otherpart);

        //データを一時的にCSVに格納
        $uniq_name = uniqid('', true);
        $csv_date = [$booking_id, $select_day, $select_time, $regist_name, $part_pro, $otherpart, $remark, $name, $pass_hash];
        $csv_name = './csv/' .$uniq_name. '.csv';
        $f = fopen($csv_name, "w");
        fputcsv($f, $csv_date);
        fclose($f);
    }
    //予約フォームがPOSTされたときの処理
    elseif(isset($_POST['form_submit'])){
        $page_flag = 2;

        //CSVを読んで削除
        $csv_name = './csv/' .$_POST['uniq_csv'].'.csv';
        $csv_file = fopen($csv_name, "r");
        $csv_date = fgetcsv($csv_file);
        fclose($csv_file);
        unlink($csv_name);

        list($booking_id, $select_day, $select_time, $regist_name, $part_pro, $otherpart, $remark, $name, $pass_hash) = $csv_date;
    
        try{
            $dbh = new PDO($dsn, $user, $pass);
            $dbh->query("INSERT INTO booking (booking_id, day, time, regist_name, part, otherpart, remark, name, password)
             VALUES ('$booking_id', '$select_day', '$select_time', '$regist_name', '$part_pro', '$otherpart','$remark', '$name', '$pass_hash')");
        }catch (PDOException $e) {
            print 'エラー' .PHP_EOL. $e->getMessage();
        }
    }
    //戻るフォームがPOSTされたときの処理
    elseif(isset($_POST['form_back'])){
        $page_flag = 0;

        //CSVを削除
        $csv_name = './csv/' .$_POST['uniq_csv'].'.csv';
        unlink($csv_name);
    }

    function booking(){
        global $dayselect, $timeselect, $partselect, $otherpartselect;
        echo <<<_HTML_
        <h1 class="title">信州大学軽音楽部 予約フォーム</h1>
        <div id="bookingMain">
            <form method="POST" action="">
            <table>
                <tr>
                    <td class="menu">予約日：</td>
                    <td>$dayselect</td>
                </tr>
                <tr>
                    <td class="menu">予約時間：</td>
                    <td>$timeselect</td>
                </tr>
                <tr>
                    <td class="menu">登録名：</td>
                    <td><input type="text" name="regist_name" maxlength="70" placeholder="例：ギター個人練習" required></td>
                </tr>
                <tr>
                    <td class="menu">パート：</td>
                    <td>$partselect</td>
                </tr>
                <tr>
                    <td class="menu">他パート参加：</td>
                    <td>$otherpartselect</td>
                </tr>
                <tr>
                    <td class="menu">備考：</td>
                    <td><textarea name="remark"></textarea></td>
                </tr>
                <tr>
                    <td class="menu">予約者名：</td>
                    <td><input type="text" name="name" maxlength="20" required></td>
                </tr>
                <tr>
                    <td class="menu">パスワード：</td>
                    <td><input type="password" name="password" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="form_confirm" value="確認"></td>
                </tr>
            </table>
            </form>
        <script>
            $(function () {
                //$('.multiple-select').multipleSelect('checkAll');
                $('.multiple-select').multipleSelect({
                    width: 250,
                    formatSelectAll: function() {
                        return 'バンド練習';
                    },
                    formatAllSelected: function() {
                        return 'バンド練習';
                    },
                    placeholder: '使用パートを選択してください'
                });
            });
        </script>
        </div>
        _HTML_;
    }

    function confirm(){
        global $select_day, $time, $select_time, $regist_name, $part, $otherpart, $remark, $name, $password_len, $uniq_name, $part_jp, $otherpart_jp, $prime_num;

        $part_each = NULL;

        if(count($part) == 7){
            $part_each .= 'バンド';
        }
        else{
            foreach($part as $i){
                $j = array_search($i, $prime_num);
                $part_each .= $part_jp[$j] . ', ';
            }
            $part_each = substr($part_each, 0, -2);
        }
    

        echo <<<_HTML_
        <div id="bookingConfirm">
            <h1 class="title">信州大学軽音楽部 予約フォーム確認</h1>
            <form method="POST" action="">
            <table>
                <tr>
                    <td class="menu">予約日：</td>
                    <td>$select_day</td>
                </tr>
                <tr>
                    <td class="menu">予約時間：</td>
                    <td>$time[$select_time]</td>
                </tr>
                <tr>
                    <td class="menu">登録名：</td>
                    <td>$regist_name</td>
                </tr>
                <tr>
                    <td class="menu">パート：</td>
                    <td>$part_each</td>
                </tr>
                <tr>
                    <td class="menu">他パート参加：</td>
                    <td>$otherpart_jp[$otherpart]</td>
                </tr>
                <tr>
                    <td class="menu">備考：</td>
                    <td>$remark</td>
                </tr>
                <tr>
                    <td class="menu">予約者名：</td>
                    <td>$name</td>
                </tr>
                <tr>
                    <td class="menu">パスワード：</td>
                    <td><b>$password_len</b></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <input type="hidden" name="uniq_csv" value="$uniq_name">
                        <input type="submit" name="form_back" value="戻る">
                        <input type="submit" name="form_submit" value="予約">
                    </td>
                </tr>
            </table>
            </form>
        </div>
        _HTML_;
    }

    function complete(){
        echo <<<_HTML_
        <div id="CompleteMain">
            <h1>完了しました！</h1>
            <button type="button" onclick="location.href='http://localhost/shindai_k_on/index.html'">ホームに戻る</button>
        </div>
        _HTML_;
    }

    function confirm_page(){
        global $date, $days, $num, $part_jp, $otherpart_jp, $prime_num, $time, $count;

        $booking_confirm = '';

        for($i=0; $i < $count; $i++){
            $part_each = NULL;
            if($date[$i]['part'] == 510510){
                $part_each .= 'バンド';
            }
            else{
                foreach(prime_fact($date[$i]['part']) as $prime){
                    $j = array_search($prime, $prime_num);
                    $part_each .= $part_jp[$j] . ',';
                }
                $part_each = rtrim($part_each, ',');
            }
            $k = $i + 1;
            $booking_confirm .= <<<_HTML_
                <div class="confirm_page">
                    <h1 class="title">信州大学軽音楽部 予約確認ページ</h1>
                    <h3>予約日:$days    予約時間:$time[$num]</h3>
                    <span>{$k}件目</span>
                    <table>
                        <tr>
                            <td class="menu">登録名：</td>
                            <td>{$date[$i]['regist_name']}</td>
                        </tr>
                        <tr>
                            <td class="menu">パート：</td>
                            <td>$part_each</td>
                        </tr>
                        <tr>
                            <td class="menu">他パート参加：</td>
                            <td>{$otherpart_jp[$date[$i]['otherpart']]}</td>
                        </tr>
                        <tr>
                            <td class="menu">備考：</td>
                            <td>{$date[$i]['remark']}</td>
                        </tr>
                        <tr>
                            <td class="menu">予約者名：</td>
                            <td>{$date[$i]['name']}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><a>予約を修正</a></td>
                            <td><a>予約を削除</a></td>
                        </tr>
                    </table>
                </div>
            _HTML_;
        }
        echo $booking_confirm;
    }

?>
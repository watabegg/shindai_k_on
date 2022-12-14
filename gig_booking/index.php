<?php

    require('../common/php/base.php');

    $today = new DateTime();


    $weekselect = NULL;
    for($i=0; $i < 7; $i++){
        $weekselect .= '<option value="'.$i.'">'.$week[$i].'</option>';
    }
    
    $timeselect = NULL;
    for($i=0; $i < count($time); $i++){
        $timeselect .= '<option value="'.$i.'">'.$time[$i].'</option>';
    }

    $komaselecttable = '<table><tr><th></th><th>曜日</th><th>時間</th></tr>';
    for($i=1; $i < 4; $i++){
        $komaselecttable .= <<<_HTML_
            <tr>
                <th>第{$i}希望</th>
                <td>
                    <select class="single-select" name="weekreq$i" required>
                        $weekselect
                    </select>
                </td>
                <td>
                    <select class="single-select" name="timereq$i" required>
                        $timeselect
                    </select>
                </td>
            </tr>
        _HTML_;
    }
    $komaselecttable .= '</table>';

    $page_flag = 0;

    //確認フォームがPOSTされたときの処理
    if(isset($_POST['koma_confirm'])) {
        $page_flag = 1;

        $band_name = htmlspecialchars($_POST['band_name'], ENT_QUOTES);
        $repre_name = htmlspecialchars($_POST['repre_name'], ENT_QUOTES);
        $grade = htmlspecialchars($_POST['grade'], ENT_QUOTES);
        $weekreq1 = htmlspecialchars($_POST['weekreq1'], ENT_QUOTES);
        $weekreq2 = htmlspecialchars($_POST['weekreq2'], ENT_QUOTES);
        $weekreq3 = htmlspecialchars($_POST['weekreq3'], ENT_QUOTES);
        $timereq1 = htmlspecialchars($_POST['timereq1'], ENT_QUOTES);
        $timereq2 = htmlspecialchars($_POST['timereq2'], ENT_QUOTES);
        $timereq3 = htmlspecialchars($_POST['timereq3'], ENT_QUOTES);
        $remark = htmlspecialchars($_POST['remark'], ENT_QUOTES);
        $password = htmlspecialchars($_POST['password'], ENT_QUOTES);

        //パスワード暗号化(文字数のみ'･'で取得)
        $pass_hash = password_hash($password, PASSWORD_DEFAULT);
        $password_len = str_repeat('･', mb_strlen($password));

        //17桁のID(文字列)
        $booking_id = strval($YMD_day) . strval(sprintf('%02d', $select_time)) . strval(sprintf('%06d', $part_pro)) . strval($otherpart);

        //データを一時的にCSVに格納
        $uniq_name = uniqid('', true);
        $csv_date = [$booking_id, $select_day, $select_time, $regist_name, $part_pro, $otherpart, $remark, $name, $pass_hash];
        $csv_name = $uniq_name. '.csv';
        $f = fopen($csv_name, "w");
        fputcsv($f, $csv_date);
        fclose($f);
    }
    //予約フォームがPOSTされたときの処理
    elseif(isset($_POST['koma_submit'])){
        $page_flag = 2;

        //CSVを読んで削除
        $csv_name = $_POST['uniq_csv'].'.csv';
        $csv_file = fopen($csv_name, "r");
        $csv_date = fgetcsv($csv_file);
        fclose($csv_file);
        unlink($csv_name);

        list($booking_id, $select_day, $select_time, $regist_name, $part_pro, $otherpart, $remark, $name, $pass_hash) = $csv_date;

        $localdsn = 'mysql:dbname=reservation;host=localhost;charset=utf8';
        $localuser = 'root';
        $localpass = '';
    
        try{
            $dbh = new PDO($localdsn, $localuser, $localpass);
            $dbh->query("INSERT INTO booking (booking_id, day, time, regist_name, part, otherpart, remark, name, password)
             VALUES ('$booking_id', '$select_day', '$select_time', '$regist_name', '$part_pro', '$otherpart','$remark', '$name', '$pass_hash')");
        }catch (PDOException $e) {
            print 'エラー' .PHP_EOL. $e->getMessage();
        }
    }
    //戻るフォームがPOSTされたときの処理
    elseif(isset($_POST['koma_form_back'])){
        $page_flag = 0;

        //CSVを削除
        $csv_name = $_POST['uniq_csv'].'.csv';
        unlink($csv_name);
    }

    function koma_booking(){
        global $komaselecttable;
        echo <<<_HTML_
        <h1 class="title">信州大学軽音楽部 コマ割り応募フォーム</h1>
        <div id="gigbookingMain">
            <form method="POST" action="">
            <table>
                <tr>
                    <td class="menu">募集タイトル：</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="menu">募集期間：</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="menu">バンド名：</td>
                    <td><input type="text" name="band_name" maxlength="50" placeholder="例:はっぴいえんどのコピバン" required></td>
                </tr>
                <tr>
                    <td class="menu">代表者氏名：</td>
                    <td><input type="name" name="repre_name" maxlength="30" placeholder="LINEで使ってる名前！" required></td>
                </tr>
                <tr>
                    <td class="menu">代表者学年：</td>
                    <td>
                        <select class="single-select" name="grade" required>
                            <option value="1" selected>学部1年</option>
                            <option value="2">学部2年</option>
                            <option value="3">学部3年</option>
                            <option value="4">学部4年</option>
                            <option value="5">学部5回生以上(備考に記入)</option>
                            <option value="6">修士1年</option>
                            <option value="7">修士2年</option>
                            <option value="8">修士3年以上(備考に記入)</option>
                            <option value="9">その他(備考に記入)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="menu">コマ希望：</td>
                    <td>
                        $komaselecttable
                    </td>
                </tr>
                <tr>
                    <td class="menu">備考：</td>
                    <td><textarea name="remark"></textarea></td>
                </tr>
                <tr>
                    <td class="menu">パスワード：</td>
                    <td><input type="password" name="password" required></td>
                </tr>
                <tr>
                    <td></td>
                    <td><input type="submit" name="koma_confirm" value="確認"></td>
                </tr>
            </table>
            </form>
        <script>
            $(function () {
                $('select').multipleSelect({
                    singleRadio: true
                })
            })
        </script>
        </div>
        _HTML_;
    }

    function koma_confirm(){
        global $select_day, $time, $select_time, $regist_name, $part, $otherpart, $remark, $name, $password_len, $uniq_name;
        $part_jp = ['ボーカル', 'ギター(マーシャル)', 'ギター(ジャズコーラス)', 'ベース', 'ドラム', 'キーボード', 'その他'];
        $otherpart_jp = ['なし', 'あり'];
        $prime_num = [2,3,5,7,11,13,17];

        $part_each = NULL;

        if(count($part) == 7){
            $part_each .= 'バンド';
        }
        else{
            foreach($part as $i){
                $j = array_search($i, $prime_num);
                $part_each .= $part_jp[$j] . ', ';
            }
            
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
                    <td>{$time[$select_time]}</td>
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
                    <td>{$otherpart_jp[$otherpart]}</td>
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
                        <input type="submit" name="koma_form_back" value="戻る">
                        <input type="submit" name="koma_submit" value="予約">
                    </td>
                </tr>
            </table>
            </form>
        </div>
        _HTML_;
    }

    function koma_complete(){
        echo <<<_HTML_
        <div id="CompleteMain">
            <h1>完了しました！</h1>
            <button type="button" onclick="location.href='http://localhost/shindai_k_on/index.html'">ホームに戻る</button>
        </div>
        _HTML_;
    }

?>
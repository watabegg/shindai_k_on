<?php
if(isset($_POST['time'])) {
    $select_day = htmlspecialchars($_POST['day'], ENT_QUOTES);
    $select_time = htmlspecialchars($_POST['time'], ENT_QUOTES);
    $regist_name = htmlspecialchars($_POST['regist_name'], ENT_QUOTES);
    $part = $_POST['part'];
    $otherpart = htmlspecialchars($_POST['otherpart'], ENT_QUOTES);
    $remark = htmlspecialchars($_POST['remark'], ENT_QUOTES);
    $name = htmlspecialchars($_POST['name'], ENT_QUOTES);
    $password = htmlspecialchars($_POST['password'], ENT_QUOTES);

    $partjson = json_encode($part);

    $localdsn = 'mysql:dbname=reservation;host=localhost;charset=utf8';
    $localuser = 'root';
    $localpass = 'Qx76pd3aa';

    try{
        $dbh = new PDO($localdsn, $localuser, $localpass);
        $dbh->query("INSERT INTO booking (ban, day, time, regist_name, part, otherpart, remark, name, password)
         VALUES (NULL, '$select_day', '$select_time', '$regist_name', '$partjson', '$otherpart','$remark', '$name', '$password')");
    }catch (PDOException $e) {
        print 'エラー' .PHP_EOL. $e->getMessage();
    }
}
?>
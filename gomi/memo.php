        <!--
        <tr>
            <th></th>
            <?php
                for($i = 0; $i < 7; $i++){
                    echo '<th>'. $SunD->format('Y年m月d日'). '<br>'; //日付習得
                    echo $week[$i].'<br>'; //日本語曜日習得
                    $SunD->modify("+1 day");
                }
                $SunD->modify("-6 day");
            ?>
            </th>
        </tr>
        <?php
            for($i = 0; $i < count($time); $i++){
                echo '<tr><th class="'. $row_style[$i % 2] . '">' .$time[$i].'</th>';
                for($j = 0; $j < 7; $j++){
                    $Uday = new DateTime("last Sunday + $j day");
                    //echo '<form method="POST" action="booking.php">';
                    echo '<td align="center"><a class="booking_window" href="booking.php?day='.$Uday->format('Y/m/d').'&time='.$time[$i].'">' .$Uday->format('Y/m/d').'<br>'.$time[$i].'</a></td>';
                    //echo '</form>';
                }
                echo '</tr>';

            }
        ?>
        -->
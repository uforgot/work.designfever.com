<?php
/**
 * Created by IntelliJ IDEA.
 * User: uforgot
 * Date: 2018. 8. 7.
 * Time: PM 3:01
 */

?>

<article class="tile is-child card">
    <input type="hidden" name="time_gubun" id="time_gubun" value="<?=$time_gubun?>">
    <div id="df-clock">
        <div class="clock-header" id="df-clock-header">
            <div class="date" id="df-clock-date">&nbsp;
                <span id="df-clock-day"></span>
            </div>
            <div class="menu">
                <span>현재시간</span>
                <!--                                                    <span>남은시간</span>-->
            </div>
        </div>

        <div class="clock-body" id="df-clock-body">
            <div class="clock-wrapper" id="df-clock-wrapper">
                <span class="large" id="df-clock-hour"></span>
                <span class="large" id="df-clock-minute"></span>
                <span class="small" id="df-clock-second"></span>
            </div>
        </div>

        <div class="clock-footer" id="df-clock-footer">
            <?
                if (!in_array($prs_id,$NoCommuting_arr)) {

                    if ($today_gubun1 >= 10) {
                        echo "<div class=\"notice\">휴가계를 제출하셨습니다.<br>출퇴근체크를 원하시면 휴가계 삭제를 요청해 주세요.</div></div>";

                    } else {
                        echo "<div class=\"info\" id=\"df-clock-info\">";



                            echo "<ul>";
                            echo "<li><dt>출근</dt><dd>";
                            //출근 시간
                            if ($time_gubun == "before") {
                                if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "") {
                                    echo substr($yesterday_checktime1, 8, 2) . ":" . substr($yesterday_checktime1, 10, 2);
                                } else {
                                    echo('-- : --');
                                }
                            } else {
                                if ($today_checktime1 == "") {
                                    echo('-- : --');
                                } else {
                                    echo substr($today_checktime1, 8, 2) . ":" . substr($today_checktime1, 10, 2);
                                }
                            }
                            echo "</dd></li>";

                            //외출 시간
                            echo "<li><dt>외출</dt><dd>";

                            if ($off_check == "Y") {
                                if ($last_off_endtime == "") {
                                    echo substr($last_off_starttime, 0, 2) . ":" . substr($last_off_starttime, 2, 2) . " ~ -- : --";
                                } else {
                                    echo substr($last_off_starttime, 0, 2) . ":" . substr($last_off_starttime, 2, 2) . " ~ " . substr($last_off_endtime, 0, 2) . ":" . substr($last_off_endtime, 2, 2);
                                }
                            } else {
                                if ($today_checktime1 != "") {
                                    echo "-- : --";
                                } else {
                                    echo substr($last_off_starttime, 0, 2) . ":" . substr($last_off_starttime, 2, 2) . " ~ " . substr($last_off_endtime, 0, 2) . ":" . substr($last_off_endtime, 2, 2);
                                }
                            }
                            if ($off_check == "Y") {
                                if ($last_off_endtime == "") {
                                    $end_check = "N";
                                } else {
                                    $end_check = "Y";
                                }
                            } else {
                                $end_check = "Y";
                            }

                            echo "</dd></li>";

                            //외출 시간
                            echo "<li><dt>퇴근</dt><dd>";
                            /*
                                출근체크가 안됐으면 일단 무조건 퇴근체크도 막아야 한다 하지만
                                    조건1. 전일 출근체크가 되어있고
                                    조건2. 자정넘어 아침8시 이전이고
                                    조건3. 자정넘어 출근체크가 안되어있고
                                    조건4. 자정넘어 출근체크가 되어 있어도 휴가여야
                                이때는 퇴근체크가 가능하게끔 로직 설정
                            */
                            $gbArray1 = array(4,8,6,10,11,12,13,14,15,16,17,18,19,20,21);
                            $gbArray2 = array(5,9);

                            $chk_gb1 = in_array($today_gubun1,$gbArray1);
                            $chk_gb2 = in_array($today_gubun2,$gbArray2);
                            if ($end_check == "Y")
                            {
                                if ($today_checktime2 != "")												//퇴근 중복체크
                                {
                                    echo substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2) ."</span>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//오늘 출근체크 X, 어제 출근체크 X
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건2)
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건2)
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이후
                                {
                                    echo "-- : --";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건4)
                                {
                                    echo "-- : --";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건4)
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 != "" && $today_checktime2 == "")		//퇴근체크 - 오늘 출근체크 O, 오늘 퇴근체크 X
                                {
                                    echo "-- : --";
                                }
                                else															//오늘 출근체크 X, 오늘 퇴근체크 X
                                {
                                    echo "-- : --";
                                }
                            }
                            else {
                                echo "-- : --";
                            }


                        echo "</dd></li>";
                        echo "</div>";

                        echo "<div class=\"clock-buttons\">";
                        echo "    <div class=\"field is-grouped is-fullwidth\">";

                        if (in_array(REMOTE_IP, $ok_ip_arr))
                        {
                            //출근 버튼
                            if ($time_gubun == "before")
                            {
                                if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "")
                                {
                                }
                                else
                                {
                                    echo "<div class=\"control\"><a href='javascript:go_office();' onClick='return !count++'><div class=\"button is-large is-link\">출근</div></a></div>";
                                }
                            }
                            else
                            {
                                if ($today_checktime1 == "")
                                {
                                    echo "<div class=\"control\"><a href='javascript:go_office();' onClick='return !count++'><div class=\"button is-large is-link\">출근</div></a></div>";
                                }
                                else
                                {
                                }
                            }

                            //외출 버튼
                            if ($off_check == "Y")
                            {
                                if ($last_off_endtime == "")
                                {
                                    echo "<div class=\"control\"><a href='javascript:off_office(\"comeback\");'><div class=\"button is-large is-link\">복귀</div></a></div>";
                                }
                                else
                                {
                                    echo "<div class=\"control\"><a href='javascript:off_office(\"goout\");'><div class=\"button is-large is-link\">외출</div></a></div>";
                                }
                            }
                            else
                            {
                                if ($today_checktime1 != "")
                                {
                                    echo "<div class=\"control\"><a href='javascript:off_office(\"goout\");'><div class=\"button is-large is-link\">외출</div></a></div>";
                                }
                                else
                                {
                                }
                            }

                            //퇴근 버튼
                            if ($end_check == "Y")
                            {
                                if ($today_checktime2 != "")												//퇴근 중복체크
                                {
                                    //echo "<a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='퇴근체크' /></a> <span>". substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2) ."</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">퇴근</div></a></div>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//오늘 출근체크 X, 어제 출근체크 X
                                {

                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건2)
                                {
                                    //echo "<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='퇴근체크' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">퇴근</div></a></div>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건2)
                                {
                                    //echo "<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='퇴근체크' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">퇴근</div></a></div>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//오늘 출근체크 X, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이후
                                {

                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//어제 퇴근체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 X, 08:00이전(조건4)
                                {
                                    //echo "<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='퇴근체크' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">퇴근</div></a></div>";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//어제 퇴근 중복체크 - 오늘 휴가, 어제 출근체크 O, 어제 퇴근체크 O, 08:00이전(조건4)
                                {
                                    //echo "<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='퇴근체크' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">퇴근</div></a></div>";
                                }
                                else if ($today_checktime1 != "" && $today_checktime2 == "")		//퇴근체크 - 오늘 출근체크 O, 오늘 퇴근체크 X
                                {
                                    //echo "<a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='퇴근체크' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">퇴근</div></a></div>";
                                }
                                else															//오늘 출근체크 X, 오늘 퇴근체크 X
                                {

                                }
                            }
                        } else {
                            echo "<div style='margin-bottom:0.6rem'>출퇴근 체크는<br>사내에서만 가능합니다</div>";
                        }

                        echo "    </div>";
                        echo "</div>";

                    }
                }
            ?>

        </div>
    </div>
</article>
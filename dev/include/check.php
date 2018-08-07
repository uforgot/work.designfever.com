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
                <span>����ð�</span>
                <!--                                                    <span>�����ð�</span>-->
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
                        echo "<div class=\"notice\">�ް��踦 �����ϼ̽��ϴ�.<br>�����üũ�� ���Ͻø� �ް��� ������ ��û�� �ּ���.</div></div>";

                    } else {
                        echo "<div class=\"info\" id=\"df-clock-info\">";



                            echo "<ul>";
                            echo "<li><dt>���</dt><dd>";
                            //��� �ð�
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

                            //���� �ð�
                            echo "<li><dt>����</dt><dd>";

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

                            //���� �ð�
                            echo "<li><dt>���</dt><dd>";
                            /*
                                ���üũ�� �ȵ����� �ϴ� ������ ���üũ�� ���ƾ� �Ѵ� ������
                                    ����1. ���� ���üũ�� �Ǿ��ְ�
                                    ����2. �����Ѿ� ��ħ8�� �����̰�
                                    ����3. �����Ѿ� ���üũ�� �ȵǾ��ְ�
                                    ����4. �����Ѿ� ���üũ�� �Ǿ� �־ �ް�����
                                �̶��� ���üũ�� �����ϰԲ� ���� ����
                            */
                            $gbArray1 = array(4,8,6,10,11,12,13,14,15,16,17,18,19,20,21);
                            $gbArray2 = array(5,9);

                            $chk_gb1 = in_array($today_gubun1,$gbArray1);
                            $chk_gb2 = in_array($today_gubun2,$gbArray2);
                            if ($end_check == "Y")
                            {
                                if ($today_checktime2 != "")												//��� �ߺ�üũ
                                {
                                    echo substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2) ."</span>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//���� ���üũ X, ���� ���üũ X
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ X, 08:00����(����2)
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����(����2)
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����
                                {
                                    echo "-- : --";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ X, 08:00����(����4)
                                {
                                    echo "-- : --";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ O, 08:00����(����4)
                                {
                                    echo "-- : --";
                                }
                                else if ($today_checktime1 != "" && $today_checktime2 == "")		//���üũ - ���� ���üũ O, ���� ���üũ X
                                {
                                    echo "-- : --";
                                }
                                else															//���� ���üũ X, ���� ���üũ X
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
                            //��� ��ư
                            if ($time_gubun == "before")
                            {
                                if ($yesterday_checktime1 != "" && $yesterday_checktime2 == "")
                                {
                                }
                                else
                                {
                                    echo "<div class=\"control\"><a href='javascript:go_office();' onClick='return !count++'><div class=\"button is-large is-link\">���</div></a></div>";
                                }
                            }
                            else
                            {
                                if ($today_checktime1 == "")
                                {
                                    echo "<div class=\"control\"><a href='javascript:go_office();' onClick='return !count++'><div class=\"button is-large is-link\">���</div></a></div>";
                                }
                                else
                                {
                                }
                            }

                            //���� ��ư
                            if ($off_check == "Y")
                            {
                                if ($last_off_endtime == "")
                                {
                                    echo "<div class=\"control\"><a href='javascript:off_office(\"comeback\");'><div class=\"button is-large is-link\">����</div></a></div>";
                                }
                                else
                                {
                                    echo "<div class=\"control\"><a href='javascript:off_office(\"goout\");'><div class=\"button is-large is-link\">����</div></a></div>";
                                }
                            }
                            else
                            {
                                if ($today_checktime1 != "")
                                {
                                    echo "<div class=\"control\"><a href='javascript:off_office(\"goout\");'><div class=\"button is-large is-link\">����</div></a></div>";
                                }
                                else
                                {
                                }
                            }

                            //��� ��ư
                            if ($end_check == "Y")
                            {
                                if ($today_checktime2 != "")												//��� �ߺ�üũ
                                {
                                    //echo "<a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='���üũ' /></a> <span>". substr($today_checktime2,8,2) .":". substr($today_checktime2,10,2) ."</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">���</div></a></div>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 == "")			//���� ���üũ X, ���� ���üũ X
                                {

                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ X, 08:00����(����2)
                                {
                                    //echo "<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">���</div></a></div>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����(����2)
                                {
                                    //echo "<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">���</div></a></div>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "after")	//���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����
                                {

                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ X, 08:00����(����4)
                                {
                                    //echo "<a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">���</div></a></div>";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ O, 08:00����(����4)
                                {
                                    //echo "<a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' alt='���üũ' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">���</div></a></div>";
                                }
                                else if ($today_checktime1 != "" && $today_checktime2 == "")		//���üũ - ���� ���üũ O, ���� ���üũ X
                                {
                                    //echo "<a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."');><img src='img/icon_b.gif' title='���üũ' /></a> <span>--:--</span>";
                                    echo "<div class=\"control\"><a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."');><div class=\"button is-large is-danger\">���</div></a></div>";
                                }
                                else															//���� ���üũ X, ���� ���üũ X
                                {

                                }
                            }
                        } else {
                            echo "<div style='margin-bottom:0.6rem'>����� üũ��<br>�系������ �����մϴ�</div>";
                        }

                        echo "    </div>";
                        echo "</div>";

                    }
                }
            ?>

        </div>
    </div>
</article>
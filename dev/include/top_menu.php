<script type="text/javascript">
//��� üũ
	function leave_office(gubun,commute,working){

		if (gubun == 1)
		{
			var msg = "���üũ�� �Ͻðڽ��ϱ�?";
		}
		else if (gubun == 2)
		{
			var msg = "�̹� ���üũ�� �ϼ̽��ϴ�. ���üũ�� �Ͻðڽ��ϱ�?";
		}
		else if (gubun == 3)
		{
			var msg = "��������� ���üũ�� �Ǿ����� �ʽ��ϴ�. \n\���üũ�� �Ͻðڽ��ϱ�? \n\(��ٽð��� ���� ��ٽð����ݿ��˴ϴ�.";
		}
		else if (gubun == 4)
		{
			var msg = "�̹� ���� ���üũ�� �ϼ̽��ϴ�.  \n\���üũ�� �Ͻðڽ��ϱ�? \n\(��ٽð��� ���� ��ٽð��� �ݿ��˴ϴ�.";
		}

		if(confirm(msg)){
			frm = document.form;
			frm.target	= "hdnFrame";
			frm.action = "/commuting/commute_check_act2.php";
			frm.submit();
		}else{
			return;
		}
	}
</script>
<div class="top">

 <nav id="navbar" class="navbar has-shadow is-fixed-top">
    <div class="container">
        <div class="navbar-brand">
            <div class="navbar-item is-size-7">
               <?=$prs_team?>&nbsp;/&nbsp;<strong><?=$prs_position2 ?> <?=$prs_name ?></strong>
           </div>
            <div class="navbar-burger burger" data-target="navbarExampleTransparentExample">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <div class="navbar-menu">
            <div class="navbar-end">
                <div class="navbar-item is-size-7">
                    <? if ($today_gubun1 >= 10){?>
                        �ް��踦 �����ϼ̽��ϴ�.<br>�����üũ�� ���Ͻø� �ް��� ������ ��û�� �ּ���.
                      <? }else if ($today_checktime1 != "" && $today_datekind == "BIZ") { ?>
                            ��� ���� �ð���&nbsp;<strong><?=substr($checkout,0,2)?>:<?=substr($checkout,2,2)?> </strong>&nbsp;�Դϴ�.
                      <? }else{}

                       ?>
                 </div>
                <div class="navbar-item">
                    <div class="field is-grouped">
                    	<!--��� �ϱ� ��ư ��ºκ�-->
                    	<?
                    if ($off_check == "Y")
                        {
                            if ($last_off_endtime == "")
                            { $end_check = "N"; }
                            else
                            { $end_check = "Y"; }
                        }
                        else{ $end_check = "Y"; }
                    //��� ��ư ���
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

                                if ($today_gubun1 >= 10){
                                 ?>
                                    <!--�ް��ϰ�� ��ư ����-->
                                <? }else if ($today_checktime2 != "")												//��� �ߺ�üũ
                                {

                                    echo "<p class='control'>
                                            <a href=javascript:leave_office(2,'". $today_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                <span>����ϱ�</span>
                                            </a>
                                          </p>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ X, 08:00����(����2)
                                {
                                    echo "<p class='control'>
                                                <a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                    <span>����ϱ�</span>
                                                </a>
                                        </p>";
                                }
                                else if ($today_checktime1 == "" && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� ���üũ X, ���� ���üũ O, ���� ���üũ O, 08:00����(����2)
                                {
                                    echo "<p class='control'>
                                                <a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                    <span>����ϱ�</span>
                                                </a>
                                            </p>";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 == "" && $time_gubun == "before")	//���� ���üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ X, 08:00����(����4)
                                {
                                    echo "<p class='control'>
                                                <a href=javascript:leave_office(3,'". $yesterday_checktime1 ."','". $totaltime ."'); class='button' href='#'>
                                                    <span>����ϱ�</span>
                                                </a>
                                            </p>";
                                }
                                else if (($chk_gb1 == 1 || $chk_gb2 == 1) && $yesterday_checktime1 != "" && $yesterday_checktime2 != "" && $time_gubun == "before")	//���� ��� �ߺ�üũ - ���� �ް�, ���� ���üũ O, ���� ���üũ O, 08:00����(����4)
                                {
                                    echo "<p class='control'>
                                            <a href=javascript:leave_office(4,'". $yesterday_checktime1 ."','". $totaltime ."');class='button'>
                                                <span>����ϱ�</span>
                                            </a>
                                        </p>";
                                }
                                else if ($today_checktime1 != "" && $today_checktime2 == "")		//���üũ - ���� ���üũ O, ���� ���üũ X
                                {
                                    echo "<p class='control'>
                                             <a href=javascript:leave_office(1,'". $today_checktime1 ."','". $totaltime ."'); class='button' >
                                                 <span>����ϱ�</span>
                                             </a>
                                        </p>";
                                }
                            }else if ($today_gubun1 >= 10)
                            {
                                echo "<p class='control'>                                                                                          
                                        </p>";
                            }
                    ?>

                        <p class="control">
                            <a class="button is-primary" href="javascript:logout();">
                                <span>�α׾ƿ�</span>
                             </a>
                        </p>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </nav>




    <section class="section-fixed-margin"></section>

    <section class="hero is-link is-hidden-mobile">
        <div class="hero-body">
            <div class="container">
                <div class="columns is-vcentered">
                    <div class="column">
                        <a href="/main.php"><img src="/assets/images/df_logo_w.svg" width="120"></a>
                    </div>
                    <div class="column is-narrow">
                        <span class="is-size-6 is-italic">a difference that matters.</span>
                    </div>
                </div>

            </div>
        </div>
        <div class="hero-foot">
            <div class="container">
                <nav class="tabs is-boxed is-fullwidth">
                     <ul>
	                   <?	if ($prf_id == 7) {	?>
											<li<? if (substr(CURRENT_URL,0,5) == "/main") { ?> class="is-active"<? } ?>><a href="/main.php">Ȩ</a></li>
											<li<? if (substr(CURRENT_URL,0,10) == "/commuting") { ?> class="is-active"<? } ?>><a href="/commuting/commuting_list.php">������Ȳ</a></li>
											<li<? if (substr(CURRENT_URL,0,5) == "/book") { ?> class="is-active"<? } ?>><a href="/book/book_list.php?board=edit">���¼�����û</a></li>
											<li<? if (substr(CURRENT_URL,0,9) == "/approval") { ?> class="is-active"<? } ?>><a href="/approval/approval_my_list.php">���ڰ���</a></li>
											<li<? if (substr(CURRENT_URL,0,6) == "/board") { ?> class="is-active"<? } ?>><a href="/board/board_list.php">��������</a></li>
										<?	} else {	?>
											<li<? if (substr(CURRENT_URL,0,5) == "/main") { ?> class="is-active"<? } ?>><a href="/main.php">Ȩ</a></li>
											<li<? if (substr(CURRENT_URL,0,10) == "/commuting") { ?> class="is-active"<? } ?>><a href="/commuting/commuting_list.php">����</a></li>
											<li<? if (substr(CURRENT_URL,0,9) == "/vacation") { ?> class="is-active"<? } ?>><a href="/vacation/vacation_list.php">�ް�</a></li>
											<li<? if (substr(CURRENT_URL,0,9) == "/approval") { ?> class="is-active"<? } ?>><a href="/approval/approval_my_list.php">���ڰ���</a></li>
											<li<? if (substr(CURRENT_URL,0,8) == "/project") { ?> class="is-active"<? } ?>><a href="/project/project_list.php">������Ʈ</a></li>
											<!-- 2014.09.17 �ְ����� �߰�-->
											<li<? if (substr(CURRENT_URL,0,7) == "/weekly") { ?> class="is-active"<? } ?>><a href="/weekly/weekly_list.php">�ְ�����</a></li>
											<!-- 2014.09.17 �ְ����� ��-->
											<li<? if (substr(CURRENT_URL,0,6) == "/board") { ?> class="is-active"<? } ?>><a href="/board/board_list.php">��������</a></li>
											<li<? if (substr(CURRENT_URL,0,6) == "/book/") { ?> class="is-active"<? } ?>><a href="/book/book_list.php">�Խ���</a></li>
											<li<? if (substr(CURRENT_URL,0,8) == "/booking" || substr(CURRENT_URL,0,6) == "/visit") { ?> class="is-active"<? } ?>><a href="/booking/booking_list.php">����</a></li>
											<li<? if (substr(CURRENT_URL,0,5) == "/org_") { ?> class="is-active"<? } ?>><a href="/org_chart/person_list.php">������</a></li>
										<?	}	?>
                	  </ul>
                </nav>
         </div>
        </div>
    </section>

    <section class="hero is-link is-hidden-tablet">
        <div class="hero-body">
            <div class="container">
                <div class="level is-mobile">
                    <div class="level-item level-left">
                        <a href="/main.php"><img src="/assets/images/df_logo_w.svg" width="50" style="vertical-align:middle;"></a>
                    </div>
                    <div class="level-item level-right is-expanded">
                        <span class="is-italic has-text-right is-fullwidth">a difference that matters.</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</div>